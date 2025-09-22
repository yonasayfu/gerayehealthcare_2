Security & RBAC

Roles
- Super Admin, Admin, CEO, COO, Doctor, Nurse, Caregiver, Staff, Guest.

Access (high-level)
- Admin/Super Admin: full access to admin modules.
- Staff: limited access to own resources (my visits, earnings, availability).
- Partners: specific modules as required by policies.

Policies & Middleware
- Policies in app/Policies; enforced via `can:` middleware in routes.
- Sensitive routes grouped under auth+verified; API protected via Sanctum.

Auth
- Login/Register pages use AuthGlassLayout. Password reset via built-in routes.
