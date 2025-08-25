@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">

        {{-- Country-wise Users Pie Chart --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <canvas id="countryPieChart" width="1500" height="350" style="width:350px; height:250px;"></canvas>
            </div>
        </div>


        {{-- Users Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">User Overview</h5>
                <div class="row g-4">
                    @php
                        $users = [
                            ['label' => 'Total Users', 'value' => $dashboardData['totalUser'], 'icon' => 'fas fa-user', 'bg' => 'bg-success'],
                            ['label' => 'Active Users', 'value' => $dashboardData['activeUser'], 'icon' => 'fas fa-users-cog', 'bg' => 'bg-warning'],
                            ['label' => 'Blocked Users', 'value' => $dashboardData['blockUser'], 'icon' => 'fas fa-user-slash', 'bg' => 'bg-danger'],
                            ['label' => 'New Users', 'value' => $dashboardData['newUser'], 'icon' => 'fas fa-user-plus', 'bg' => 'bg-primary'],
                        ];
                    @endphp

                    @foreach ($users as $user)
                        <div class="col-md-3">
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
                                <a href="/users"><i class="fas fa-arrow-right text-muted"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Deposits Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Deposits</h5>
                <div class="row g-4">
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['totalDeposits'], 2) }}" label="Total Deposits" bg="success" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['todayDeposits'], 2) }}" label="Today Deposits" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last7DaysDeposits'], 2) }}" label="Last 7 Days Deposits" bg="info" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last30DaysDeposits'], 2) }}" label="Last 30 days Deposits" bg="secondary" />
                </div>
            </div>
        </div>

        {{-- Withdrawals Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Withdrawals</h5>
                <div class="row g-4">
                    <x-dashboard.stat-card icon="fas fa-credit-card" value="${{$dashboardData['totalWithdrawals']}}" label="Total Withdrawn" bg="success" />
                    <x-dashboard.stat-card icon="fas fa-credit-card" value="${{$dashboardData['todayWithdrawals']}}" label="Today Withdrawals" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-credit-card" value="${{$dashboardData['last30DaysWithdrawals']}}" label="Last 30 days Withdrawals" bg="info" />
                    <x-dashboard.stat-card icon="fas fa-percent" value="${{number_format($dashboardData['withdrawChargeAmount'], 2)}}" label="Total Withdrawal Charge" bg="secondary" />
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('countryPieChart').getContext('2d');

            const countries = ["Bangladesh","united of america","US"];
            const countryCounts = [1, 4, 1];

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

