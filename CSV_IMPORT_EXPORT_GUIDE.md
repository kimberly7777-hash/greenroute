# CSV Import/Export System Documentation

## Overview

Your GreenRoute system now has comprehensive CSV support for managing data in bulk. This includes importing and exporting locations, users, clients, and billing rates.

## Features

### ✅ Supported Operations

1. **Import Data from CSV**
   - Locations
   - Users/Staff
   - Clients
   - Billing Rates

2. **Export Data to CSV**
   - Locations
   - Users
   - Clients
   - Billing Rates

3. **Data Validation**
   - Automatic CSV format validation
   - Row-level error reporting
   - Preview before import

4. **Batch Processing**
   - Large file support (up to 20MB)
   - Optimized batch processing for performance
   - Transaction rollback on errors

## Access Points

### 1. Web Interface (Admin Dashboard)
Navigate to: `/admin/csv-management`

Features:
- Drag & drop file upload
- Live CSV preview (first 5 rows)
- Import statistics
- One-click export
- CSV format templates

### 2. API Endpoints

#### CSV Preview (No Import)
```
POST /api/csv/preview
Content-Type: multipart/form-data

Parameters:
- file: CSV file (required)
- type: locations|users|clients|contractors|billing_rates|routes (required)
```

#### Import Endpoints
```
POST /api/csv/import/locations
POST /api/csv/import/users
POST /api/csv/import/clients
POST /api/csv/import/billing-rates
```

#### Export Endpoints
```
GET /api/csv/export/locations
GET /api/csv/export/users
GET /api/csv/export/clients
GET /api/csv/export/billing-rates
```

#### Statistics
```
GET /api/csv/stats
```

### 3. Artisan Commands

#### Import Locations
```bash
php artisan csv:import-locations {file} [--batch=100]

Example:
php artisan csv:import-locations storage/app/tbl_locations.csv --batch=100
```

#### Import Users
```bash
php artisan csv:import-users {file} [--batch=50]

Example:
php artisan csv:import-users users.csv --batch=50
```

#### Export Data
```bash
php artisan csv:export {type} [--output=storage/exports]

Types: locations|users|clients|billing_rates

Examples:
php artisan csv:export locations --output=storage/exports
php artisan csv:export users
php artisan csv:export clients
php artisan csv:export billing_rates
```

## CSV Format Requirements

### 📍 Locations CSV
```csv
region,district,ward,street
ARUSHA,ARUSHA,ARUSHA,Avenue Street
ARUSHA,ARUSHA,SAMPSON,Main Road
DAR ES SALAAM,ILALA,ILALA,Morogoro Road
```

**Required Columns:** region, district, ward, street
**Optional Columns:** None
**Max Rows:** Unlimited (batch processed)

### 👤 Users CSV
```csv
name,email,password,role,phone
John Doe,john@example.com,password123,admin,255712345678
Jane Smith,jane@example.com,password456,contractor,255787654321
Bob Wilson,bob@example.com,password789,staff,255712345679
```

**Required Columns:** name, email, password, role
**Optional Columns:** phone
**Validation Rules:**
- name: Min 3 chars, Max 255 chars
- email: Valid email format
- role: admin, contractor, staff, client
- password: Will be bcrypt hashed automatically

### 🏢 Clients CSV
```csv
name,email,phone,region,district,ward,street
ABC Company,contact@abc.com,255712345678,ARUSHA,ARUSHA,ARUSHA,Avenue Street
XYZ Traders,info@xyz.com,255787654321,DAR ES SALAAM,ILALA,ILALA,Morogoro Road
```

**Required Columns:** name, email, phone
**Optional Columns:** region, district, ward, street
**Validation Rules:**
- name: Min 3 chars
- email: Valid email format
- phone: Valid phone number format

### 💰 Billing Rates CSV
```csv
region,district,ward,rate
ARUSHA,ARUSHA,ARUSHA,5000
ARUSHA,ARUSHA,SAMPSON,4500
DAR ES SALAAM,ILALA,ILALA,6000
```

**Required Columns:** region, rate
**Optional Columns:** district, ward
**Validation Rules:**
- region: Required
- rate: Numeric value

## Using the CSV Management Page

### Step 1: Navigate to CSV Management
1. Login to admin dashboard
2. Go to Admin → CSV Management

### Step 2: Import Data
1. Click on the import type (Locations, Users, Clients, Billing Rates)
2. Drag & drop your CSV file or click to browse
3. Review the preview (showing first 5 rows)
4. Click "Import Data" to start the import
5. Wait for confirmation message

### Step 3: Export Data
1. Scroll to Export section
2. Click on the data type you want to export
3. File will download automatically

## API Integration Examples

### JavaScript/Fetch
```javascript
// Import Locations
const formData = new FormData();
formData.append('file', csvFile);

const response = await fetch('/api/csv/import/locations', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

const data = await response.json();
console.log(data.message); // Success message
```

### cURL
```bash
# Import Users
curl -X POST \
  -F "file=@users.csv" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  http://yourapp.com/api/csv/import/users

# Export Locations
curl -X GET \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  http://yourapp.com/api/csv/export/locations > locations.csv
```

### PHP (Laravel)
```php
use App\Services\CsvService;

$csvService = new CsvService();

// Read CSV
$data = $csvService->readCsv('path/to/file.csv');

// Validate
$validation = $csvService->validateCsv($data['data'], [
    'email' => 'required|email',
    'name' => 'required|min:3'
]);

// Export
$result = $csvService->exportToCsv(
    $dataArray,
    'export.csv',
    ['id', 'name', 'email']
);
```

## Advanced Features

### Batch Processing
Large files are automatically processed in batches to prevent memory issues:

```php
$csvService->processCsvInBatches('file.csv', 100, function($batch) {
    // Process each batch
    Model::insert($batch);
    return ['inserted' => count($batch)];
});
```

### Data Sanitization
All imported data is automatically sanitized:
- Trimmed whitespace
- Removed HTML tags
- Escaped special characters

### Transaction Safety
All imports run within database transactions:
- If any row fails, the entire batch is rolled back
- Data consistency is guaranteed
- Detailed error reporting

### Progress Tracking (Artisan)
When using CLI commands, progress bars show:
- Rows processed
- Batch status
- Final statistics

## Error Handling

### Common Errors

#### Missing Required Columns
```
Error: Missing required columns: region, district
```
Solution: Ensure your CSV has all required column headers

#### Invalid Email Format
```
Row 5: Field 'email' must be a valid email
```
Solution: Check email format is correct (example@domain.com)

#### Phone Number Invalid
```
Row 3: Field 'phone' must be a valid phone number
```
Solution: Use format like 255712345678 or +255712345678

#### Duplicate Entry
```
Record skipped (already exists)
```
Solution: Duplicates are automatically skipped; update the row in the database first

## Performance Tips

1. **Batch Size**: Default is 100 rows per batch. Increase for faster imports:
   ```bash
   php artisan csv:import-locations file.csv --batch=500
   ```

2. **File Size**: For files > 10MB, consider splitting into smaller files

3. **Off-Peak**: Run large imports during off-peak hours

4. **Validation**: Always preview before importing

## Troubleshooting

### Import Hangs
- Check CSV file size (max 20MB)
- Ensure database has sufficient space
- Check server logs: `tail -f storage/logs/laravel.log`

### Validation Errors
- Download and check error details from admin page
- Verify column names match exactly
- Check data types and formats

### Memory Issues
- Reduce batch size: `--batch=50`
- Split large files into smaller chunks
- Increase PHP memory: `memory_limit = 512M`

### CSRF Token Errors
- Ensure CSRF token is included in requests
- Clear browser cache if persists
- Check CSRF middleware configuration

## Security Considerations

1. **Access Control**: CSV management is protected by admin middleware
2. **File Upload**: Only CSV files allowed, max 20MB
3. **Data Validation**: All data validated before import
4. **User Passwords**: Automatically hashed with bcrypt
5. **Audit Trail**: All imports logged in application logs
6. **Input Sanitization**: All user input trimmed and sanitized

## Backup Recommendations

Before large imports:
```bash
# Backup database
php artisan db:backup

# Or manually export current data
php artisan csv:export locations --output=backups/
php artisan csv:export users --output=backups/
```

## Next Steps

1. ✅ Prepare your CSV files using provided templates
2. ✅ Test import with small file first
3. ✅ Validate data in preview
4. ✅ Perform full import
5. ✅ Verify imported data in admin dashboard

## Support

For issues or questions:
1. Check the error messages displayed
2. Review CSV format templates in admin page
3. Check application logs: `storage/logs/laravel.log`
4. Test API endpoints using the preview function first

---

**Last Updated:** May 7, 2026
**Version:** 1.0
