# Prescription Module Documentation

## Overview
The Prescription module is a comprehensive system for managing patient prescriptions within the Geraye Home Care Services application. It provides full CRUD functionality, sharing capabilities, printing, and exporting features.

## Module Structure

### Backend Components
1. **Models**
   - `Prescription`: Main prescription entity
   - `PrescriptionItem`: Individual medication items within a prescription

2. **Controllers**
   - `PrescriptionController`: Handles all prescription-related operations

3. **Services**
   - `PrescriptionService`: Business logic for prescription management

4. **Database**
   - `prescriptions` table: Stores prescription metadata
   - `prescription_items` table: Stores individual medication items

### Frontend Components
1. **Vue Pages**
   - `Index.vue`: Prescription listing with filtering and pagination
   - `Show.vue`: Detailed prescription view with sharing options
   - `Create.vue`: Form for creating new prescriptions
   - `Edit.vue`: Form for editing existing prescriptions
   - `Form.vue`: Shared form component used by Create and Edit

## CRUD Operations

### Create
- Accessible via "Add Prescription" button on the Index page
- Form includes:
  - Patient selection
  - Prescription date
  - Status (draft, final, dispensed, cancelled)
  - Instructions
  - Medication items (medication name, dosage, frequency, duration, notes)

### Read
- **Index View**: Paginated list with search, status filtering, and sorting
- **Show View**: Detailed view with all prescription information and items

### Update
- Edit functionality available from both Index and Show views
- Full form editing including medication items

### Delete
- Delete functionality with confirmation modal
- Available from Index view

## Sharing Functionality

### Features
1. **Share Links**: Generate secure URLs for external access
2. **Expiration**: Links expire after 30 days by default
3. **PIN Protection**: Optional PIN security for sensitive prescriptions
4. **View Tracking**: Track how many times a prescription has been viewed
5. **Link Rotation**: Generate new share tokens while invalidating old ones
6. **Expiration Control**: Manually expire share links

### Implementation
- **Backend**: Token-based sharing with expiration dates
- **Frontend**: Share dropdown with multiple options:
  - Copy Link
  - WhatsApp
  - Twitter
  - Telegram

### Public Access
- Public URLs: `/public/prescriptions/{token}`
- PIN authentication when enabled
- View counting and tracking

## Print Functionality

### Professional A5 Layout
- Optimized for medical prescription printing
- Clean, professional design with clinic branding
- Responsive layout for both screen and print

### Print Options
1. **Print Current**: Print the current view from Index
2. **Print Single**: Print individual prescription from Show view
3. **Print All**: Print all prescriptions (with filters applied)

## Export Functionality

### CSV Export
- Export current view with all filters applied
- Includes all prescription data and item details

## Relationships

### Prescription Model Relationships
1. **Patient**: Belongs to Patient model
2. **Medical Document**: Optional link to MedicalDocument
3. **Created By**: Belongs to Staff member
4. **Items**: Has many PrescriptionItems

### PrescriptionItem Model Relationships
1. **Prescription**: Belongs to Prescription

## Security Features

### Access Control
- Role-based permissions for viewing, creating, editing, and deleting
- Protected public access with token authentication
- Optional PIN protection for sensitive prescriptions

### Data Protection
- Secure token generation for sharing
- Expiration dates for share links
- Session-based PIN authentication

## Integration Points

### With Other Modules
1. **Patients**: Prescriptions are linked to patient records
2. **Staff**: Track which staff member created each prescription
3. **Medical Documents**: Optional linking to medical documents
4. **Reports**: Prescription data available in reporting modules

## Routes

### Admin Routes
- `GET /admin/prescriptions` - Index
- `GET /admin/prescriptions/create` - Create form
- `POST /admin/prescriptions` - Store new prescription
- `GET /admin/prescriptions/{prescription}` - Show
- `GET /admin/prescriptions/{prescription}/edit` - Edit form
- `PUT/PATCH /admin/prescriptions/{prescription}` - Update
- `DELETE /admin/prescriptions/{prescription}` - Delete
- `GET /admin/prescriptions/export` - Export CSV
- `GET /admin/prescriptions/print-all` - Print all
- `GET /admin/prescriptions/print-current` - Print current view
- `GET /admin/prescriptions/{prescription}/print` - Print single
- `GET /admin/prescriptions/{prescription}/share-link` - Generate share link
- `POST /admin/prescriptions/{prescription}/rotate-share` - Rotate share link
- `POST /admin/prescriptions/{prescription}/expire-share` - Expire share link
- `POST /admin/prescriptions/{prescription}/set-pin` - Set share PIN

### Public Routes
- `GET /public/prescriptions/{token}` - View shared prescription
- `POST /public/prescriptions/{token}/authenticate` - Authenticate with PIN
- `GET /public/prescriptions/{token}/pdf` - Download as PDF

## Enhancement Recommendations

### 1. UI/UX Improvements
- Add status badges with color coding in Index view
- Implement bulk actions for prescriptions
- Add prescription templates for common medications
- Include medication search/dropdown for standardized names

### 2. Sharing Enhancements
- Email sharing option
- QR code generation for prescriptions
- SMS sharing integration
- Share history tracking

### 3. Reporting Features
- Prescription analytics dashboard
- Medication usage statistics
- Doctor prescribing patterns
- Patient medication history

### 4. Integration Improvements
- Pharmacy system integration
- Drug interaction checking
- Insurance claim integration
- Electronic prescription transmission

### 5. Mobile Optimization
- Dedicated mobile view for prescriptions
- Camera integration for medication scanning
- Offline prescription creation
- Mobile-friendly sharing options

## Technical Considerations

### Performance
- Pagination for large datasets
- Efficient database indexing
- Eager loading of relationships
- Caching for frequently accessed data

### Security
- Input validation and sanitization
- CSRF protection
- Secure token generation
- Rate limiting for public endpoints

### Maintainability
- Modular code structure
- Comprehensive service layer
- Clear separation of concerns
- Extensive documentation

## Future Development

### Planned Features
1. Prescription renewal system
2. Medication reminder notifications
3. Integration with national prescription databases
4. Advanced search and filtering options
5. Prescription comparison tools
6. Multi-language support

### Technical Debt
1. Consider implementing a more robust medication database
2. Add unit tests for prescription service methods
3. Implement audit logging for prescription changes
4. Add API endpoints for mobile applications

## Conclusion

The Prescription module provides a solid foundation for managing patient prescriptions with comprehensive features for healthcare providers. The module is well-integrated with other system components and provides both administrative and public access capabilities. With the recommended enhancements, it can become a world-class prescription management system.