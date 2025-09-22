# Geraye Healthcare - Module-Specific Scenarios Guide

## 📋 **Detailed Module Workflows by Role**

This guide provides comprehensive, step-by-step scenarios for each module, organized by user role with realistic test data and expected outcomes.

---

## 🏥 **PATIENT MANAGEMENT MODULE**

### **👨‍💼 Admin Role - Patient Management**

#### **Scenario 1: Complete Patient Registration Process**

**Objective**: Register a new patient with complete medical and insurance information

**Prerequisites**: 
- Admin user logged in
- Insurance company "Ethiopian Insurance" exists in system
- Staff member available for assignment

**Step-by-Step Process**:

1. **Navigate to Patient Module**
   - Click "Patients" in sidebar
   - Click "All Patients"
   - Verify current patient count (note for testing)

2. **Start New Patient Registration**
   - Click "Add Patient" button
   - Form opens with multiple tabs

3. **Fill Personal Information Tab**
   ```
   First Name: Almaz
   Last Name: Tadesse
   Date of Birth: 1990-03-15
   Gender: Female
   Phone: +251911234567
   Email: almaz.tadesse@email.com
   National ID: ET1234567890
   ```

4. **Fill Address Information Tab**
   ```
   Street Address: Bole Road, House #123
   City: Addis Ababa
   Region: Addis Ababa
   Postal Code: 1000
   Country: Ethiopia
   ```

5. **Fill Emergency Contact Tab**
   ```
   Contact Name: Dawit Tadesse
   Relationship: Brother
   Phone: +251911234568
   Email: dawit.tadesse@email.com
   Address: Same as patient
   ```

6. **Fill Medical Information Tab**
   ```
   Blood Type: A+
   Known Allergies: Penicillin, Shellfish
   Current Medications: Multivitamin daily
   Medical History: Hypertension (2018), Appendectomy (2015)
   Primary Care Physician: Dr. Sarah Johnson
   ```

7. **Fill Insurance Information Tab**
   ```
   Insurance Provider: Ethiopian Insurance Corporation
   Policy Number: EIC2024001234
   Group Number: GRP789
   Policy Holder: Self
   Coverage Type: Comprehensive
   Effective Date: 2024-01-01
   Expiry Date: 2024-12-31
   ```

8. **Save and Verify**
   - Click "Save Patient"
   - System generates Patient ID: PAT-2024-001
   - Verify patient appears in patient list
   - Check patient card generation

**Expected Results**:
- Patient successfully created with ID PAT-2024-001
- All information saved correctly
- Patient card generated and printable
- Patient appears in searchable patient list
- System sends welcome SMS/email (if configured)

**Test Verification**:
- Search for patient by name: "Almaz Tadesse"
- Verify all tabs contain correct information
- Check patient count increased by 1
- Verify insurance information is linked correctly

---

#### **Scenario 2: Patient Information Update**

**Objective**: Update existing patient information and track changes

**Prerequisites**: Patient "Almaz Tadesse" exists in system

**Step-by-Step Process**:

1. **Locate Patient**
   - Navigate to "Patients" → "All Patients"
   - Search for "Almaz Tadesse"
   - Click on patient name or "Edit" button

2. **Update Contact Information**
   ```
   New Phone: +251911234569
   New Email: almaz.new@email.com
   New Address: Kazanchis, Building #456, Apt 3B
   ```

3. **Update Medical Information**
   ```
   Add New Allergy: Latex
   Update Current Medications: Multivitamin daily, Lisinopril 5mg
   Add Recent Medical History: Annual checkup (2024-01-15)
   ```

4. **Update Insurance Information**
   ```
   New Policy Number: EIC2024001235
   Updated Coverage: Premium Plan
   ```

5. **Save Changes and Document**
   - Click "Save Changes"
   - Add update reason: "Patient requested contact update"
   - System logs all changes with timestamp

**Expected Results**:
- All changes saved successfully
- Change history logged with admin user and timestamp
- Updated information reflects immediately
- Patient receives notification of changes (if configured)

---

### **👩‍⚕️ Doctor Role - Patient Management**

#### **Scenario 3: Patient Medical Record Management**

**Objective**: Access patient records, add medical notes, and update treatment plans

**Prerequisites**: 
- Doctor logged in
- Patient "Almaz Tadesse" has scheduled appointment
- Previous medical records exist

**Step-by-Step Process**:

1. **Access Patient from Schedule**
   - Navigate to "Patients" → "Today's Appointments"
   - Click on "Almaz Tadesse - 2:00 PM"
   - Review appointment details

2. **Review Medical History**
   - Click "Medical History" tab
   - Review previous visits:
     - Last visit: 2023-12-15 (Annual checkup)
     - Diagnosis: Hypertension, well controlled
     - Current medications: Lisinopril 5mg
   - Check allergy alerts (Penicillin, Shellfish, Latex)

3. **Start New Visit Documentation**
   - Click "Start Visit" button
   - Visit automatically timestamped: 2024-01-20 14:00

4. **Record Chief Complaint**
   ```
   Chief Complaint: "Headaches for past week, blood pressure concerns"
   History of Present Illness: Patient reports daily headaches, worse in morning, 
   associated with dizziness. Concerned about blood pressure control.
   ```

5. **Document Physical Examination**
   ```
   Vital Signs:
   - Blood Pressure: 150/95 mmHg
   - Heart Rate: 78 bpm
   - Temperature: 98.6°F (37°C)
   - Respiratory Rate: 16/min
   - Weight: 68 kg
   - Height: 165 cm
   - BMI: 25.0

   Physical Exam:
   - General: Alert, oriented, appears well
   - HEENT: No acute distress, pupils equal and reactive
   - Cardiovascular: Regular rate and rhythm, no murmurs
   - Respiratory: Clear to auscultation bilaterally
   - Neurological: No focal deficits
   ```

6. **Assessment and Plan**
   ```
   Assessment:
   1. Hypertension, poorly controlled (I10)
   2. Headache, likely secondary to hypertension (R51)

   Plan:
   1. Increase Lisinopril to 10mg daily
   2. Add Amlodipine 5mg daily
   3. Home blood pressure monitoring
   4. Follow-up in 2 weeks
   5. Lifestyle counseling: low sodium diet, regular exercise
   ```

7. **Generate Prescription**
   - Click "Create Prescription"
   - Add medications:
     ```
     1. Lisinopril 10mg tablets
        - Quantity: 30 tablets
        - Directions: Take 1 tablet daily in morning
        - Refills: 2
     
     2. Amlodipine 5mg tablets
        - Quantity: 30 tablets
        - Directions: Take 1 tablet daily
        - Refills: 2
     ```

8. **Schedule Follow-up**
   - Click "Schedule Follow-up"
   - Date: 2 weeks from today
   - Time: 2:00 PM
   - Type: Blood pressure check
   - Duration: 15 minutes

9. **Complete Visit**
   - Click "Complete Visit"
   - Generate visit summary
   - Print prescription for patient
   - Send visit notes to patient portal

**Expected Results**:
- Complete visit documentation saved
- Prescription generated and sent to pharmacy
- Follow-up appointment scheduled
- Patient medical record updated
- Visit summary available for patient
- Insurance claim information prepared

---

## 💊 **PRESCRIPTION MANAGEMENT MODULE**

### **👩‍⚕️ Doctor Role - Prescription Workflow**

#### **Scenario 4: Complete Prescription Management**

**Objective**: Create, modify, and track prescriptions with drug interaction checking

**Prerequisites**: 
- Patient "Almaz Tadesse" visit in progress
- Drug database updated with interaction data
- Pharmacy integration configured

**Step-by-Step Process**:

1. **Access Prescription Module**
   - From patient visit, click "Prescriptions"
   - Review current medications:
     - Lisinopril 5mg (current)
     - Multivitamin (current)

2. **Check Drug Interactions**
   - Click "Check Interactions"
   - System scans current medications
   - Review interaction warnings
   - No major interactions found

3. **Create New Prescription**
   - Click "Add Prescription"
   - Search drug: "Amlodipine"
   - Select: "Amlodipine 5mg tablets"

4. **Configure Prescription Details**
   ```
   Drug: Amlodipine 5mg tablets
   Strength: 5mg
   Quantity: 30 tablets
   Days Supply: 30 days
   Directions: Take 1 tablet by mouth daily
   Refills: 2
   Generic Substitution: Allowed
   ```

5. **Add Drug Interaction Check**
   - System automatically checks against:
     - Current medications
     - Known allergies
     - Patient conditions
   - Warning: "Monitor blood pressure closely"
   - Proceed with caution note added

6. **Add Patient Instructions**
   ```
   Patient Instructions:
   - Take at the same time each day
   - Do not stop suddenly without consulting doctor
   - Monitor for swelling in legs/ankles
   - Report dizziness or fainting
   - Continue low sodium diet
   ```

7. **Electronic Prescription Transmission**
   - Select pharmacy: "Bethel Pharmacy"
   - Verify pharmacy contact: +251911555777
   - Click "Send to Pharmacy"
   - Prescription transmitted electronically

8. **Generate Patient Copy**
   - Click "Print Patient Copy"
   - Include medication guide
   - Add doctor contact information
   - Print prescription slip

**Expected Results**:
- Prescription created and saved in patient record
- Electronic transmission to pharmacy successful
- Patient copy printed with instructions
- Drug interaction check completed and documented
- Prescription tracking number generated: RX-2024-001234

---

## 📅 **APPOINTMENT SCHEDULING MODULE**

### **👨‍💼 Admin Role - Appointment Management**

#### **Scenario 5: Complex Appointment Scheduling**

**Objective**: Schedule multiple appointment types with resource management

**Prerequisites**:
- Multiple doctors available
- Different appointment types configured
- Room/resource scheduling enabled

**Step-by-Step Process**:

1. **Access Scheduling Module**
   - Navigate to "Appointments" → "Schedule"
   - View weekly calendar
   - Check doctor availability

2. **Schedule Regular Consultation**
   ```
   Patient: Almaz Tadesse
   Doctor: Dr. Sarah Johnson
   Date: Tomorrow
   Time: 2:00 PM - 2:30 PM
   Type: Follow-up Consultation
   Room: Consultation Room 1
   Reason: Blood pressure follow-up
   ```

3. **Schedule Diagnostic Appointment**
   ```
   Patient: Almaz Tadesse
   Service: ECG Test
   Technician: David Kim
   Date: Same day as consultation
   Time: 1:30 PM - 1:45 PM
   Room: Diagnostic Room A
   Preparation: No special preparation needed
   ```

4. **Schedule Specialist Referral**
   ```
   Patient: Almaz Tadesse
   Specialist: Dr. Ahmed Hassan (Neurologist)
   Date: Next week
   Time: 10:00 AM - 11:00 AM
   Type: Specialist Consultation
   Room: Neurology Suite
   Reason: Headache evaluation
   Referral Required: Yes
   ```

5. **Configure Appointment Reminders**
   - SMS reminder: 24 hours before
   - Email reminder: 2 hours before
   - Phone call: For missed appointments
   - Patient portal notification: Immediate

6. **Handle Appointment Conflicts**
   - System detects doctor double-booking
   - Suggests alternative times:
     - 2:30 PM - 3:00 PM
     - 3:00 PM - 3:30 PM
   - Reschedule conflicting appointment

7. **Generate Appointment Confirmations**
   - Print appointment cards
   - Send SMS confirmations
   - Update patient portal
   - Add to doctor's schedule

**Expected Results**:
- Three appointments scheduled successfully
- No scheduling conflicts
- All reminders configured
- Confirmation sent to patient
- Resources (rooms, equipment) reserved
- Doctor schedules updated

---

## 💰 **BILLING AND PAYMENT MODULE**

### **👨‍💼 Admin Role - Complete Billing Workflow**

#### **Scenario 6: End-to-End Billing Process**

**Objective**: Process complete billing cycle from service to payment

**Prerequisites**:
- Patient visit completed
- Services rendered and documented
- Insurance information verified
- Payment methods configured

**Step-by-Step Process**:

1. **Access Billing Module**
   - Navigate to "Billing" → "Create Invoice"
   - Select patient: "Almaz Tadesse"
   - Select visit date: Today

2. **Add Services to Invoice**
   ```
   Services Rendered:
   1. Office Visit - Follow-up (99213)
      - Amount: $150.00
      - Provider: Dr. Sarah Johnson
   
   2. ECG Test (93000)
      - Amount: $75.00
      - Provider: David Kim (Technician)
   
   3. Blood Pressure Monitoring (99401)
      - Amount: $25.00
      - Provider: Nurse Lisa Brown
   ```

3. **Calculate Insurance Coverage**
   ```
   Insurance: Ethiopian Insurance Corporation
   Policy: EIC2024001235
   Coverage: 80% for office visits, 70% for diagnostics
   
   Calculations:
   - Office Visit: $150.00 × 80% = $120.00 covered
   - ECG Test: $75.00 × 70% = $52.50 covered
   - BP Monitoring: $25.00 × 80% = $20.00 covered
   
   Total Charges: $250.00
   Insurance Coverage: $192.50
   Patient Responsibility: $57.50
   ```

4. **Process Insurance Claim**
   - Click "Submit Insurance Claim"
   - Generate claim form
   - Attach supporting documentation:
     - Visit notes
     - ECG results
     - Prescription records
   - Submit electronically to insurance
   - Claim number: CLM-2024-001234

5. **Process Patient Payment**
   - Patient payment due: $57.50
   - Payment method: Credit card
   - Process payment:
     ```
     Amount: $57.50
     Card: **** **** **** 1234
     Authorization: AUTH123456
     Transaction ID: TXN789012
     ```

6. **Generate Receipt and Documentation**
   - Print patient receipt
   - Email invoice copy
   - Update patient account
   - Record payment in system

7. **Track Outstanding Balances**
   - Insurance pending: $192.50
   - Patient paid: $57.50
   - Total collected: $57.50
   - Outstanding: $192.50 (pending insurance)

**Expected Results**:
- Invoice created: INV-2024-001234
- Insurance claim submitted successfully
- Patient payment processed
- Receipt generated and provided
- Account balance updated
- Payment tracking initiated

---

## 📊 **REPORTING AND ANALYTICS MODULE**

### **🏢 CEO Role - Executive Reporting**

#### **Scenario 7: Monthly Executive Dashboard Review**

**Objective**: Generate comprehensive executive reports for board presentation

**Prerequisites**:
- One month of operational data
- All modules have recorded transactions
- Financial data is current
- Staff performance data available

**Step-by-Step Process**:

1. **Access Executive Dashboard**
   - Login as CEO
   - Navigate to "Analytics" → "Executive Dashboard"
   - Select date range: "Last 30 days"

2. **Review Key Performance Indicators**
   ```
   Financial Metrics:
   - Total Revenue: $125,000
   - Net Profit: $35,000
   - Profit Margin: 28%
   - Outstanding Receivables: $15,000
   
   Operational Metrics:
   - Total Patients Served: 450
   - New Patient Registrations: 75
   - Average Visit Duration: 25 minutes
   - Patient Satisfaction Score: 4.7/5.0
   
   Staff Metrics:
   - Staff Utilization Rate: 85%
   - Average Response Time: 12 minutes
   - Staff Satisfaction: 4.2/5.0
   - Overtime Hours: 120 hours
   ```

3. **Generate Department Performance Report**
   ```
   Cardiology Department:
   - Patients: 120
   - Revenue: $45,000
   - Satisfaction: 4.8/5.0
   
   Emergency Department:
   - Patients: 180
   - Revenue: $35,000
   - Average Wait Time: 15 minutes
   
   Laboratory:
   - Tests Processed: 350
   - Turnaround Time: 2.5 hours
   - Quality Score: 99.2%
   ```

4. **Create Trend Analysis**
   - Compare with previous month
   - Identify growth patterns
   - Highlight areas of concern
   - Generate forecasts

5. **Export Executive Summary**
   - Click "Generate Executive Report"
   - Select format: PowerPoint presentation
   - Include charts and graphs
   - Add executive summary text
   - Export for board meeting

**Expected Results**:
- Comprehensive executive dashboard displayed
- All KPIs calculated and current
- Trend analysis completed
- Executive report generated: EXEC-RPT-2024-01
- Data ready for board presentation

---

## 🏥 **INVENTORY MANAGEMENT MODULE**

### **⚙️ COO Role - Inventory Operations**

#### **Scenario 8: Complete Inventory Management Cycle**

**Objective**: Manage inventory from procurement to usage tracking

**Prerequisites**:
- Supplier relationships established
- Inventory items configured
- Reorder levels set
- Staff trained on inventory procedures

**Step-by-Step Process**:

1. **Review Inventory Status**
   - Navigate to "Inventory" → "Items"
   - Check low stock alerts:
     ```
     Critical Items:
     - Surgical Gloves: 15 units (Reorder: 50)
     - Syringes 5ml: 25 units (Reorder: 100)
     - Blood Test Tubes: 30 units (Reorder: 200)
     ```

2. **Create Purchase Request**
   - Click "Create Request"
   - Select items for reorder:
     ```
     Request #: REQ-2024-001
     
     Items:
     1. Surgical Gloves (Box of 100)
        - Current Stock: 15
        - Reorder Quantity: 200 boxes
        - Supplier: MedSupply Ethiopia
        - Unit Cost: $25.00
        - Total: $5,000.00
     
     2. Syringes 5ml (Pack of 100)
        - Current Stock: 25
        - Reorder Quantity: 500 packs
        - Supplier: MedSupply Ethiopia
        - Unit Cost: $15.00
        - Total: $7,500.00
     ```

3. **Process Purchase Order**
   - Review and approve request
   - Generate purchase order: PO-2024-001
   - Send to supplier
   - Set expected delivery date
   - Track order status

4. **Receive Inventory**
   - Delivery arrives: 5 days later
   - Navigate to "Inventory" → "Receiving"
   - Scan delivery receipt
   - Verify quantities:
     ```
     Received:
     - Surgical Gloves: 200 boxes ✓
     - Syringes 5ml: 500 packs ✓
     - Quality check: Passed ✓
     - Expiry dates: Verified ✓
     ```

5. **Update Inventory Records**
   - Click "Receive Items"
   - Update stock levels:
     - Surgical Gloves: 15 + 200 = 215 boxes
     - Syringes: 25 + 500 = 525 packs
   - Generate receiving report
   - Update supplier records

6. **Track Usage and Consumption**
   - Monitor daily usage patterns
   - Set up automated alerts
   - Generate consumption reports
   - Plan future orders

**Expected Results**:
- Purchase request created and approved
- Purchase order sent to supplier
- Inventory received and verified
- Stock levels updated accurately
- Usage tracking initiated
- Supplier performance recorded

---

## 📱 **MOBILE APPLICATION SCENARIOS**

### **👩‍⚕️ Doctor Mobile Workflow**

#### **Scenario 9: Mobile Patient Care**

**Objective**: Complete patient care using mobile application

**Prerequisites**:
- Mobile app installed and logged in
- Patient appointment scheduled
- Mobile device has camera and internet

**Step-by-Step Process**:

1. **Check Daily Schedule**
   - Open mobile app
   - Tap "Schedule" tab
   - View today's appointments:
     ```
     Today's Schedule:
     9:00 AM - John Smith (Checkup)
     10:30 AM - Mary Johnson (Follow-up)
     2:00 PM - Almaz Tadesse (BP Check)
     3:30 PM - David Wilson (Consultation)
     ```

2. **Start Patient Visit**
   - Tap on "Almaz Tadesse - 2:00 PM"
   - Review patient summary
   - Tap "Start Visit"
   - Mobile timer starts automatically

3. **Record Vital Signs**
   - Tap "Vital Signs"
   - Use quick entry interface:
     ```
     Blood Pressure: 145/92 (use number pad)
     Heart Rate: 76 (use number pad)
     Temperature: 98.4°F (use number pad)
     Weight: 68 kg (use number pad)
     ```
   - Tap "Save Vitals"

4. **Document Visit Notes**
   - Tap "Visit Notes"
   - Use voice-to-text feature:
     "Patient reports improved headaches since last visit. Blood pressure still elevated. Compliance with medications good. No side effects reported."
   - Review and edit text
   - Tap "Save Notes"

5. **Take Clinical Photos**
   - Tap "Photos"
   - Take photo of blood pressure reading
   - Add caption: "BP reading 145/92"
   - Save to patient record

6. **Create Prescription**
   - Tap "Prescriptions"
   - Search medication: "Amlodipine"
   - Select dosage: "10mg"
   - Set quantity: "30 tablets"
   - Add instructions using templates
   - Send to pharmacy electronically

7. **Schedule Follow-up**
   - Tap "Schedule Follow-up"
   - Select date: 2 weeks from today
   - Choose available time slot
   - Add appointment notes
   - Send confirmation to patient

8. **Complete Visit**
   - Tap "Complete Visit"
   - Review visit summary
   - Generate patient instructions
   - Send visit summary to patient portal

**Expected Results**:
- Visit completed in 15 minutes
- All data synchronized to main system
- Prescription sent to pharmacy
- Follow-up appointment scheduled
- Patient receives visit summary
- Mobile workflow completed successfully

---

## 🧪 **COMPREHENSIVE TESTING SCENARIOS**

### **Testing Checklist for Each Role**

#### **Super Admin Testing Checklist**
- [ ] Login with superadmin@gerayehealthcare.com
- [ ] Create new user account
- [ ] Assign roles and permissions
- [ ] Configure system settings
- [ ] Generate system backup
- [ ] View all modules and data
- [ ] Export system reports
- [ ] Manage user permissions

#### **CEO Testing Checklist**
- [ ] Login with ceo@gerayehealthcare.com
- [ ] Access executive dashboard
- [ ] View financial analytics
- [ ] Generate executive reports
- [ ] Review performance metrics
- [ ] Export board presentation materials
- [ ] Monitor system health
- [ ] Access strategic planning tools

#### **COO Testing Checklist**
- [ ] Login with coo@gerayehealthcare.com
- [ ] Manage staff schedules
- [ ] Review operational metrics
- [ ] Process inventory requests
- [ ] Approve leave requests
- [ ] Generate operational reports
- [ ] Monitor resource utilization
- [ ] Coordinate department activities

#### **Admin Testing Checklist**
- [ ] Login with admin@gerayehealthcare.com
- [ ] Register new patient (use test data provided)
- [ ] Schedule appointments
- [ ] Process insurance claims
- [ ] Generate invoices
- [ ] Export patient reports
- [ ] Manage appointment calendar
- [ ] Process payments

#### **Doctor Testing Checklist**
- [ ] Login with doctor@gerayehealthcare.com
- [ ] Review today's appointments
- [ ] Complete patient consultation (use scenario data)
- [ ] Write prescriptions
- [ ] Update medical records
- [ ] Schedule follow-up appointments
- [ ] Generate visit summaries
- [ ] Access patient history

#### **Nurse Testing Checklist**
- [ ] Login with nurse@gerayehealthcare.com
- [ ] Record patient vital signs
- [ ] Administer medications
- [ ] Update care plans
- [ ] Communicate with doctors
- [ ] Document patient care
- [ ] Monitor patient status
- [ ] Process lab orders

#### **Mobile Application Testing**
- [ ] Install mobile app
- [ ] Login with different roles
- [ ] Test offline functionality
- [ ] Verify data synchronization
- [ ] Test push notifications
- [ ] Complete mobile workflows
- [ ] Test camera integration
- [ ] Verify responsive design

### **Cross-Platform Testing Scenarios**

#### **Data Synchronization Test**
1. Create patient record on web application
2. Verify record appears in mobile app
3. Update record on mobile app
4. Verify changes reflect on web application
5. Test offline mode on mobile
6. Verify sync when connection restored

#### **Permission Boundary Testing**
1. Login as Staff user
2. Attempt to access Admin functions
3. Verify access denied appropriately
4. Test permission escalation
5. Verify role-based UI elements
6. Test fallback content display

#### **End-to-End Workflow Testing**
1. Guest books appointment (mobile)
2. Admin confirms appointment (web)
3. Doctor conducts visit (web/mobile)
4. Nurse follows up (mobile)
5. Admin processes billing (web)
6. Patient receives summary (mobile)

This comprehensive guide provides detailed, step-by-step scenarios for each major module and user role, with realistic test data and expected outcomes. Each scenario can be used for training, testing, and user acceptance validation.
