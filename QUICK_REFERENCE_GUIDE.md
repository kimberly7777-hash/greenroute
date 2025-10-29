# AFIA-ORBIT System - Quick Reference Guide

## 🚀 Quick Start

This guide provides quick reference for all major features and improvements in the system.

---

## 📍 Location Features

### Cascading Dropdowns
```javascript
// Load regions
GET /api/locations/regions

// Load districts (when region selected)
GET /api/locations/districts?region=ARUSHA

// Load wards (when district selected)
GET /api/locations/wards?region=ARUSHA&district=ARUSHA%20CBD

// Load streets (when ward selected)
GET /api/locations/streets?region=ARUSHA&district=ARUSHA%20CBD&ward=SEKEI
```

### Autocomplete (NEW!)
```javascript
// Fast autocomplete for any location type
GET /api/locations/autocomplete?q=ARU&type=region&limit=10

// Types: region, district, ward, street, all
```

### Search
```javascript
// Search all locations
GET /api/locations/search?q=SEKEI&limit=20
```

---

## 💰 Invoice Features

### Get Clients by Location
```bash
POST /api/invoices/clients-by-location
{
  "contractor_registration_number": "CONT123456",
  "region": "ARUSHA",
  "district": "ARUSHA CBD",
  "ward": "SEKEI",
  "street": "SANAWARI"
}
```

### Create Bulk Invoices
```bash
POST /api/invoices/bulk-create
{
  "contractor_registration_number": "CONT123456",
  "client_ids": [1, 2, 3, 4, 5],
  "site_location": {
    "region": "ARUSHA",
    "district": "ARUSHA CBD",
    "ward": "SEKEI",
    "street": "SANAWARI"
  },
  "invoice_date": "2025-10-28",
  "due_date": "2025-11-28",
  "service_type": "Waste Collection - October 2025",
  "description": "Monthly waste collection service",
  "subtotal": 50000,
  "tax_rate": 18,
  "notes": "Payment due within 30 days"
}
```

---

## 📊 Analytics (NEW!)

### Dashboard Analytics
```bash
GET /api/analytics/contractor/dashboard?contractor_registration_number=CONT123456
```

**Returns**:
- Client statistics (total, active, by region)
- Invoice statistics (by status)
- Location distribution
- Revenue analytics
- Monthly trends

### Location Revenue
```bash
GET /api/analytics/location-revenue?contractor_registration_number=CONT123456&region=ARUSHA&district=ARUSHA%20CBD&ward=SEKEI
```

**Returns**:
- Total revenue for location
- Client count
- Invoice count
- Average per client
- Collection rate

---

## 👤 Client Model

### Query Scopes
```php
use App\Models\Client;

// Filter by location
$clients = Client::byLocation('ARUSHA', 'ARUSHA CBD', 'SEKEI')->get();

// Filter by region only
$clients = Client::byRegion('ARUSHA')->get();

// For a specific contractor
$clients = Client::forContractor($contractorId)->get();

// Active clients only
$clients = Client::active()->get();

// Combine filters
$clients = Client::forContractor($contractorId)
    ->byLocation('ARUSHA', 'ARUSHA CBD', 'SEKEI')
    ->active()
    ->get();
```

### Get Formatted Location
```php
$client = Client::find(1);
echo $client->site_location; 
// Output: "SANAWARI, SEKEI, ARUSHA CBD, ARUSHA"
```

---

## 🚛 Contractor Model

### Query Scopes
```php
use App\Models\Contractor;

// Filter by location
$contractors = Contractor::byLocation('ARUSHA', 'ARUSHA CBD', 'SEKEI')->get();

// By region
$contractors = Contractor::byRegion('ARUSHA')->get();

// By district
$contractors = Contractor::byDistrict('ARUSHA CBD')->get();
```

### Get Formatted Location
```php
$contractor = Contractor::find(1);
echo $contractor->site_location;
// Output: "SANAWARI, SEKEI, ARUSHA CBD, ARUSHA"
```

---

## 📄 API Resources

### Use in Controllers
```php
use App\Http\Resources\ClientResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\ContractorResource;

// Single resource
return new ClientResource($client);

// Collection
return ClientResource::collection($clients);

// In JSON response
return response()->json([
    'success' => true,
    'data' => ClientResource::collection($clients)
]);
```

### Available Resources
- `ClientResource` - Client data with nested location
- `InvoiceResource` - Invoice with financial details
- `ContractorResource` - Contractor with location
- `LocationResource` - Location data

---

## ✅ Form Requests

### Using in Controllers
```php
use App\Http\Requests\BulkInvoiceRequest;
use App\Http\Requests\ClientsByLocationRequest;
use App\Http\Requests\ContractorRegistrationRequest;

// Automatic validation
public function store(BulkInvoiceRequest $request)
{
    // $request is already validated!
    // Access data normally
    $clientIds = $request->client_ids;
}
```

### Available Form Requests
- `BulkInvoiceRequest` - Validates bulk invoice creation
- `ClientsByLocationRequest` - Validates location filtering
- `ContractorRegistrationRequest` - Validates contractor signup

---

## 🗃️ Caching

### Cache Keys Used
```
locations:regions
locations:districts:{region}
locations:wards:{region}:{district}
locations:streets:{region}:{district}:{ward}
locations:autocomplete:{type}:{query}:{limit}
locations:statistics
analytics:contractor:{registration_number}
```

### Clear Cache
```bash
# Clear analytics cache
POST /api/analytics/clear-cache
{
  "contractor_registration_number": "CONT123456"
}

# Clear location cache (via LocationService)
$locationService->clearCache();
```

### Cache Durations
- Location data: **24 hours**
- Autocomplete: **1 hour**
- Analytics: **5 minutes**

---

## 🎨 Frontend Integration Examples

### Autocomplete Dropdown
```javascript
// Debounced autocomplete
let timeout;
document.getElementById('location-search').addEventListener('input', (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(async () => {
        const query = e.target.value;
        if (query.length < 2) return;
        
        const response = await fetch(
            `/api/locations/autocomplete?q=${query}&type=all&limit=10`
        );
        const data = await response.json();
        
        // Display suggestions
        displaySuggestions(data.data);
    }, 300);
});
```

### Load Clients by Location
```javascript
async function loadClients(location) {
    const response = await fetch('/api/invoices/clients-by-location', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            contractor_registration_number: 'CONT123456',
            ...location
        })
    });
    
    const data = await response.json();
    return data.data.clients;
}
```

### Create Bulk Invoices
```javascript
async function createBulkInvoices(clientIds, location, invoiceData) {
    const response = await fetch('/api/invoices/bulk-create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            contractor_registration_number: 'CONT123456',
            client_ids: clientIds,
            site_location: location,
            ...invoiceData
        })
    });
    
    return await response.json();
}
```

### Load Dashboard Analytics
```javascript
async function loadDashboard(contractorRegNumber) {
    const response = await fetch(
        `/api/analytics/contractor/dashboard?contractor_registration_number=${contractorRegNumber}`
    );
    
    const data = await response.json();
    
    // Update UI
    document.getElementById('total-clients').textContent = data.data.clients.total;
    document.getElementById('total-revenue').textContent = data.data.revenue.total_invoiced;
    document.getElementById('collection-rate').textContent = data.data.revenue.collection_rate + '%';
}
```

---

## 🔧 Database Queries

### Get Clients at Location
```php
$clients = Client::forContractor($contractorId)
    ->byLocation($region, $district, $ward, $street)
    ->active()
    ->orderBy('name')
    ->get();
```

### Get Revenue by Location
```php
$revenue = Invoice::forContractor($contractorId)
    ->whereIn('client_id', $clientIds)
    ->sum('total_amount');
```

### Get Location Statistics
```php
$stats = Client::forContractor($contractorId)
    ->select('region', 'district', 'ward', DB::raw('count(*) as count'))
    ->groupBy('region', 'district', 'ward')
    ->orderBy('count', 'desc')
    ->get();
```

---

## ⚠️ Common Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "region": ["Region is required."],
    "client_ids": ["Please select at least one client."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to process request",
  "error": "Detailed error message"
}
```

---

## 📈 Performance Tips

1. **Use Autocomplete Instead of Search**
   - ✅ Faster (cached for 1 hour)
   - ✅ Less data transfer
   - ✅ Better UX

2. **Cache Analytics Data**
   - ✅ Cached for 5 minutes
   - ✅ Clear cache after major updates

3. **Limit Results**
   - ✅ Use `limit` parameter
   - ✅ Paginate large datasets
   - ✅ Load on demand

4. **Use API Resources**
   - ✅ Consistent formatting
   - ✅ Automatic transformations
   - ✅ Hide sensitive data

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `LOCATION_API_GUIDE.md` | Location endpoints |
| `CRITICAL_FEATURE_IMPLEMENTATION.md` | Invoice creation |
| `CONTRACTOR_LOCATION_REGISTRATION.md` | Registration |
| `API_IMPROVEMENTS_DOCUMENTATION.md` | All improvements |
| `QUICK_REFERENCE_GUIDE.md` | This file |

---

## 🎯 Status

**All Features**: ✅ Production Ready

**Last Updated**: October 29, 2025
