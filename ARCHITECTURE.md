Architecture Overview

Layers
- DTOs: app/DTOs – input contracts for create/update actions.
- Services: app/Services – business logic; controllers remain thin.
- Controllers: app/Http/Controllers – orchestrate requests, call services, return Inertia/Resources.
- Requests/Rules: app/Http/Requests, app/Services/Validation/Rules – validation.
- Policies: app/Policies – authorization; used via can: middleware.
- Notifications/Events: app/Notifications, app/Events – event-driven comms.
- Exports/Print: app/Http/Traits/ExportableTrait + app/Http/Config/ExportConfig.

Routing
- Web: grouped under /dashboard with name('admin.'); staff under name('staff.').
- API: /api/v1 protected by Sanctum; Resources for responses.
See ROUTING.md for naming and grouping conventions.

Exports/Print
- Centralized via ExportableTrait; configs in ExportConfig/AdditionalExportConfigs.
- Shared layouts: resources/views/pdf-layout.blade.php, print-layout.blade.php.

Storage
- See STORAGE.md. Public assets localized; fonts via @font-face.
