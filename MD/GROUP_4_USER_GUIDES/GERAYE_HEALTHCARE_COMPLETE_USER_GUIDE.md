# Geraye Healthcare - Complete User Guide

## 📱💻 **Dual Platform Healthcare Management System**

Welcome to Geraye Healthcare's comprehensive management system, available on both **Web Application** and **Mobile Application** platforms. This guide provides step-by-step instructions for all user roles and modules.

---

## 🏗️ **System Overview**

### **Platform Availability**
- **🌐 Web Application**: Full-featured admin dashboard for desktop/laptop use
- **📱 Mobile Application**: Streamlined mobile interface for on-the-go healthcare management

### **User Roles & Access Levels**
1. **👑 Super Admin** - Complete system control
2. **🏢 CEO** - Executive oversight and analytics
3. **⚙️ COO** - Operational management
4. **👨‍💼 Admin** - Administrative functions
5. **👩‍⚕️ Staff** (Doctors, Nurses, Technicians) - Clinical operations
6. **👤 Guest** - Limited public access

---

## 🔐 **Getting Started - Login Credentials**

### **Test User Accounts**

| Role | Email | Password | Platform Access |
|------|-------|----------|----------------|
| Super Admin | superadmin@gerayehealthcare.com | SuperAdmin123! | Web + Mobile |
| CEO | ceo@gerayehealthcare.com | CEO123! | Web + Mobile |
| COO | coo@gerayehealthcare.com | COO123! | Web + Mobile |
| Admin | admin@gerayehealthcare.com | Admin123! | Web + Mobile |
| Doctor | doctor@gerayehealthcare.com | Doctor123! | Web + Mobile |
| Nurse | nurse@gerayehealthcare.com | Nurse123! | Web + Mobile |
| Technician | technician@gerayehealthcare.com | Tech123! | Web + Mobile |
| Guest | guest@gerayehealthcare.com | Guest123! | Mobile Only |

---

## 🌐 **WEB APPLICATION GUIDE**

### **Accessing the Web Application**
1. Open your web browser
2. Navigate to: `https://your-domain.com`
3. Click "Login" button
4. Enter your email and password
5. Click "Sign In"

### **Web Dashboard Overview**
- **Navigation Sidebar**: Access all modules and features
- **Main Content Area**: Display current page content
- **Header Bar**: User profile, notifications, logout
- **Breadcrumbs**: Track your current location
- **Action Buttons**: Create, Export, Print functions

---

## 📱 **MOBILE APPLICATION GUIDE**

### **Installing the Mobile App**
1. **Android**: Download from Google Play Store
2. **iOS**: Download from Apple App Store
3. **Web**: Access via mobile browser at `https://your-domain.com/mobile`

### **Mobile App Features**
- **Role-based Dashboards**: Customized for each user type
- **Offline Capability**: Works without internet connection
- **Real-time Sync**: Automatic data synchronization
- **Push Notifications**: Instant alerts and updates
- **Touch-optimized UI**: Designed for mobile interaction

---

## 👑 **SUPER ADMIN USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Complete system administration
- User and role management
- System configuration and maintenance
- Data backup and security oversight

### **Step-by-Step Workflows**

#### **1. User Management**
**Scenario**: Create a new doctor account

**Web Application Steps**:
1. Login as Super Admin
2. Navigate to "Users" → "All Users"
3. Click "Add User" button
4. Fill in user details:
   - Name: "Dr. John Smith"
   - Email: "john.smith@gerayehealthcare.com"
   - Password: Generate secure password
   - Role: Select "Staff"
5. Click "Save User"
6. Navigate to "Staff" → "All Staff"
7. Click "Add Staff Member"
8. Link to the created user account
9. Fill in professional details:
   - Position: "Senior Doctor"
   - Department: "Cardiology"
   - Phone: "+251911234567"
   - Hourly Rate: "$120.00"
10. Click "Save Staff"

**Mobile Application Steps**:
1. Open mobile app and login
2. Tap "Admin" tab
3. Tap "Staff Management"
4. Tap "Add Staff" (+ button)
5. Fill in all required fields
6. Tap "Save"

**Test Data to Create**:
```
User: Dr. John Smith
Email: john.smith@hospital.com
Department: Cardiology
Position: Senior Doctor
Phone: +251911234567
Hourly Rate: $120.00
Status: Active
```

#### **2. System Configuration**
**Scenario**: Configure system backup settings

**Web Steps**:
1. Navigate to "System" → "Settings"
2. Click "Backup Configuration"
3. Set backup frequency: "Daily at 2:00 AM"
4. Select backup location: "Cloud Storage"
5. Enable email notifications
6. Click "Save Configuration"
7. Test backup by clicking "Run Backup Now"

#### **3. Role and Permission Management**
**Scenario**: Create custom role for "Department Head"

**Web Steps**:
1. Navigate to "Roles" → "All Roles"
2. Click "Create Role"
3. Enter role name: "Department Head"
4. Select permissions:
   - View staff, Edit staff
   - View patients, Edit patients
   - View reports, Export reports
5. Click "Save Role"
6. Assign role to user in "Users" section

---

## 🏢 **CEO USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Strategic oversight and decision making
- Financial and performance analytics
- High-level reporting and insights
- Executive dashboard monitoring

### **Step-by-Step Workflows**

#### **1. Executive Dashboard Review**
**Scenario**: Daily executive briefing

**Web Application Steps**:
1. Login as CEO
2. Dashboard automatically displays:
   - Key Performance Indicators (KPIs)
   - Revenue trends and financial metrics
   - Patient satisfaction scores
   - Staff performance overview
   - System health status
3. Click on any metric for detailed analysis
4. Use date filters to view historical data
5. Export reports for board meetings

**Mobile Application Steps**:
1. Open app and login
2. CEO dashboard shows:
   - Today's key metrics
   - Critical alerts
   - Quick action buttons
3. Swipe through metric cards
4. Tap any metric for details

**Test Scenario Data**:
```
Review Period: Last 30 days
Expected Metrics:
- Total Revenue: $125,000
- Patient Visits: 450
- Staff Utilization: 85%
- Patient Satisfaction: 4.7/5
- System Uptime: 99.8%
```

#### **2. Financial Analysis**
**Scenario**: Monthly financial review

**Web Steps**:
1. Navigate to "Analytics" → "Financial Reports"
2. Select date range: "Last Month"
3. Review revenue breakdown:
   - Service revenue
   - Insurance payments
   - Outstanding invoices
4. Compare with previous month
5. Export detailed financial report
6. Generate executive summary

#### **3. Strategic Planning**
**Scenario**: Quarterly performance review

**Web Steps**:
1. Navigate to "Reports" → "Executive Reports"
2. Generate quarterly summary
3. Review department performance
4. Analyze growth trends
5. Export presentation-ready reports
6. Schedule follow-up meetings

---

## ⚙️ **COO USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Operational management and oversight
- Staff scheduling and resource allocation
- Process optimization and efficiency
- Quality assurance and compliance

### **Step-by-Step Workflows**

#### **1. Staff Schedule Management**
**Scenario**: Create weekly staff schedule

**Web Application Steps**:
1. Login as COO
2. Navigate to "Staff" → "Schedules"
3. Select week: "Next Week"
4. Click "Create Schedule"
5. Assign shifts for each staff member:
   - Dr. Smith: Monday-Wednesday (8AM-6PM)
   - Nurse Johnson: Monday-Friday (6AM-2PM)
   - Dr. Wilson: Thursday-Saturday (10AM-8PM)
6. Check for conflicts (system highlights overlaps)
7. Add break times and lunch hours
8. Click "Publish Schedule"
9. Send notifications to all staff

**Mobile Application Steps**:
1. Open app and login
2. Tap "Operations" tab
3. Tap "Staff Schedules"
4. Tap "Create Schedule" (+)
5. Select staff member
6. Set shift times using time picker
7. Tap "Save"
8. Repeat for all staff

**Test Data to Create**:
```
Week: March 4-10, 2024
Dr. Smith: Mon-Wed 8AM-6PM
Nurse Johnson: Mon-Fri 6AM-2PM
Dr. Wilson: Thu-Sat 10AM-8PM
Technician Kim: Tue-Thu 9AM-5PM
```

#### **2. Inventory Management**
**Scenario**: Weekly inventory review and restocking

**Web Steps**:
1. Navigate to "Inventory" → "Items"
2. Review low stock alerts (red indicators)
3. Click on low stock item: "Surgical Gloves"
4. Current stock: 25 units
5. Reorder level: 50 units
6. Click "Create Request"
7. Fill request details:
   - Quantity: 200 units
   - Supplier: "MedSupply Co."
   - Priority: "High"
   - Justification: "Below reorder level"
8. Click "Submit Request"
9. Track request status

#### **3. Quality Assurance**
**Scenario**: Monthly quality review

**Web Steps**:
1. Navigate to "Reports" → "Quality Metrics"
2. Review patient satisfaction scores
3. Check staff performance ratings
4. Analyze service delivery times
5. Identify improvement areas
6. Create action plans
7. Assign follow-up tasks

---

## 👨‍💼 **ADMIN USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Patient registration and management
- Appointment scheduling
- Insurance and billing coordination
- Administrative support functions

### **Step-by-Step Workflows**

#### **1. Patient Registration**
**Scenario**: Register new patient

**Web Application Steps**:
1. Login as Admin
2. Navigate to "Patients" → "All Patients"
3. Click "Add Patient"
4. Fill in patient information:
   - **Personal Details**:
     - First Name: "Sarah"
     - Last Name: "Johnson"
     - Date of Birth: "1985-06-15"
     - Gender: "Female"
     - Phone: "+251911987654"
     - Email: "sarah.johnson@email.com"
   - **Address**:
     - Street: "123 Main Street"
     - City: "Addis Ababa"
     - Region: "Addis Ababa"
     - Postal Code: "1000"
   - **Emergency Contact**:
     - Name: "John Johnson"
     - Relationship: "Husband"
     - Phone: "+251911987655"
   - **Medical Information**:
     - Blood Type: "O+"
     - Allergies: "Penicillin"
     - Current Medications: "None"
     - Insurance Provider: "Ethiopian Insurance"
     - Policy Number: "EI123456789"
5. Click "Save Patient"
6. Print patient card
7. Generate patient ID number

**Mobile Application Steps**:
1. Open app and login
2. Tap "Patients" tab
3. Tap "Add Patient" (+)
4. Fill in patient form (multiple screens)
5. Take patient photo (optional)
6. Tap "Save Patient"

**Test Data to Create**:
```
Patient: Sarah Johnson
DOB: June 15, 1985
Phone: +251911987654
Email: sarah.johnson@email.com
Blood Type: O+
Allergies: Penicillin
Insurance: Ethiopian Insurance (EI123456789)
Emergency Contact: John Johnson (Husband) +251911987655
```

#### **2. Appointment Scheduling**
**Scenario**: Schedule patient appointment

**Web Steps**:
1. Navigate to "Appointments" → "Schedule"
2. Click "New Appointment"
3. Search and select patient: "Sarah Johnson"
4. Select service: "General Consultation"
5. Choose doctor: "Dr. Smith"
6. Select date: "Tomorrow"
7. Choose time: "2:00 PM"
8. Add notes: "Follow-up for blood pressure"
9. Click "Schedule Appointment"
10. Send confirmation SMS/Email
11. Print appointment slip

#### **3. Insurance Processing**
**Scenario**: Process insurance claim

**Web Steps**:
1. Navigate to "Insurance" → "Claims"
2. Click "New Claim"
3. Select patient: "Sarah Johnson"
4. Select visit/service
5. Enter claim details:
   - Service code: "99213"
   - Amount: "$150.00"
   - Diagnosis code: "I10"
6. Upload supporting documents
7. Submit to insurance company
8. Track claim status

---

## 👩‍⚕️ **DOCTOR USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Patient consultations and treatment
- Medical record documentation
- Prescription management
- Treatment planning and follow-up

### **Step-by-Step Workflows**

#### **1. Patient Consultation Workflow**
**Scenario**: Complete patient visit from start to finish

**Web Application Steps**:

**Step 1: Review Patient Information**
1. Login as Doctor
2. Navigate to "Patients" → "Today's Appointments"
3. Click on patient: "Sarah Johnson - 2:00 PM"
4. Review patient history:
   - Previous visits
   - Current medications
   - Known allergies
   - Insurance information

**Step 2: Document Visit**
1. Click "Start Visit"
2. Record vital signs:
   - Blood Pressure: "140/90"
   - Temperature: "98.6°F"
   - Heart Rate: "72 bpm"
   - Weight: "65 kg"
3. Document chief complaint: "High blood pressure follow-up"
4. Record examination findings:
   - Physical examination results
   - Symptoms assessment
   - Clinical observations

**Step 3: Create Treatment Plan**
1. Click "Treatment Plan"
2. Add diagnosis: "Hypertension (I10)"
3. Prescribe medications:
   - Medication: "Lisinopril 10mg"
   - Dosage: "Once daily"
   - Duration: "30 days"
   - Instructions: "Take with food"
4. Add follow-up instructions:
   - "Return in 2 weeks"
   - "Monitor blood pressure daily"
   - "Reduce sodium intake"

**Step 4: Generate Documents**
1. Click "Generate Prescription"
2. Print prescription for patient
3. Click "Create Medical Report"
4. Generate visit summary
5. Update medical records

**Step 5: Schedule Follow-up**
1. Click "Schedule Follow-up"
2. Select date: "2 weeks from today"
3. Choose time slot
4. Add appointment notes
5. Send reminder to patient

**Step 6: Process Payment**
1. Click "Generate Invoice"
2. Add services:
   - Consultation: "$100.00"
   - Blood pressure check: "$25.00"
3. Apply insurance coverage: "80%"
4. Patient responsibility: "$25.00"
5. Process payment or send to billing

**Mobile Application Steps**:
1. Open app and login
2. Tap "Patients" tab
3. Tap "Today's Schedule"
4. Select patient appointment
5. Tap "Start Visit"
6. Fill in visit details using mobile forms
7. Use voice-to-text for notes
8. Take photos of relevant documents
9. Generate prescription using mobile interface
10. Schedule follow-up appointment
11. Process payment through mobile payment system

**Test Data to Create**:
```
Patient: Sarah Johnson
Visit Date: Today, 2:00 PM
Chief Complaint: High blood pressure follow-up
Vital Signs:
- BP: 140/90
- Temp: 98.6°F
- HR: 72 bpm
- Weight: 65 kg

Diagnosis: Hypertension (I10)
Prescription: Lisinopril 10mg, once daily, 30 days
Follow-up: 2 weeks
Services: Consultation ($100), BP check ($25)
Insurance Coverage: 80%
Patient Payment: $25.00
```

#### **2. Medical Records Management**
**Scenario**: Update patient medical history

**Web Steps**:
1. Navigate to "Medical Records"
2. Search patient: "Sarah Johnson"
3. Click "Add Medical Record"
4. Document type: "Progress Note"
5. Add clinical notes
6. Upload test results
7. Update medication list
8. Save and sign electronically

#### **3. Prescription Management**
**Scenario**: Manage patient prescriptions

**Web Steps**:
1. Navigate to "Prescriptions"
2. View active prescriptions
3. Check for drug interactions
4. Renew prescriptions
5. Send to pharmacy electronically
6. Track prescription status

---

## 👩‍⚕️ **NURSE USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Patient care coordination
- Vital signs monitoring
- Medication administration
- Patient education and support

### **Step-by-Step Workflows**

#### **1. Patient Care Workflow**
**Scenario**: Daily patient care routine

**Web Application Steps**:
1. Login as Nurse
2. Navigate to "Patients" → "Assigned Patients"
3. Review patient list for today
4. Click on patient: "Sarah Johnson"
5. Record vital signs:
   - Time: "8:00 AM"
   - Blood Pressure: "138/88"
   - Temperature: "98.4°F"
   - Pulse: "70 bpm"
6. Document care activities:
   - Medication administered
   - Patient response
   - Any concerns or observations
7. Update care plan
8. Communicate with doctor if needed

**Mobile Application Steps**:
1. Open app and login
2. Tap "Patients" tab
3. View assigned patients
4. Tap patient name
5. Use quick entry for vital signs
6. Take photos of wounds/conditions
7. Voice record patient notes
8. Send alerts to doctor if needed

#### **2. Medication Administration**
**Scenario**: Administer prescribed medications

**Steps**:
1. Navigate to "Medications" → "Due Now"
2. Scan patient wristband (mobile)
3. Verify patient identity
4. Check medication order
5. Verify dosage and timing
6. Administer medication
7. Document administration
8. Monitor for adverse reactions

---

## 🔬 **TECHNICIAN USER GUIDE**

### **Platform Access**: Web + Mobile

### **Key Responsibilities**
- Laboratory test processing
- Equipment maintenance
- Sample collection and analysis
- Quality control procedures

### **Step-by-Step Workflows**

#### **1. Laboratory Test Processing**
**Scenario**: Process blood test for patient

**Web Steps**:
1. Login as Technician
2. Navigate to "Laboratory" → "Pending Tests"
3. Select test order: "Sarah Johnson - Blood Panel"
4. Scan sample barcode
5. Process test:
   - Run blood analysis
   - Record results
   - Verify quality controls
6. Enter results in system
7. Flag abnormal values
8. Send results to ordering physician

#### **2. Equipment Maintenance**
**Scenario**: Daily equipment check

**Steps**:
1. Navigate to "Equipment" → "Maintenance"
2. Select equipment: "Blood Analyzer"
3. Perform daily checks:
   - Calibration verification
   - Quality control tests
   - Cleaning procedures
4. Document maintenance activities
5. Report any issues
6. Schedule preventive maintenance

---

## 👤 **GUEST USER GUIDE (Mobile Only)**

### **Platform Access**: Mobile Application Only

### **Key Features**
- Browse healthcare services
- Book appointments
- View contact information
- Access public health information

### **Step-by-Step Workflows**

#### **1. Service Browsing**
**Scenario**: Explore available services

**Mobile Steps**:
1. Open mobile app
2. Tap "Services" tab
3. Browse service categories:
   - Emergency Services
   - Specialized Care
   - Diagnostic Services
   - Surgical Services
4. Tap on service for details
5. View pricing and availability
6. Read service descriptions

#### **2. Appointment Booking**
**Scenario**: Book appointment as guest

**Mobile Steps**:
1. Tap "Book Appointment"
2. Fill in personal information:
   - Name: "John Doe"
   - Phone: "+251911555666"
   - Email: "john.doe@email.com"
3. Select service: "General Consultation"
4. Choose preferred date and time
5. Add reason for visit
6. Submit appointment request
7. Receive confirmation message

---

## 📊 **COMMON FEATURES ACROSS ALL ROLES**

### **Export and Print Functions**

#### **Export Data**
1. Navigate to any data table
2. Click "Export" button
3. Choose format: CSV, Excel, or PDF
4. Select date range (if applicable)
5. Click "Download"
6. File downloads to your device

#### **Print Reports**
1. Navigate to desired report/page
2. Click "Print" button
3. Choose print options:
   - Current view only
   - All records
   - Custom date range
4. Preview before printing
5. Send to printer or save as PDF

### **Search and Filter**
1. Use search box at top of any list
2. Enter keywords (name, ID, date, etc.)
3. Use advanced filters:
   - Date ranges
   - Status filters
   - Category filters
4. Results update automatically
5. Clear filters to reset view

### **Notifications**
- **Web**: Bell icon in header shows notifications
- **Mobile**: Push notifications and in-app alerts
- Click/tap to view details
- Mark as read or take action

---

## 🔧 **TROUBLESHOOTING GUIDE**

### **Common Issues and Solutions**

#### **Login Problems**
- **Issue**: Cannot login
- **Solution**: 
  1. Verify email and password
  2. Check caps lock
  3. Try password reset
  4. Contact system administrator

#### **Data Not Loading**
- **Issue**: Pages loading slowly or not at all
- **Solution**:
  1. Check internet connection
  2. Refresh page/app
  3. Clear browser cache
  4. Try different browser/device

#### **Permission Denied**
- **Issue**: Cannot access certain features
- **Solution**:
  1. Verify your role and permissions
  2. Contact administrator for access
  3. Ensure you're logged in correctly

#### **Mobile App Issues**
- **Issue**: App crashes or freezes
- **Solution**:
  1. Force close and restart app
  2. Update to latest version
  3. Restart device
  4. Reinstall app if needed

---

## 📞 **SUPPORT AND CONTACT**

### **Technical Support**
- **Email**: support@gerayehealthcare.com
- **Phone**: +251-911-000-000
- **Hours**: 24/7 for critical issues

### **Training and Help**
- **User Manual**: Available in app help section
- **Video Tutorials**: Access through help menu
- **Live Chat**: Available during business hours
- **Training Sessions**: Contact admin for scheduling

---

## 🎯 **QUICK REFERENCE CARDS**

### **Admin Quick Actions**
- Add Patient: Patients → Add Patient
- Schedule Appointment: Appointments → New
- Process Payment: Billing → New Invoice
- Generate Report: Reports → Select Type

### **Doctor Quick Actions**
- Start Visit: Patients → Today's Appointments
- Write Prescription: Visit → Prescriptions
- Update Records: Medical Records → Add Entry
- Schedule Follow-up: Appointments → New

### **Nurse Quick Actions**
- Record Vitals: Patients → Vital Signs
- Administer Medication: Medications → Due Now
- Update Care Plan: Patients → Care Plans
- Send Alert: Communications → Alert Doctor

---

This comprehensive guide covers all major workflows and scenarios for each user role across both web and mobile platforms. Each section includes step-by-step instructions with realistic test data to help users understand and practice the system functionality.
