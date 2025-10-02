# Geraye Mobile — Publishing Guide (Android + iOS)

This guide takes you from a production‑ready Flutter build to publishing on Google Play (Android) and App Store Connect (iOS). It includes account setup, store configuration, signing, build commands, track strategy, and post‑release checks.

## 0) Prerequisites
- Flutter stable (3.x) installed and verified (`flutter doctor`)
- A working production API (see MD/PRODUCTION_READINESS.md) and server URL
- Access to the mobile repo (separate from Laravel)
- App icons, splash assets, screenshots, privacy policy URL, support email/URL
- Optional: Firebase project for FCM (Android) and APNs keys (iOS) if you ship push notifications at launch

## 1) Prepare the App for Deployment
- Configure environment → production
  - Use dart‑defines (recommended):
    - `--dart-define=ENVIRONMENT=production`
    - `--dart-define=API_BASE_URL=https://api.gerayehealthcare.com/api/v1` (if you wire it)
  - Or set `AppConstants.baseUrl` to your production API host (Flutter)
- Versioning (Semantic + build numbers)
  - Update `pubspec.yaml` → `version: 1.0.0+1`
  - Increment build numbers on every store submission
- App identity (package/bundle IDs) ✅ CONFIGURED
  - Android `applicationId` in `android/app/build.gradle.kts`: `com.gerayehealthcare.mobile`
  - iOS Bundle Identifier in Xcode → Targets → Runner → Signing & Capabilities: `com.gerayehealthcare.mobile` (needs Xcode configuration)
- App display name ✅ CONFIGURED
  - Android: `android/app/src/main/res/values/strings.xml`
    ```xml
    <resources>
      <string name="app_name">Geraye Healthcare</string>
    </resources>
    ```
  - iOS: `ios/Runner/Info.plist`
    ```xml
    <key>CFBundleDisplayName</key>
    <string>Geraye Healthcare</string>
    <key>CFBundleName</key>
    <string>Geraye Healthcare</string>
    ```
- App launcher icon (Flutter)
  - Add `flutter_launcher_icons` to `pubspec.yaml`:
    ```yaml
    dev_dependencies:
      flutter_launcher_icons: ^0.13.1

    flutter_icons:
      android: true
      ios: true
      image_path: assets/icons/app_icon.png
      adaptive_icon_background: "#FFFFFF"
      adaptive_icon_foreground: assets/icons/app_icon_foreground.png
    ```
  - Run: `flutter pub run flutter_launcher_icons`
  - Android 13+ uses monochrome adaptive icons optionally.
- Splash screen (Flutter)
  - Add `flutter_native_splash` to `pubspec.yaml`:
    ```yaml
    dev_dependencies:
      flutter_native_splash: ^2.4.0

    flutter_native_splash:
      color: "#FFFFFF"
      image: assets/splash/splash_logo.png
      android_12:
        color: "#FFFFFF"
        image: assets/splash/splash_logo_android12.png
    ```
  - Run: `flutter pub run flutter_native_splash:create`
  - Android 12 uses system splash, configured via the tool above.
- Permissions & privacy strings
  - Android: declare only needed permissions in `android/app/src/main/AndroidManifest.xml` (e.g., location if GPS check‑in):
    ```xml
    <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
    ```
  - iOS: add usage strings to `ios/Runner/Info.plist` as needed:
    ```xml
    <key>NSLocationWhenInUseUsageDescription</key>
    <string>We use your location for visit check‑in and check‑out.</string>
    <key>NSUserTrackingUsageDescription</key>
    <string>We use this to improve your experience.</string>
    <key>UIBackgroundModes</key>
    <array>
      <string>fetch</string>
      <string>remote-notification</string>
    </array>
    ```
- Android release optimizations
  - In `android/app/build.gradle` set release buildType:
    ```gradle
    buildTypes {
      release {
        signingConfig signingConfigs.release
        minifyEnabled true      // R8 shrinker
        shrinkResources true
        proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
      }
    }
    ```
  - Add `proguard-rules.pro` keep rules for Flutter (usually default is fine). If using reflection-heavy libs, add relevant keep rules.
  - Ensure `compileSdkVersion`/`targetSdkVersion` are current (e.g., 34) in `android/app/build.gradle` and `android/build.gradle`.
- Signing (Android)
  - Create `android/key.properties` (do not commit):
    ```
    storePassword=***
    keyPassword=***
    keyAlias=geraye
    storeFile=../geraye-upload.keystore
    ```
  - Reference `key.properties` in `build.gradle` signingConfigs.
  - Prefer Play App Signing; use this key only for upload.
- iOS signing
  - Xcode → Runner target → “Automatically manage signing” with your Team.
  - Set Bundle ID and increment version/build before archive.
- Push notifications (optional at launch)
  - Android: add `google-services.json` to `android/app/` and integrate Firebase core/messaging.
  - iOS: enable Push capability, upload APNs Auth Key to Apple Developer; add capability to Xcode project.
- Hardening & flags
  - Disable verbose logs in release.
  - Initialize analytics/crash reporting only in production.
  - Review privacy policy and data safety disclosures ready for stores.
- QA checklists
  - Offline check‑in/out queue → sync when online
  - Notification preferences persisted
  - Patient self‑service available for patient persona

## 2) Developer Accounts
- Google Play Console (Android)
  - Register a Google Play Developer account
  - Set up organization, payments profile (for paid apps/IAP if any)
- Apple Developer Program (iOS)
  - Enroll in the Apple Developer Program (individual or organization)
  - Accept agreements; add team members as needed

## 3) Android — Google Play (AAB)
### 3.1 App/Store Setup
- In Play Console → Create app → Provide name, default language, app type, category.
- Complete:
  - App access (if login required, provide credentials)
  - Ads declaration (select No unless you serve ads)
  - Content rating questionnaire
  - Privacy policy URL
  - Store listing: short & full description, screenshots, feature graphic (1024×500), icons

### 3.2 App Signing & Keystore
- Prefer Play App Signing (recommended)
- If using your own upload key, generate it once:
  - `keytool -genkey -v -keystore geraye-upload.keystore -alias geraye -keyalg RSA -keysize 2048 -validity 10000`
- Configure `android/app/build.gradle` signingConfigs (upload key)

### 3.3 Build Release (AAB)
- From the mobile repo root:
  - `flutter clean`
  - `flutter pub get`
  - `flutter build appbundle --release --dart-define=ENVIRONMENT=production`
- Artifact: `build/app/outputs/bundle/release/app-release.aab`

### 3.4 Upload & Tracks
- Play Console → Your app → Production (or Internal testing first)
- Create new release → Upload `.aab`
- Set release notes (per locale)
- Rollout strategy:
  - Start with **Internal testing** (trusted testers), then **Closed testing**, then **Production** with phased (staged) rollout 5% → 20% → 100%
- Submit; resolve any pre‑launch warnings

## 4) iOS — App Store Connect (IPA)
### 4.1 Certificates, Identifiers & Profiles
- Apple Developer portal:
  - Create App ID with your Bundle ID (e.g., `com.gerayehealthcare.mobile`)
  - Enable capabilities (Push Notifications if needed, Background Modes → Remote notifications)
  - Create signing certificates (Apple Distribution) and a provisioning profile (iOS App Store)
  - (If using APNs): Create APNs Auth Key and add it to your push backend configuration

### 4.2 Xcode Configuration
- Open `ios/Runner.xcworkspace` in Xcode
- Set Team, Bundle Identifier, version (CFBundleShortVersionString) and build (CFBundleVersion)
- Ensure `Runner` target → Signing & Capabilities is set to “Automatically manage signing”

### 4.3 Build & Archive
- Option A (Flutter CLI):
  - `flutter build ipa --release --export-options-plist=ios/exportOptions.plist --dart-define=ENVIRONMENT=production`
  - ExportOptions should include method `app-store`
- Option B (Xcode):
  - Product → Archive → Distribute App → App Store Connect → Upload

### 4.4 App Store Connect Setup
- Create a new app in App Store Connect (iOS app)
  - Provide name, SKU, primary language, Bundle ID, platforms
- App Information
  - Subtitle, keywords, support URL, marketing URL
  - Age rating, content rights, privacy policy URL (required)
- Prepare screenshots (6.7”, 6.5”, 5.5” sizes) and app previews if available
- TestFlight testing (recommended)
  - Internal testers (immediate), external testers (requires review)
- Submit for review (production)
  - After passing review, release manually or automatically

## 5) Store Listing Essentials
- Accurate descriptions aligned with screenshots
- Clear persona journeys (clinician, patient) and privacy positioning
- Data safety (Android) and Privacy Nutrition Labels (iOS)
- Contact & support info

## 6) CI/CD (Optional but Recommended)
- GitHub Actions or CircleCI with:
  - Flutter build matrix (Android AAB + iOS IPA)
  - Fastlane lanes for signing, build, and upload (supply/pilot)
  - Secrets: keystore, keystore password, Apple API key, App Store Connect API
- Artifacts & release tagging

## 7) Post‑Release
- Monitor crash rates (Crashlytics/Sentry) and ANR (Android)
- Track retention, active users, and feature usage via analytics
- Prepare hotfix builds if critical issues appear
- Increment version/build numbers for subsequent releases

## 8) Quick Command Reference
- Android release build:
  - `flutter build appbundle --release --dart-define=ENVIRONMENT=production`
- iOS release build (Xcode archive):
  - `open ios/Runner.xcworkspace` → Product → Archive
- iOS build (CLI, optional):
  - `flutter build ipa --release --export-options-plist=ios/exportOptions.plist --dart-define=ENVIRONMENT=production`

## 9) Checklists
- [ ] Production API URL configured (dart‑define or constants)
- [ ] Version and build numbers set
- [ ] Icons/splash/screenshots complete
- [ ] Privacy policy URL and data disclosures done
- [ ] Push notifications configured (if used)
- [ ] Offline queue tested + sync
- [ ] Login credentials for reviewers provided (if required)
- [ ] Internal/closed testing complete
- [ ] Staged rollout plan defined

---
If you want, I can add fastlane configuration and GitHub Actions workflows to automate Play Console and App Store Connect uploads.
