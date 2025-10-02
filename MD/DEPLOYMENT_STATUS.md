# Geraye Healthcare - Deployment Status
**Last Updated**: September 30, 2025  
**Status**: Ready for Testing & Staging Deployment

---

## 📱 Mobile App (Flutter) - Play Store & App Store

### ✅ Completed Configuration
- [x] **App Identity Updated**
  - Package ID: `com.gerayehealthcare.mobile`
  - App Name: `Geraye Healthcare`
  - Android namespace: `com.gerayehealthcare.mobile`
  
- [x] **Android Configuration**
  - `android/app/build.gradle.kts` updated with correct package ID
  - `strings.xml` created for proper app naming
  - `AndroidManifest.xml` using string resources
  - NDK 27.0.12077973 installed and fixed
  
- [x] **iOS Configuration**
  - `Info.plist` updated with correct display names
  - Bundle identifier ready: `com.gerayehealthcare.mobile`
  
- [x] **Dependencies**
  - Flutter 3.35.2 (stable)
  - All major packages installed (Riverpod, Dio, Firebase, etc.)
  - 69 packages have updates available (review recommended)

### ⚠️ Known Issues Fixed
- [x] NDK malformed installation → Fixed by copying source.properties to root
- [x] qr_code_scanner namespace missing → Added namespace to plugin build.gradle
- [x] App naming inconsistency → Standardized to "Geraye Healthcare"

### 🔄 In Progress
- [ ] **Testing Flutter Build**
  - Debug build test ongoing
  - Expected to complete shortly
  
### 📋 Next Steps for Play Store Deployment

#### Immediate (Before Internal Testing)
1. **Icons & Splash Screen**
   - [ ] Add `flutter_launcher_icons` to dev_dependencies
   - [ ] Create app icons (512x512 for Play Store, 1024x1024 for App Store)
   - [ ] Create adaptive icons for Android (foreground + background)
   - [ ] Add `flutter_native_splash` configuration
   - [ ] Generate icons: `flutter pub run flutter_launcher_icons`
   - [ ] Generate splash: `flutter pub run flutter_native_splash:create`

2. **Permissions & Privacy**
   - [ ] Review and add required permissions to AndroidManifest.xml:
     ```xml
     <uses-permission android:name="android.permission.INTERNET" />
     <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
     <uses-permission android:name="android.permission.CAMERA" />
     <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
     <uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
     ```
   - [ ] Add iOS usage descriptions to Info.plist:
     ```xml
     <key>NSLocationWhenInUseUsageDescription</key>
     <string>We use your location for visit check-in and check-out.</string>
     <key>NSCameraUsageDescription</key>
     <string>We need camera access to scan QR codes and capture documents.</string>
     <key>NSPhotoLibraryUsageDescription</key>
     <string>We need photo library access to attach documents and images.</string>
     ```

3. **API Configuration**
   - [ ] Set production API base URL
   - [ ] Configure dart-defines for environment management
   - [ ] Test API connectivity with production backend

4. **Android Release Build**
   - [ ] Generate upload keystore:
     ```bash
     keytool -genkey -v -keystore geraye-upload.keystore -alias geraye -keyalg RSA -keysize 2048 -validity 10000
     ```
   - [ ] Create `android/key.properties` (DO NOT COMMIT):
     ```
     storePassword=<YOUR_STORE_PASSWORD>
     keyPassword=<YOUR_KEY_PASSWORD>
     keyAlias=geraye
     storeFile=../geraye-upload.keystore
     ```
   - [ ] Update `android/app/build.gradle.kts` with signing config
   - [ ] Enable R8 minification and ProGuard rules
   - [ ] Build release AAB: `flutter build appbundle --release`

5. **iOS Release Build**
   - [ ] Open Xcode workspace: `open ios/Runner.xcworkspace`
   - [ ] Configure signing with Apple Developer account
   - [ ] Set Bundle Identifier: `com.gerayehealthcare.mobile`
   - [ ] Set version/build numbers
   - [ ] Archive and upload to TestFlight

#### Developer Accounts Setup
- [ ] **Google Play Console**
  - [ ] Register Google Play Developer account ($25 one-time fee)
  - [ ] Create app listing
  - [ ] Set up Play App Signing (recommended)
  
- [ ] **Apple Developer Program**
  - [ ] Enroll in Apple Developer Program ($99/year)
  - [ ] Create App ID and provisioning profiles
  - [ ] Set up App Store Connect listing

#### Store Listing Requirements
- [ ] **Screenshots** (6.7", 6.5", 5.5" for iOS; various for Android)
- [ ] **App Description** (short & full)
- [ ] **Privacy Policy URL** (required)
- [ ] **Support Email/URL**
- [ ] **Feature Graphic** (1024x500 for Play Store)
- [ ] **Content Rating** (PEGI, ESRB, etc.)
- [ ] **Data Safety Declaration** (Play Store)
- [ ] **Privacy Nutrition Labels** (App Store)

#### Testing Strategy
1. **Internal Testing** (Play Console / TestFlight)
   - Add 5-10 internal testers
   - Test core workflows: login, visits, messaging, offline sync
   
2. **Closed Testing** (Play Console)
   - 20-50 beta testers
   - Test GPS check-in/out, notifications, document uploads
   
3. **Production Rollout**
   - Start with 5% staged rollout
   - Monitor crash rates and ANRs
   - Gradually increase to 20% → 50% → 100%

---

## 🌐 Web App (Laravel) - Laravel Cloud

### ✅ Current Status
- [x] **Production-ready Laravel application**
  - Laravel 12 with Vue 3 + Inertia.js
  - PostgreSQL database with 80+ tables
  - Advanced RBAC with Spatie permissions
  - 200+ Vue components
  
- [x] **Core Features Complete**
  - Patient management
  - Staff & HR
  - Visit services
  - Messaging system
  - Medical records
  - Inventory management
  - Insurance coordination
  - Marketing tracker
  - Events management

### 📋 Laravel Cloud Deployment Steps

#### 1. Pre-Deployment Checklist
- [ ] **Environment Configuration**
  ```env
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://your-domain.com
  
  # Database (Laravel Cloud will provide)
  DB_CONNECTION=pgsql
  DB_HOST=<provided-by-laravel-cloud>
  DB_PORT=5432
  DB_DATABASE=<your-database>
  DB_USERNAME=<provided>
  DB_PASSWORD=<provided>
  
  # Queue (use database or Redis)
  QUEUE_CONNECTION=database
  
  # Mail (configure SendGrid/Mailgun)
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=<your-username>
  MAIL_PASSWORD=<your-password>
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@gerayehealthcare.com
  MAIL_FROM_NAME="Geraye Healthcare"
  
  # Storage (use S3 or local)
  FILESYSTEM_DISK=public
  
  # Sanctum (for mobile API)
  SANCTUM_STATEFUL_DOMAINS=your-domain.com,www.your-domain.com
  
  # Trusted Proxies
  TRUSTED_PROXIES=*
  ```

- [ ] **API Readiness**
  - Review `/api/v1` endpoints
  - Ensure Sanctum auth is configured
  - Test mobile API endpoints locally
  - Configure CORS for mobile app

#### 2. Laravel Cloud Setup
1. **Create Project**
   - Log in to Laravel Cloud console
   - Create new project: "Geraye Healthcare"
   - Select region (closest to users)
   
2. **Connect Repository**
   - Link to your GitHub/GitLab repository
   - Configure branch for deployment (main/production)
   - Set up automatic deployments
   
3. **Configure Environment**
   - Copy environment variables from checklist above
   - Laravel Cloud will provide database credentials
   - Add any secrets via Laravel Cloud dashboard
   
4. **Database Setup**
   - Laravel Cloud provisions PostgreSQL automatically
   - Run migrations via dashboard or CLI:
     ```bash
     php artisan migrate --force
     php artisan db:seed --class=RolesAndPermissionsSeeder
     php artisan db:seed --class=TestUsersSeeder
     ```
   
5. **Build & Optimization**
   - Configure build command in Laravel Cloud:
     ```bash
     npm install && npm run build
     composer install --optimize-autoloader --no-dev
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     php artisan event:cache
     php artisan storage:link
     ```

6. **Queue Workers**
   - Enable queue workers in Laravel Cloud dashboard
   - Configure: `php artisan queue:work --sleep=3 --tries=3`
   
7. **Scheduler**
   - Enable Laravel Scheduler in dashboard
   - Runs: `* * * * * php artisan schedule:run`

8. **Storage**
   - Run `php artisan storage:link` after deployment
   - Consider S3 for production file storage
   - Configure bucket and credentials if using S3

#### 3. Domain & SSL
- [ ] **Custom Domain**
  - Add your domain in Laravel Cloud dashboard
  - Update DNS records (A/CNAME) as instructed
  - Laravel Cloud provides automatic SSL via Let's Encrypt
  
- [ ] **Mobile API Endpoint**
  - Set API base URL: `https://api.gerayehealthcare.com/api/v1`
  - Or use subdomain: `https://your-domain.com/api/v1`
  - Update Flutter app constants with production URL

#### 4. Health Checks & Monitoring
- [ ] Set up health check endpoint: `GET /api/v1/system/health`
- [ ] Configure uptime monitoring (built into Laravel Cloud)
- [ ] Enable error tracking (Laravel Cloud provides logs)
- [ ] Set up APM if needed (optional)

#### 5. Post-Deployment Verification
- [ ] Test login from web
- [ ] Test API endpoints from mobile app
- [ ] Verify email sending
- [ ] Test file uploads
- [ ] Check queue processing
- [ ] Verify scheduled tasks
- [ ] Test RBAC permissions
- [ ] Review logs for errors

---

## 🔒 Security Checklist (Both Platforms)

### Mobile App
- [ ] Disable verbose logging in release builds
- [ ] Store API keys securely (flutter_secure_storage)
- [ ] Implement certificate pinning (optional)
- [ ] Enable ProGuard/R8 obfuscation
- [ ] Review all permissions (remove unused)
- [ ] Add network security config (Android)

### Web App
- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] API rate limiting configured
- [ ] CORS properly configured for mobile
- [ ] Session/cookie security settings
- [ ] HTTPS enforced
- [ ] Security headers configured
- [ ] Backup strategy in place

---

## 📊 Success Metrics

### Mobile App Launch (First 30 Days)
- [ ] 100+ downloads
- [ ] <2% crash rate
- [ ] >4.0 rating
- [ ] 70%+ feature adoption (visits, messaging)

### Web App Production
- [ ] <200ms API response time (p95)
- [ ] 99.9% uptime
- [ ] Zero critical security incidents
- [ ] All user roles can access their features

---

## 🚀 Timeline Estimate

| Phase | Duration | Status |
|-------|----------|--------|
| Mobile: Icons & Assets | 2-3 days | ⏳ Pending |
| Mobile: Release Build Setup | 1 day | ⏳ Pending |
| Mobile: Internal Testing | 1 week | ⏳ Pending |
| Mobile: Play Store Submission | 1-3 days review | ⏳ Pending |
| Mobile: App Store Submission | 1-2 weeks review | ⏳ Pending |
| Web: Laravel Cloud Setup | 1 day | ⏳ Pending |
| Web: Deployment & Testing | 2-3 days | ⏳ Pending |
| **Total to Production** | **3-4 weeks** | ⏳ In Progress |

---

## 📞 Support & Resources

- **Documentation**: See `/MD` folder for comprehensive guides
- **Mobile Development Guide**: `MD/GROUP_3_MOBILE_API/`
- **Production Readiness**: `MD/PRODUCTION_READINESS.md`
- **Publishing Guide**: `MD/publishMobile.md`
- **API Documentation**: `docs/openapi-v1.yaml`

---

## ✅ Quick Command Reference

### Mobile (Flutter)
```bash
# Clean and rebuild
flutter clean && flutter pub get

# Build Android AAB (release)
flutter build appbundle --release --dart-define=ENVIRONMENT=production

# Build iOS (Xcode archive)
open ios/Runner.xcworkspace
# Then: Product → Archive

# Run on emulator (debug)
flutter run -d emulator-5554
```

### Web (Laravel)
```bash
# Local development
composer install
npm install && npm run dev
php artisan serve

# Production deployment (Laravel Cloud handles this)
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

**Next Immediate Action**: Complete mobile icon/splash setup and test release build
