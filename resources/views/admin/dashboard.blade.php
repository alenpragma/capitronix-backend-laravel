@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">

        {{-- Pending Withdrawals Alert --}}
        @if($dashboardData['pendingWithdrawals'] > 0)
            <a href="/withdraw?filter=pending" class="text-decoration-none">
                <div class="alert alert-warning d-flex align-items-center shadow-sm rounded p-3 mb-4">
                    <i class="fas fa-exclamation-triangle text-dark fs-4 me-3"></i>
                    <div class="fw-semibold text-dark">
                        You currently have {{ $dashboardData['pendingWithdrawals'] }} pending withdrawal {{ $dashboardData['pendingWithdrawals'] > 1 ? 'requests' : 'request' }}.
                    </div>
                </div>
            </a>
        @endif


        {{-- Country-wise Users Pie Chart --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Users by Country</h5>
                <div class="d-flex justify-content-center">
                    <canvas id="countryPieChart" style="max-width:100%; height:300px;"></canvas>
                </div>
            </div>
        </div>


        {{-- Users Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">User Overview</h5>
                <div class="row g-4">
                    @php
                        $users = [
                            ['label' => 'Total Users', 'value' => $dashboardData['totalUser'], 'icon' => 'fas fa-user', 'bg' => 'bg-success', 'filter' => 'all'],
                            ['label' => 'Active Users', 'value' => $dashboardData['activeUser'], 'icon' => 'fas fa-users-cog', 'bg' => 'bg-warning', 'filter' => 'active'],
                            ['label' => 'Blocked Users', 'value' => $dashboardData['blockUser'], 'icon' => 'fas fa-user-slash', 'bg' => 'bg-danger', 'filter' => 'blocked'],
                            ['label' => 'New Users', 'value' => $dashboardData['newUser'], 'icon' => 'fas fa-user-plus', 'bg' => 'bg-primary', 'filter' => 'new'],
                        ];
                    @endphp

                    @foreach ($users as $user)
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.index', ['filter' => $user['filter']]) }}" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $user['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $user['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">{{ $user['value'] }}</div>
                                            <small class="text-muted">{{ $user['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- Deposit Wallet Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Deposit Wallet Report</h5>
                <div class="row g-4">
                    @php
                        $deposits = [
                            ['label' => 'Total Deposits', 'value' => number_format($dashboardData['totalDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-success'],
                            ['label' => 'Today Deposits', 'value' => number_format($dashboardData['todayDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-warning'],
                            ['label' => 'Total Auto Deposits', 'value' => number_format($dashboardData['autoDeposits'], 2), 'icon' => 'fas fa-robot', 'bg' => 'bg-secondary'],
                            ['label' => 'Total Manual Deposits', 'value' => number_format($dashboardData['manualDeposits'], 2), 'icon' => 'fas fa-hand-paper', 'bg' => 'bg-primary'],
                            ['label' => 'Last 7 Days Deposits', 'value' => number_format($dashboardData['last7DaysDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-info'],
                            ['label' => 'Last 30 Days Deposits', 'value' => number_format($dashboardData['last30DaysDeposits'], 2), 'icon' => 'fas fa-calendar-alt', 'bg' => 'bg-secondary'],
                        ];
                    @endphp

                    @foreach ($deposits as $deposit)
                        <div class="col-md-6">
                            <a href="{{ route('deposit.index') }}" class="text-decoration-none text-black">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $deposit['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $deposit['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${{ $deposit['value'] }}</div>
                                            <small class="text-muted">{{ $deposit['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- Active Wallet Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Active Wallet Report</h5>
                <div class="row g-4">
                    @php
                        $activeWallets = [
                            ['label' => 'Total Active Wallet', 'value' => number_format($dashboardData['totalActiveDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-success'],
                            ['label' => 'Today Active Wallet', 'value' => number_format($dashboardData['todayActiveDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-warning'],
                            ['label' => 'Total Auto Active Deposit', 'value' => number_format($dashboardData['autoActiveDeposits'], 2), 'icon' => 'fas fa-robot', 'bg' => 'bg-primary'],
                            ['label' => 'Total Manual Active Deposit', 'value' => number_format($dashboardData['manualActiveDeposits'], 2), 'icon' => 'fas fa-hand-paper', 'bg' => 'bg-secondary'],
                            ['label' => 'Last 7 Days Active Wallet', 'value' => number_format($dashboardData['last7DaysActiveDeposits'], 2), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-info'],
                            ['label' => 'Last 30 Days Active Wallet', 'value' => number_format($dashboardData['last30DaysActiveDeposits'], 2), 'icon' => 'fas fa-calendar-alt', 'bg' => 'bg-danger'],
                        ];
                    @endphp

                    @foreach ($activeWallets as $wallet)
                        <div class="col-md-6">
                            <a href="{{ route('deposit.index') }}" class="text-decoration-none text-black">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $wallet['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $wallet['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${{ $wallet['value'] }}</div>
                                            <small class="text-muted">{{ $wallet['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- Withdrawals Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Withdrawals</h5>
                <div class="row g-4">
                    @php
                        $withdrawals = [
                            ['label' => 'Total Withdrawn', 'value' => number_format($dashboardData['totalWithdrawals'], 2), 'icon' => 'fas fa-credit-card', 'bg' => 'bg-success'],
                            ['label' => 'Today Withdrawals', 'value' => number_format($dashboardData['todayWithdrawals'], 2), 'icon' => 'fas fa-credit-card', 'bg' => 'bg-warning'],
                            ['label' => 'Last 30 Days Withdrawals', 'value' => number_format($dashboardData['last30DaysWithdrawals'], 2), 'icon' => 'fas fa-credit-card', 'bg' => 'bg-info'],
                            ['label' => 'Total Withdrawal Charge', 'value' => number_format($dashboardData['withdrawChargeAmount'], 2), 'icon' => 'fas fa-percent', 'bg' => 'bg-secondary'],
                        ];
                    @endphp

                    @foreach ($withdrawals as $withdraw)
                        <div class="col-md-6">
                            <a href="{{ route('withdraw.index') }}" class="text-decoration-none text-black">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $withdraw['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $withdraw['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${{ $withdraw['value'] }}</div>
                                            <small class="text-muted">{{ $withdraw['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Code Details Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Code Details</h5>
                <div class="row g-4">
                    @php
                        $codes = [
                            ['label' => 'Code Purchase', 'value' => number_format($dashboardData['totalPurchased'], 2), 'icon' => 'fas fa-dollar-sign', 'bg' => 'bg-success'],
                            ['label' => 'Total Code', 'value' => number_format($dashboardData['totalCodes']), 'icon' => 'fas fa-qrcode', 'bg' => 'bg-primary'],
                            ['label' => 'Used Code', 'value' => number_format($dashboardData['usedCodes']), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-info'],
                            ['label' => 'Unused Code', 'value' => number_format($dashboardData['unusedCodes']), 'icon' => 'fas fa-clock', 'bg' => 'bg-warning'],
                        ];
                    @endphp

                    @foreach ($codes as $code)
                        <div class="col-md-6">
                            <a href="{{ route('codes.index') }}" class="text-decoration-none text-black">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $code['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $code['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${{ $code['value'] }}</div>
                                            <small class="text-muted">{{ $code['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

{{-- Investment Details Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Investment Details</h5>
                <div class="row g-4">
                    @php
                        $investments = [
                            ['label' => 'Total Investment', 'value' => number_format($dashboardData['totalInvestmentAmount'], 2), 'icon' => 'fas fa-coins', 'bg' => 'bg-primary'],
                            ['label' => 'Running Investment', 'value' => number_format($dashboardData['runningInvestmentAmount'], 2), 'icon' => 'fas fa-play-circle', 'bg' => 'bg-success'],
                            ['label' => 'Canceled Investment', 'value' => number_format($dashboardData['canceledInvestmentAmount'], 2), 'icon' => 'fas fa-ban', 'bg' => 'bg-danger'],
                            ['label' => 'Expired Investment', 'value' => number_format($dashboardData['expiredInvestmentAmount'], 2), 'icon' => 'fas fa-hourglass-end', 'bg' => 'bg-warning'],
                        ];
                    @endphp

                    @foreach ($investments as $investment)
                        <div class="col-md-6">
                            <a href="{{ route('admin.investments') }}" class="text-decoration-none text-black">
                                <div class="d-flex justify-content-between align-items-center border rounded p-3 h-100 bg-light hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box {{ $investment['bg'] }} bg-opacity-75 text-white rounded d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                            <i class="{{ $investment['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${{ $investment['value'] }}</div>
                                            <small class="text-muted">{{ $investment['label'] }}</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('countryPieChart').getContext('2d');

            const countries = @json($countries);
            const countryCounts = @json($countryCounts);

            // Generate random colors dynamically in JS
            const backgroundColors = countries.map(() => '#' + Math.floor(Math.random()*16777215).toString(16));

            const countryPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: countries,
                    datasets: [{
                        label: 'Users by Country',
                        data: countryCounts,
                        backgroundColor: backgroundColors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right', labels: { usePointStyle: true } }
                    }
                }
            });
        });
    </script>
@endsection

