Storage Strategy

Overview
- public/storage is the symlink to storage/app/public created by `php artisan storage:link`.
- storage/framework is for compiled views, cache, and sessions. Never commit.
- storage/debugbar is runtime output from Laravel Debugbar. Never commit.

Module-oriented folders (under storage/app/public)
- patients/: uploads related to patient records
- staff/: staff profile docs/photos
- invoices/: generated PDFs or CSVs for invoices
- shared-invoices/: shared exports
- prescriptions/: attachments for prescriptions
- referral-documents/: uploaded referral docs
- events/: event assets
- marketing/: marketing attachments
- inventory/: inventory item images and exports
- suppliers/: supplier attachments
- partners/: partner docs

Retention guidance
- Exports (CSV/PDF) older than 90 days may be pruned by a scheduled job.
- User-uploaded originals (e.g., referral documents) should be retained per policy (e.g., 7 years) unless deleted by authorized action.
- Temporary files live under storage/app/tmp and are purged daily.

Git ignore
- Default Laravel .gitignore covers storage/* except necessary placeholders. Do not commit runtime files.

Local assets
- Fonts are bundled locally in public/fonts and loaded via @font-face in resources/css/app.css to avoid CDN reliance.
