# 🚀 Deploy AFIA ORBIT to Render

## Quick Deploy Commands

### **Option 1: Deploy Everything**
```bash
git add .
git commit -m "Configure for Render deployment - Production ready"
git push origin backend
```

### **Option 2: Check What Will Be Committed**
```bash
git status
git diff
```

### **Option 3: Deploy Specific Files**
```bash
git add bootstrap/app.php
git add app/Http/Middleware/TrustProxies.php
git add render.yaml
git add RENDER_DEPLOYMENT_GUIDE.md
git add DEPLOYMENT_CHECKLIST.md
git commit -m "Add Render deployment configuration"
git push origin backend
```

---

## 📦 What Gets Deployed

### **New Files**:
- `app/Http/Middleware/TrustProxies.php` - HTTPS proxy handling
- `RENDER_DEPLOYMENT_GUIDE.md` - Complete deployment guide
- `DEPLOYMENT_CHECKLIST.md` - Pre/post deployment checklist
- `DEPLOY.md` - This file

### **Modified Files**:
- `bootstrap/app.php` - Added proxy trust configuration
- `render.yaml` - Updated with production URL

---

## ⏱️ Deployment Timeline

### **After you push**:

1. **Build Phase** (~5-10 minutes):
   - Pull Docker image
   - Install Composer dependencies
   - Install Node dependencies
   - Build assets with Vite
   - Create SQLite database
   - Generate APP_KEY

2. **Deploy Phase** (~1-2 minutes):
   - Run migrations
   - Cache configurations
   - Start Apache server

3. **Live** ✅:
   - Application accessible at https://afia-orbit.onrender.com
   - You'll receive email notification

**Total**: ~10-15 minutes for first deploy

---

## 🔍 Monitor Deployment

### **Watch Build Progress**:
1. Go to: https://dashboard.render.com
2. Click on your service
3. View **"Events"** tab for status
4. View **"Logs"** tab for build output

### **Check for Errors**:
```
Look for:
✅ "Build successful"
✅ "Deploy live"
❌ "Build failed" - Check logs for errors
```

---

## ✅ After Successful Deployment

### **1. Verify Application**:
Open: https://afia-orbit.onrender.com
- Should see homepage
- No errors
- HTTPS working

### **2. Create Admin Account**:

**Access Render Shell**:
- Dashboard → Your Service → Shell tab

**Run**:
```bash
php artisan tinker
```

**Create Admin**:
```php
$admin = new App\Models\User();
$admin->name = 'Administrator';
$admin->email = 'admin@afiaorbit.com';
$admin->username = 'admin';
$admin->password = bcrypt('SecurePassword123!');
$admin->user_type = 'admin';
$admin->status = 'approved';
$admin->email_verified_at = now();
$admin->save();
exit
```

### **3. Test Admin Login**:
- Go to: https://afia-orbit.onrender.com/admin/login
- Email: admin@afiaorbit.com
- Password: SecurePassword123!
- Should access admin dashboard ✅

---

## 🐛 If Deployment Fails

### **Common Fixes**:

**1. Build Failed**:
```bash
# Locally test build
npm install
npm run build
composer install --no-dev
```

**2. Missing Dependencies**:
```bash
# Check composer.json and package.json
# Ensure all dependencies listed
```

**3. Migration Errors**:
```bash
# In Render Shell
php artisan migrate:fresh --force
```

**4. Permission Errors**:
- Already handled in Dockerfile
- Check Render logs for specific errors

---

## 🔄 Redeploy After Changes

### **For Code Changes**:
```bash
git add .
git commit -m "Your change description"
git push origin backend
```

### **For Environment Variable Changes**:
1. Render Dashboard → Your Service
2. Environment → Edit variable
3. Click "Save Changes"
4. Service will auto-redeploy

---

## 📱 Production URLs

After deployment, your app will be available at:

| Page | URL |
|------|-----|
| Homepage | https://afia-orbit.onrender.com |
| Admin | https://afia-orbit.onrender.com/admin/login |
| Contractor | https://afia-orbit.onrender.com/login/contractor |
| Client | https://afia-orbit.onrender.com/login/client |

---

## 💡 Pro Tips

### **1. Test Locally First**:
```bash
# Build production assets
npm run build

# Test Laravel
php artisan config:cache
php artisan route:cache
php artisan serve
```

### **2. Check Git Branch**:
```bash
# Verify you're on backend branch
git branch

# If not, switch to it
git checkout backend
```

### **3. View Commit History**:
```bash
# See what's been deployed
git log --oneline -5
```

### **4. Rollback if Needed**:
```bash
# Revert to previous commit
git revert HEAD
git push origin backend
```

---

## 🎉 You're Ready!

**Execute this to deploy**:

```bash
git add .
git commit -m "Configure for Render deployment - Production ready"
git push origin backend
```

Then watch the magic happen at:
👉 https://dashboard.render.com

---

**Good luck with your deployment!** 🚀
