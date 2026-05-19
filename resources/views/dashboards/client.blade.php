@php
    $client = Auth::user();
@endphp

<x-dashboard-layout title="Client Dashboard" :hide-top-actions="true">
    <x-slot name="nav">
        <ul class="nav nav-pills flex-row">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('client.dashboard') }}">
                    <i class="bi bi-house me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person me-2"></i>Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('client.schedules') }}">
                    <i class="bi bi-calendar3 me-2"></i>Schedules
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('client.invoices') }}">
                    <i class="bi bi-receipt me-2"></i>Invoices
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('client.chats') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chats
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('client.support') }}">
                    <i class="bi bi-headset me-2"></i>Support/Help
                </a>
            </li>
        </ul>
    </x-slot>

    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Client</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
    </x-slot>

    <x-slot name="notificationCount">{{ $client->unread_notifications_count ?? 1 }}</x-slot>

    <style>
        :root {
            --primary-color: #055c5c;
            --secondary-color: #640404;
            --white-color: #ffffff;
            --light-bg: #f8f9fa;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        /* Header Section */
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        /* Welcome Section - Enhanced from contractor */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), #087272);
            color: var(--white-color);
            padding: 3rem 2.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(5, 92, 92, 0.2);
        }

        .welcome-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .date-display {
            display: flex;
            gap: 2rem;
            justify-content: flex-end;
        }

        .date-item {
            text-align: center;
        }

        .date-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .date-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Statistics Grid - Direct from contractor */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: var(--white-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .stat-icon.primary { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }
        .stat-icon.success { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }
        .stat-icon.warning { background: rgba(217, 119, 6, 0.1); color: #d97706; }
        .stat-icon.info { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
            color: var(--text-dark);
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Content Sections */
        .content-section {
            background: var(--white-color);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-bg);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Tabs from contractor */
        .tab-buttons {
            display: flex;
            gap: 0.5rem;
            background: var(--light-bg);
            padding: 0.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .tab-btn:not(.active):hover {
            background: rgba(5, 92, 92, 0.1);
            color: var(--primary-color);
        }

        /* Table Styling from contractor */
        .table-container {
            background: transparent;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead th {
            background: var(--primary-color);
            color: var(--white-color);
            border: none;
            padding: 1.25rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge.primary { background: var(--primary-color); color: white; }
        .badge.success { background: var(--primary-color); color: white; }
        .badge.warning { background: #d97706; color: white; }

        /* Progress Bars */
        .progress {
            height: 8px;
            background: var(--light-bg);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .progress-bar.success { background: var(--primary-color); }
        .progress-bar.warning { background: #d97706; }

        /* Buttons from contractor */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: #044a4a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(5, 92, 92, 0.3);
            color: white !important;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white !important;
        }

        /* Activity Feed from contractor */
        .activity-feed {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: var(--white-color);
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }

        .activity-icon.success { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }
        .activity-icon.primary { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }
        .activity-icon.warning { background: rgba(217, 119, 6, 0.1); color: #d97706; }
        .activity-icon.info { background: rgba(5, 92, 92, 0.1); color: var(--primary-color); }

        /* Responsive from contractor */
        @media (max-width: 1200px) {
            .activity-feed {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .dashboard-container {
                padding: 1.5rem;
            }

            .welcome-section {
                padding: 2rem 1.5rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .content-section {
                padding: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .tab-buttons {
                width: 100%;
                justify-content: center;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }
    </style>
        <!-- Welcome Section -->
        <div class="welcome-section">
                <h1 class="welcome-title">Welcome back, {{ $client->name ?? 'Client' }}!</h1>
                <p class="welcome-subtitle">Here's what's happening with your waste collection services today.</p>
                <div class="date-display" style="justify-content: flex-end">
                    <div class="date-item">
                        <div id="dashboard-day" class="date-value">{{ date('d') }}</div>
                        <div id="dashboard-month-year" class="date-label">{{ date('M Y') }}</div>
                    </div>
                    <div class="date-item">
                        <div id="dashboard-weekday" class="date-value">{{ date('l') }}</div>
                        <div id="dashboard-time" class="date-label">{{ date('H:i A') }}</div>
                    </div>
                </div>
        </div>

        <script>
            function updateClientDashboardClock() {
                const now = new Date();
                const dayElement = document.getElementById('dashboard-day');
                const monthYearElement = document.getElementById('dashboard-month-year');
                const weekdayElement = document.getElementById('dashboard-weekday');
                const timeElement = document.getElementById('dashboard-time');

                if (!dayElement || !monthYearElement || !weekdayElement || !timeElement) {
                    return;
                }

                const options = { month: 'short', year: 'numeric' };
                const locale = navigator.language || 'en-US';

                const day = String(now.getDate()).padStart(2, '0');
                const monthYear = now.toLocaleDateString(locale, options);
                const weekday = now.toLocaleDateString(locale, { weekday: 'long' });
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                    const timeString = `${hours}:${minutes} ${ampm}`;

                dayElement.textContent = day;
                monthYearElement.textContent = monthYear;
                weekdayElement.textContent = weekday;
                timeElement.textContent = timeString;
            }

            document.addEventListener('DOMContentLoaded', function () {
                updateClientDashboardClock();
                setInterval(updateClientDashboardClock, 1000);
            });
        </script>

        <!-- Stats Grid from contractor, adapted for client data -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $completedPickups }}</div>
                    <div class="stat-label">Completed Pickups</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $upcomingScheduleCount }}</div>
                    <div class="stat-label">Upcoming Schedules</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="bi bi-coin"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">TZS {{ number_format($accountAmount, 0) }}</div>
                    <div class="stat-label">Amount in Customer Account</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">TZS {{ number_format($totalPending, 0) }}</div>
                    <div class="stat-label">Pending Amount</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tab-buttons">
            <a href="{{ route('client.dashboard') }}" class="tab-btn active">
                <i class="bi bi-speedometer2"></i>Overview
            </a>
        </div>

        <div class="content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="bi bi-list-ul"></i>Your Services Overview
                        </h2>
                        <div>
                            <a href="{{ route('client.request.service') }}" class="btn-outline-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Request Service
                            </a>
                        </div>
                    </div>

                    <!-- Client Schedules Table from contractor style -->
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingSchedules as $schedule)
                                    @php
                                        $status = strtolower($schedule->status ?? 'scheduled');
                                        $statusClass = $status === 'pending' ? 'warning' : ($status === 'scheduled' ? 'success' : 'primary');
                                        $progress = $status === 'pending' ? 30 : ($status === 'scheduled' ? 65 : 100);
                                        $label = ucfirst($status);
                                        $serviceName = $schedule->service_type ? str_replace('_', ' ', ucfirst($schedule->service_type)) : 'Waste Pickup';
                                        $pickupLocation = $schedule->pickup_location ?: 'Scheduled pickup';
                                        $pickupDate = optional($schedule->pickup_date)->format('D, M d');
                                        $pickupTime = $schedule->pickup_time ? (is_string($schedule->pickup_time) ? $schedule->pickup_time : $schedule->pickup_time->format('H:i')) : '';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-{{ $statusClass }} bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-truck text-{{ $statusClass }} fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $serviceName }}</div>
                                                    <small class="text-muted">{{ $pickupLocation }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $pickupDate }}{{ $pickupTime ? ' • ' . $pickupTime : '' }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $label }}</span></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar {{ $statusClass }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $progress }}% Complete</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('client.schedules') }}" class="btn-outline-primary btn-sm" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No upcoming service requests yet. Use the request service button to schedule a pickup.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Animations from contractor
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat items on hover
            const statItems = document.querySelectorAll('.stat-item');
            statItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Animate table rows
            const tableRows = document.querySelectorAll('.table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</x-dashboard-layout>

