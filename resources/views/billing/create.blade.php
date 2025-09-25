<x-guest-layout>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h5 class="text-success mb-0">Create Invoice</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('billing.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client *</label>
                            <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="service_type" class="form-label">Service Type *</label>
                            <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                <option value="">Select Service</option>
                                <option value="waste_collection" {{ old('service_type') == 'waste_collection' ? 'selected' : '' }}>Waste Collection</option>
                                <option value="disposal" {{ old('service_type') == 'disposal' ? 'selected' : '' }}>Waste Disposal</option>
                                <option value="recycling" {{ old('service_type') == 'recycling' ? 'selected' : '' }}>Recycling</option>
                                <option value="consultation" {{ old('service_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                            </select>
                            @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="subtotal" class="form-label">Subtotal ($) *</label>
                            <input type="number" class="form-control @error('subtotal') is-invalid @enderror" id="subtotal" name="subtotal" step="0.01" value="{{ old('subtotal') }}" required>
                            @error('subtotal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" step="0.01" value="{{ old('tax_rate', 0) }}">
                            @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="due_date" class="form-label">Due Date *</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Create Invoice
                        </button>
                        <a href="{{ route('billing.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>