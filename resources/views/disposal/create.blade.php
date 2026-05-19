<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Disposal Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #055c5c;
            --white-color: #ffffff;
            --light-bg: #f8f9fa;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
        }
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            padding: 2rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .table-section {
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
        }
        .table thead th {
            background: var(--primary-color);
            color: var(--white-color);
            border: none;
            padding: 1.25rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .badge.bg-primary { background: var(--primary-color) !important; }
        .empty-state {
            padding: 2rem;
            text-align: center;
            border: 1px dashed var(--primary-color);
            border-radius: 12px;
            background: rgba(5, 92, 92, 0.05);
        }
        .btn-success {
            background-color: #10b981;
            border-color: #10b981;
            color: white;
        }
        .btn-success:hover {
            background-color: #0f9f72;
            border-color: #0f9f72;
        }
        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Create Disposal Schedule</h1>
                <p class="text-muted">Choose a completed collection and record disposal details for it.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.contractor') }}" class="btn btn-outline-dark d-flex align-items-center gap-2" style="border-color: #cbd5e1;" target="_parent">
                    <i class="bi bi-house-door-fill" style="color: var(--primary-color);"></i> Home
                </a>
                <a href="{{ route('disposal.index') }}" class="btn btn-success d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Disposal List
                </a>
            </div>
        </div>

        <div class="table-section">
            <div class="section-header">
                <h2 class="section-title">Pending Disposal Records</h2>
            </div>

            @if($pendingSchedules->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Client</th>
                                <th>Pickup Location</th>
                                <th>Pickup Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSchedules as $schedule)
                            <tr>
                                <td><span class="badge bg-primary">{{ $schedule->route ?? 'N/A' }}</span></td>
                                <td>{{ optional($schedule->client)->name ?? 'Unknown' }}</td>
                                <td>{{ $schedule->pickup_location }}</td>
                                <td>{{ $schedule->pickup_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <a href="{{ route('disposal.edit', $schedule) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i>Record Data
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container mt-4">
                    {{ $pendingSchedules->links() }}
                </div>
            @else
                <div class="empty-state">
                    <h4 class="mb-3">No pending disposal records found</h4>
                    <p class="mb-3">Mark a collection schedule as completed to add a disposal schedule, or go back to the disposal list.</p>
                    <a href="{{ route('disposal.index') }}" class="btn btn-success">
                        <i class="bi bi-arrow-left-circle me-2"></i>Back to Disposal Schedules
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
