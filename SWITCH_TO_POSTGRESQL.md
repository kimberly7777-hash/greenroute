# Switch from SQLite to PostgreSQL

## 🎯 Why Switch?

- ✅ **Better for production** - handles concurrent users
- ✅ **More reliable** - proper database server
- ✅ **Better performance** - optimized for web apps
- ✅ **Free on Render** - 256MB storage, 1GB RAM
- ✅ **Persistent data** - survives deployments

## 📋 Step-by-Step Migration

### 1. Create PostgreSQL Database on Render

1. Go to https://dashboard.render.com
2. Click **"New +"** → **"PostgreSQL"**
3. Configure:
   ```
   Name: afia-orbit-db
   Database: afia_orbit
   User: (auto-generated)
   Region: Oregon (US West) - same as your web service
   PostgreSQL Version: 16
   Plan: Free (256MB storage)
   ```
4. Click **"Create Database"**
5. Wait 1-2 minutes for provisioning

### 2. Get Connection Details

After creation:
1. Click on your database in Render dashboard
2. Go to **"Info"** or **"Connect"** tab
3. You'll see:
   ```
   Internal Database URL: (use this in your web service)
   External Database URL: (use this for local connections if needed)
   
   Host: dpg-xxxxx-xxxxx.oregon-postgres.render.com
   Port: 5432
   Database: afia_orbit
   Username: afia_orbit_user
   Password: xxxxxxxxxxxxxxxxxxxx
   ```

### 3. Update Web Service Environment Variables

1. Go to your **AFIA-ORBIT** web service
2. Click **"Environment"** tab
3. Click **"Edit"**
4. Update/add these variables:

```env
DB_CONNECTION=pgsql
DB_HOST=dpg-xxxxx-xxxxx.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=afia_orbit
DB_USERNAME=afia_orbit_user
DB_PASSWORD=your_password_here
```

5. Click **"Save Changes"**
6. Render will automatically redeploy (2-3 minutes)

### 4. Run Migrations on Render

After redeploy:
1. Go to your web service → **"Shell"** tab
2. Run:
   ```bash
   php artisan migrate:fresh
   ```

This creates all tables in the PostgreSQL database.

### 5. Import Locations

After migrations, import locations using our API method:

On your local machine:
```bash
php push_locations_to_render.php
```

Enter: `https://afia-orbit.onrender.com`

This will:
- Clear any existing data
- Import all 68,593 locations
- Verify the import

### 6. Test Everything

1. Check diagnostics:
   ```
   https://afia-orbit.onrender.com/api/diagnostics/system
   ```

2. Should show:
   ```json
   {
     "database_connected": true,
     "locations_table_exists": true,
     "locations_count": 68593,
     "locations_status": "populated"
   }
   ```

3. Test autocomplete on contractor registration!

---

## 🔧 Local Development Options

### Option A: Keep MySQL Locally (Recommended)

Your local `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=afia_orbit
DB_USERNAME=root
DB_PASSWORD=
```

Render uses PostgreSQL, local uses MySQL - both work fine!

### Option B: Use PostgreSQL Locally Too

1. Install PostgreSQL on Windows:
   - Download from https://www.postgresql.org/download/windows/
   - Or use XAMPP/Laragon with PostgreSQL

2. Update local `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=afia_orbit
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

3. Create local database:
   ```bash
   psql -U postgres
   CREATE DATABASE afia_orbit;
   \q
   ```

4. Run migrations locally:
   ```bash
   php artisan migrate:fresh
   ```

---

## ⚠️ Important Notes

### Before Migration:
- ✅ Backup any important data (users, contractors, clients, invoices)
- ✅ Export to CSV or JSON first

### After Migration:
- ✅ PostgreSQL uses different syntax than SQLite
- ✅ Most queries work the same in Laravel
- ✅ Some raw SQL might need adjustment

### Data Persistence:
- ✅ PostgreSQL data persists across deployments
- ✅ Free tier: 256MB storage, 90 days retention
- ✅ Backups available in paid plans

---

## 🎉 Benefits After Migration

| Feature | SQLite | PostgreSQL |
|---------|--------|------------|
| Concurrent Users | ❌ Limited | ✅ Unlimited |
| Data Persistence | ⚠️ Risk | ✅ Guaranteed |
| Performance | ⚠️ Slow | ✅ Fast |
| Backup/Restore | ❌ Manual | ✅ Automatic |
| Production Ready | ❌ No | ✅ Yes |

---

## 🚀 Quick Migration Commands

```bash
# 1. After creating PostgreSQL on Render and updating env vars
# Wait for Render to redeploy

# 2. In Render Shell
php artisan migrate:fresh

# 3. On your local machine
php push_locations_to_render.php

# 4. Verify
# Visit: https://afia-orbit.onrender.com/api/diagnostics/system

# 5. Test autocomplete!
```

---

**Migration time: ~10 minutes total** ⏱️

**Result: Production-ready database!** 🎉
