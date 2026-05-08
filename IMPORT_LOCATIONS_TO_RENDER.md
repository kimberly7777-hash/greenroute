# Import Locations to Render Database

## 🎯 Problem
- Local database: 68,593 locations ✅
- Render database: 0 locations ❌

## 🔧 Solution: Upload and Seed Locations

### Step 1: Copy CSV File to Storage

Copy your `tbl_locations.csv` file to the `storage/app/` directory:

```bash
# Make sure you're in the project root
cp path/to/your/tbl_locations.csv storage/app/tbl_locations.csv
```

> If you already have a file named `locations.csv`, rename it to `tbl_locations.csv` or place it alongside the renamed copy in `storage/app/`.

### Step 2: Commit and Push Seeder

```bash
git add database/seeders/LocationsSeeder.php
git add storage/app/locations.csv
git commit -m "Add locations seeder and CSV data"
git push
```

### Step 3: Run Seeder on Render

**Option A - Via Render Shell:**
1. Go to https://dashboard.render.com
2. Select your AFIA-ORBIT service
3. Click "Shell" tab
4. Run:
   ```bash
   php artisan db:seed --class=LocationsSeeder
   ```

**Option B - Via Command (if you have Render CLI):**
```bash
render shell
php artisan db:seed --class=LocationsSeeder
```

### Step 4: Verify Import

After seeding, check diagnostics again:
```
https://afia-orbit.onrender.com/api/diagnostics/system
```

Should now show:
```json
{
  "locations_count": 68593,
  "locations_status": "populated"
}
```

### Step 5: Test Autocomplete

Go to contractor registration and type "ARUSHA" - should now work!

---

## ⚠️ Important Notes

1. **CSV File Size:** The locations.csv is large (68,593 records). GitHub has file size limits.
   - If CSV is > 100MB, you'll need to use Git LFS or alternative method

2. **Alternative: Direct Database Import**
   - Export: `mysqldump -u root -p database tbl_locations > locations.sql`
   - Import to Render via their database connection string

3. **One-Time Operation:** This only needs to be done once. After that, locations persist in Render's database.

---

## 🎉 Expected Result

After importing:
- ✅ Autocomplete works on Render
- ✅ 68,593 locations searchable
- ✅ Contractor registration form fully functional
