# CSV Quick Reference Guide

## 🚀 Quick Start

### Access CSV Management
```
Admin Dashboard → CSV Management
```
**URL:** `http://yourapp.com/admin/csv-management`

## 📋 CSV Format Templates

### Locations
```csv
region,district,ward,street
ARUSHA,ARUSHA,ARUSHA,Avenue Street
DAR ES SALAAM,ILALA,ILALA,Morogoro Road
```

### Users
```csv
name,email,password,role,phone
John Doe,john@example.com,secret123,admin,255712345678
Jane Smith,jane@example.com,secret456,contractor,255787654321
```

### Clients
```csv
name,email,phone,region,district,ward,street
ABC Company,contact@abc.com,255712345678,ARUSHA,ARUSHA,ARUSHA,Avenue Street
XYZ Traders,info@xyz.com,255787654321,DAR ES SALAAM,ILALA,ILALA,Morogoro Road
```

### Billing Rates
```csv
region,district,ward,rate
ARUSHA,ARUSHA,ARUSHA,5000
DAR ES SALAAM,ILALA,ILALA,6000
```

## 🔧 CLI Commands

### Import from Terminal

```bash
# Import locations
php artisan csv:import-locations path/to/locations.csv

# Import users
php artisan csv:import-users path/to/users.csv

# Import with custom batch size
php artisan csv:import-locations data.csv --batch=500
```

### Export from Terminal

```bash
# Export to default location (storage/exports/)
php artisan csv:export locations
php artisan csv:export users
php artisan csv:export clients
php artisan csv:export billing_rates

# Export to custom location
php artisan csv:export locations --output=/path/to/backups/
```

## 🌐 API Endpoints

### Preview CSV (Test Before Import)
```
POST /api/csv/preview
Content-Type: multipart/form-data

file: [CSV file]
type: locations|users|clients|billing_rates
```

### Import Data
```
POST /api/csv/import/locations
POST /api/csv/import/users
POST /api/csv/import/clients
POST /api/csv/import/billing-rates

Content-Type: multipart/form-data
file: [CSV file]
```

### Export Data
```
GET /api/csv/export/locations
GET /api/csv/export/users
GET /api/csv/export/clients
GET /api/csv/export/billing-rates
```

### Get Statistics
```
GET /api/csv/stats
```

## 📊 Web UI Workflow

### Step 1: Choose Data Type
Click one of: Locations, Users, Clients, Billing Rates

### Step 2: Upload File
- Drag & drop file OR click to browse
- Supported: CSV, TXT files
- Max size: 20MB

### Step 3: Preview
- System shows first 5 rows
- Verify data looks correct

### Step 4: Import
- Click "Import Data" button
- Wait for confirmation
- Check success message

### Step 5: Export (Optional)
- Click export button for any data type
- File downloads automatically

## ✅ Validation Rules

### Locations
- ✓ All 4 columns required (region, district, ward, street)
- ✓ Cannot have empty values
- ✓ Duplicates automatically skipped

### Users
- ✓ Name: min 3 chars, max 255 chars
- ✓ Email: valid email format required
- ✓ Password: any length (auto-hashed)
- ✓ Role: must be admin, contractor, staff, or client
- ✓ Phone: optional, must match phone format

### Clients
- ✓ Name: min 3 chars required
- ✓ Email: valid email required
- ✓ Phone: valid format required
- ✓ Location fields: optional
- ✓ Duplicates (by email) skipped

### Billing Rates
- ✓ Region: required
- ✓ Rate: numeric value required
- ✓ District/Ward: optional

## 🐛 Troubleshooting

### Error: "Missing required columns"
**Solution:** Check your CSV headers match exactly:
- `region`, `district`, `ward`, `street` (no extra spaces)

### Error: "Invalid email format"
**Solution:** Use format: `example@domain.com`

### Error: "File too large"
**Solution:** 
- Max file size is 20MB
- Split into multiple files
- Increase limits in php.ini

### Error: "CSRF token mismatch"
**Solution:** 
- Use web interface (automatically handles CSRF)
- If using API, include X-CSRF-TOKEN header

### Import appears stuck
**Solution:**
- Large files take time (100,000 rows = ~2 minutes)
- Check application logs: `tail storage/logs/laravel.log`
- Increase PHP memory limit if needed

## 📈 Performance Tips

### Large File Imports
```bash
# Use larger batch size for speed
php artisan csv:import-locations large-file.csv --batch=500
```

### Recommended Batch Sizes
- Default: 100 rows (balanced)
- Small files: 50 rows (safer)
- Large files: 500+ rows (faster)

### Off-Peak Import
```bash
# Schedule for 2 AM (example)
0 2 * * * php artisan csv:import-locations /path/to/file.csv
```

## 🔐 Security Notes

- ✓ Admin authentication required
- ✓ CSRF protection enabled
- ✓ File type validation (CSV only)
- ✓ File size limits enforced
- ✓ All data sanitized
- ✓ Passwords hashed with bcrypt
- ✓ SQL injection prevention
- ✓ Audit logging enabled

## 💾 Backup Strategy

### Daily Backup
```bash
# Add to cron (runs daily at 1 AM)
0 1 * * * php artisan csv:export locations --output=/backups/daily/
0 1 * * * php artisan csv:export users --output=/backups/daily/
0 1 * * * php artisan csv:export clients --output=/backups/daily/
```

### Weekly Full Backup
```bash
# Add to cron (runs every Sunday at 3 AM)
0 3 * * 0 php artisan csv:export locations --output=/backups/weekly/$(date +\%Y\%m\%d)/
0 3 * * 0 php artisan csv:export users --output=/backups/weekly/$(date +\%Y\%m\%d)/
```

## 📝 CSV Best Practices

### ✓ DO:
- Use correct column names (case-insensitive)
- Include headers in first row
- Use UTF-8 encoding
- Test with small file first
- Preview before importing
- Keep backup of original file
- Use consistent date format

### ✗ DON'T:
- Include special characters in data
- Mix data types in same column
- Use formulas (export as values)
- Leave required fields empty
- Import same file twice (use export/import cycle for updates)

## 🎯 Common Workflows

### Add New Locations
```bash
# 1. Create locations.csv
# 2. Upload via web UI or:
php artisan csv:import-locations locations.csv
```

### Bulk Create Staff Accounts
```bash
# 1. Create users.csv with columns: name, email, password, role
# 2. Import via /admin/csv-management
# 3. Share login info with staff
```

### Update Billing Rates
```bash
# 1. Export current: php artisan csv:export billing_rates
# 2. Edit exported file
# 3. Re-import updated file
```

### Backup All Data
```bash
# Export everything
php artisan csv:export locations --output=backups/
php artisan csv:export users --output=backups/
php artisan csv:export clients --output=backups/
php artisan csv:export billing_rates --output=backups/
```

### Migrate from Old System
```bash
# 1. Export from old system to CSV
# 2. Map columns to match our format
# 3. Preview and validate
# 4. Import via API or web UI
```

## 📞 Support

**Issues?** Check:
1. CSV_IMPORT_EXPORT_GUIDE.md (detailed guide)
2. Application logs: `storage/logs/laravel.log`
3. Preview endpoint (test before import)
4. Error messages (include row numbers)

---

**Version:** 1.0 | **Updated:** May 7, 2026
