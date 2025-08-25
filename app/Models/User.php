<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory,HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'deposit_wallet',
        'profit_wallet',
        'refer_by',
        'refer_code',
        'is_active',
        'is_block',
        'password',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refer_by');
    }


    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'refer_by');
    }

    public function totalTeamMembersCount(int $level = 1): int
    {
        $count = $this->referrals()->count();

        foreach ($this->referrals as $referral) {
            $count += $referral->totalTeamMembersCount($level + 1);
        }

        return $count;
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->refer_code = self::generateReferCode();
        });
    }

    public static function generateReferCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (self::where('refer_code', $code)->exists());

        return $code;
    }


    public function investors()
    {
        return $this->hasMany(Investor::class, 'user_id');
    }

    public function totalLevel2InvestmentAmount(): float
    {
        $level1Ids = $this->referrals()->pluck('id');

        return (float) Investor::whereHas('user', function ($query) use ($level1Ids) {
            $query->whereIn('refer_by', $level1Ids);
        })
            ->sum('investment');
    }

    public function teamDataByLevel(int $level = 1, array &$result = [])
    {
        $referrals = $this->referrals()->with('investors')->get();

        $totalUsers = $referrals->count();
        $totalInvestment = 0;

        foreach ($referrals as $user) {
            $totalInvestment += $user->investors->sum('investment');
        }

        if (!isset($result["Level{$level}"])) {
            $result["Level{$level}"] = [
                'total' => 0,
                'totalInvestment' => 0,
            ];
        }

        $result["Level{$level}"]['total'] += $totalUsers;
        $result["Level{$level}"]['totalInvestment'] += $totalInvestment;

        foreach ($referrals as $referral) {
            $referral->teamDataByLevel($level + 1, $result);
        }

        return $result;
    }


    public function totalLevel1InvestmentAmount(): float
    {
        return $this->referrals()
            ->with('investors')
            ->get()
            ->reduce(function ($carry, $user) {
                $sum = $user->investors->sum('investment');
                return $carry + (float) $sum;
            }, 0);
    }


    public function countLevel2Users(): int
    {
        $level1Referrals = $this->referrals()->pluck('id');

        return self::whereIn('refer_by', $level1Referrals)->count();
    }


    public function investor()
    {
        return $this->hasOne(Investor::class, 'user_id');
    }

    public function totalTeamInvestment(): float
    {
        $total = 0;

        // Direct referrals (level-1) সহ investors relation আনা হলো
        $referrals = $this->referrals()->with('investors')->get();

        foreach ($referrals as $referral) {
            // ওই referral এর নিজের মোট investment
            $investment = $referral->investors->sum('investment');

            // যোগ করা হলো
            $total += $investment;

            // ওই referral এর নিচের লেভেলগুলো (recursive)
            $total += $referral->totalTeamInvestment();
        }

        return $total;
    }


}


