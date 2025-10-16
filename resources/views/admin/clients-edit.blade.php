<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client - AFIA ORBIT Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-teal: #055c5c;
            --primary-red: #640404;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .back-link {
            margin-bottom: 1.5rem;
        }
        
        .back-link a {
            color: var(--primary-teal);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-teal);
            margin-bottom: 0.5rem;
        }
        
        .page-description {
            color: #666;
        }
        
        .client-id {
            background: #e6f2f2;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            text-align: right;
        }
        
        .client-id strong {
            color: var(--primary-teal);
            display: block;
            font-size: 0.875rem;
        }
        
        .client-id span {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            font-size: 1.2rem;
            color: var(--primary-teal);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-teal);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-group label .required {
            color: #ef4444;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-teal);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .btn-submit {
            background: var(--primary-teal);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-right: 1rem;
        }
        
        .btn-submit:hover {
            background: #044a4a;
        }
        
        .btn-cancel {
            background: #6b7280;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background: #4b5563;
            color: white;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .help-text {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="back-link">
            <a href="{{ route('admin.clients') }}">
                <i class="bi bi-arrow-left me-2"></i>Back to Clients List
            </a>
        </div>
        
        <div class="page-header">
            <div>
                <h1 class="page-title">Edit Client</h1>
                <p class="page-description">Update client information</p>
            </div>
            <div class="client-id">
                <strong>Registration Number</strong>
                <span>{{ $client->registration_number }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="form-section">
                    <h3><i class="bi bi-person me-2"></i>Basic Information</h3>
                    
                    <div class="form-group">
                        <label>Client Name <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                            <div class="help-text">Optional - for notifications</div>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Phone Number <span class="required">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}" required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Category <span class="required">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="residential" {{ old('category', $client->category) == 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ old('category', $client->category) == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            </select>
                            @error('category')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Status <span class="required">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Assign to Contractor</label>
                        <select name="contractor_id" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach($contractors as $contractor)
                                <option value="{{ $contractor->id }}" {{ old('contractor_id', $client->contractor_id) == $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('contractor_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address Information -->
                <div class="form-section">
                    <h3><i class="bi bi-geo-alt me-2"></i>Address Information</h3>
                    
                    <div class="form-group">
                        <label>Street Address <span class="required">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $client->address) }}" required>
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City <span class="required">*</span></label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $client->city) }}" required>
                            @error('city')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>State <span class="required">*</span></label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $client->state) }}" required>
                            @error('state')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>ZIP Code <span class="required">*</span></label>
                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $client->zip_code) }}" required>
                            @error('zip_code')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Service Frequency</label>
                            <select name="service_frequency" class="form-control">
                                <option value="">Select Frequency</option>
                                <option value="daily" {{ old('service_frequency', $client->service_frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('service_frequency', $client->service_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="bi-weekly" {{ old('service_frequency', $client->service_frequency) == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                                <option value="monthly" {{ old('service_frequency', $client->service_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('service_frequency')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- GPS Coordinates (Optional) -->
                <div class="form-section">
                    <h3><i class="bi bi-pin-map me-2"></i>GPS Coordinates (Optional)</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $client->latitude) }}">
                            <div class="help-text">Example: 5.6037</div>
                            @error('latitude')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $client->longitude) }}">
                            <div class="help-text">Example: -0.1870</div>
                            @error('longitude')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="form-section">
                    <h3><i class="bi bi-journal-text me-2"></i>Additional Notes</h3>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $client->notes) }}</textarea>
                        <div class="help-text">Any special instructions or information about this client</div>
                        @error('notes')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-section">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle me-2"></i>Update Client
                    </button>
                    <a href="{{ route('admin.clients') }}" class="btn-cancel">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
