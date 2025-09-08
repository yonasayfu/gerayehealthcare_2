# Phase 8 — Storage & Assets

> Architecture alignment: storage strategy per module, retention rules, and local assets for resilience.

Define storage strategy, remove cruft, and localize critical assets.

---

- [x] **Understand framework & debugbar storage** (prio:P3)  
  *Task:* Document roles of `storage/framework` and `storage/debugbar`; ensure proper `.gitignore`.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/storage/framework`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/storage/debugbar`

- [x] **Public storage management** (prio:P2)  
  *Task:* Clean `public/storage` targets; organize `storage/app/public` per module and retention rules.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/public/storage`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/storage/app/public`

- [x] **Fonts & asset localization** (prio:P3)  
  *Task:* Remove unused fonts; prefer local assets in `app.blade.php` to avoid CDN issues.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/public/fonts`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/resources/views/app.blade.php`

---

### Clean Architecture Alignment
- Services define where files live; controllers never access filesystem directly beyond download responses.

### OriginalIssue Traceability
- storage/framework & debugbar: item 9
- storage/app/public organization and public/storage cleanup: items 10, 86
- fonts and local asset strategy: items 17, 87

## Commit & Push
1. `git checkout -b fix/phase-8-storage-assets`  
2. Commit: `chore(storage): organize public & app storage; localize assets; remove unused fonts`  
3. `git push origin fix/phase-8-storage-assets`

**Reminder:** Document storage folders and retention in `STORAGE.md`.
