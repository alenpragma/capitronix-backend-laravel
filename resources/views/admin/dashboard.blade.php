@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">

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

        {{-- Deposit Wallet Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Deposit Wallet Report</h5>
                <div class="row g-4">
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['totalDeposits'], 2) }}" label="Total Deposits" bg="success" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['todayDeposits'], 2) }}" label="Today Deposits" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['autoDeposits'], 2) }}" label="Total Auto Deposits" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['manualDeposits'], 2) }}" label="Total Manual Deposits" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last7DaysDeposits'], 2) }}" label="Last 7 Days Deposits" bg="info" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last30DaysDeposits'], 2) }}" label="Last 30 days Deposits" bg="secondary" />
                </div>
            </div>
        </div>

                {{-- Active Wallet Section --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Active Wallet Report</h5>
                <div class="row g-4">
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['totalActiveDeposits'], 2) }}" label="Total Active Wallet" bg="success" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['todayActiveDeposits'], 2) }}" label="Today Active Wallet" bg="warning" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['autoActiveDeposits'], 2) }}" label="Total Auto Active Deposit" bg="primary" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['manualActiveDeposits'], 2) }}" label="Total Manual Active Deposit" bg="secondary" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last7DaysActiveDeposits'], 2) }}" label="Last 7 Days Active Wallet" bg="info" />
                    <x-dashboard.stat-card icon="fas fa-hand-holding-usd" value="${{ number_format($dashboardData['last30DaysActiveDeposits'], 2) }}" label="Last 30 days Active Wallet" bg="danger" />
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

                    {{-- Code Section --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-4">Code Details</h5>
            <div class="row g-4">
                <x-dashboard.stat-card icon="fas fa-dollar-sign" value="${{ number_format($dashboardData['totalPurchased'], 2) }}" label="Code Purchase" bg="success"/>

                <x-dashboard.stat-card icon="fas fa-qrcode" value="{{ number_format($dashboardData['totalCodes']) }}" label="Total Code" bg="primary" />

                <x-dashboard.stat-card icon="fas fa-check-circle" value="{{ number_format($dashboardData['usedCodes']) }}" label="Used Code" bg="info" />

                <x-dashboard.stat-card icon="fas fa-clock" value="{{ number_format($dashboardData['unusedCodes']) }}" label="Unused Code" bg="warning" />
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

