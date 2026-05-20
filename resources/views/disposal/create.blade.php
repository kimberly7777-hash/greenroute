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
            --secondary-color: #10b981;
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
        .form-card,
        .table-section {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
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
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(5, 92, 92, 0.15);
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #044a4a;
            border-color: #044a4a;
            color: white;
        }
        .btn-secondary-custom {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        .btn-success {
            background: var(--primary-color) !important;
            border: none !important;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(5, 92, 92, 0.3);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-success:hover {
            background: #044a4a !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(5, 92, 92, 0.4);
        }
        .btn-secondary-custom:hover {
            background: rgba(5, 92, 92, 0.08);
            color: var(--primary-color);
        }
        .alert-custom {
            background: rgba(16, 185, 129, 0.12);
            border-color: #10b981;
            color: #134e4a;
        }
        @media (max-width: 992px) {
            .container {
                padding: 1.5rem;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            .form-card,
            .table-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Create Disposal Schedule</h1>
                <p class="text-muted">Add disposal details for completed collections using a form styled like collection schedule creation.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.contractor') }}" class="btn btn-outline-dark d-flex align-items-center gap-2" style="border-color: #cbd5e1;" target="_parent">
                    <i class="bi bi-house-door-fill" style="color: var(--primary-color);"></i> Home
                </a>
                <a href="{{ route('disposal.index') }}" class="btn btn-success d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul me-2"></i>Disposal List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-custom mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-card">
            <div class="section-header">
                <h2 class="section-title">Disposal Schedule Form</h2>
            </div>

            @if($pendingSchedules->count())
                <form method="POST" action="{{ route('disposal.store') }}">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="schedule_id">Completed Collection *</label>
                            <select id="schedule_id" name="schedule_id" class="form-select @error('schedule_id') is-invalid @enderror" required>
                                <option value="">Choose a completed collection</option>
                                @foreach($pendingSchedules as $schedule)
                                    <option value="{{ $schedule->id }}" {{ old('schedule_id') == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->pickup_date->format('M d, Y') }} — {{ $schedule->pickup_location }} | {{ optional($schedule->client)->name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('schedule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="service_type">Service Type *</label>
                            <select id="service_type" name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                                <option value="disposal" {{ old('service_type', 'disposal') == 'disposal' ? 'selected' : '' }}>Disposal</option>
                                <option value="collection" {{ old('service_type') == 'collection' ? 'selected' : '' }}>Collection</option>
                                <option value="both" {{ old('service_type') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('service_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="estimated_duration">Estimated Duration (hours)</label>
                            <input type="number" step="0.5" min="0" id="estimated_duration" name="estimated_duration" class="form-control @error('estimated_duration') is-invalid @enderror" value="{{ old('estimated_duration') }}" placeholder="e.g., 2.5">
                            @error('estimated_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="total_volume">Total Volume (cubic yards) *</label>
                            <input type="number" step="0.1" min="0" id="total_volume" name="total_volume" class="form-control @error('total_volume') is-invalid @enderror" value="{{ old('total_volume') }}" placeholder="e.g., 15.0" required>
                            @error('total_volume')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="disposal_type">Disposal Type *</label>
                            <select id="disposal_type" name="disposal_type" class="form-select @error('disposal_type') is-invalid @enderror" required>
                                <option value="">Select Disposal Type</option>
                                <option value="sorting_facility" {{ old('disposal_type') == 'sorting_facility' ? 'selected' : '' }}>Sorting Facility</option>
                                <option value="landfill" {{ old('disposal_type') == 'landfill' ? 'selected' : '' }}>Landfill</option>
                            </select>
                            @error('disposal_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="disposal_site">Disposal Site *</label>
                            <input type="text" id="disposal_site" name="disposal_site" class="form-control @error('disposal_site') is-invalid @enderror" value="{{ old('disposal_site') }}" placeholder="e.g., Landfill A, Recycling Center" required>
                            @error('disposal_site')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="disposal_notes">Notes</label>
                        <textarea id="disposal_notes" name="disposal_notes" rows="4" class="form-control @error('disposal_notes') is-invalid @enderror" placeholder="Special instructions or additional information">{{ old('disposal_notes') }}</textarea>
                        @error('disposal_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-3 pt-3 border-top">
                        <a href="{{ route('disposal.index') }}" class="btn btn-secondary-custom">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-circle me-1"></i> Create Disposal Schedule
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    No completed collections are available yet for disposal recording. Complete a collection first or return to the disposal schedule list.
                </div>
            @endif
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSchedules as $schedule)
                                <tr>
                                    <td><strong>{{ $schedule->route ?? 'N/A' }}</strong></td>
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

                <div class="mt-4">
                    {{ $pendingSchedules->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <p class="mb-0">There are no pending disposal records to display.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
