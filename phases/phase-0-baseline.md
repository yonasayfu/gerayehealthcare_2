# Phase 0 — Baseline & Housekeeping

This phase hardens the foundation: branch strategy + CI, and removal of dev-only artifacts.

---

- [x] **Create baseline branch & CI checks** (prio:P1) (type:chore)  
  *Task:* Create a dedicated branch and wire CI linters/tests for PHP/TS.  
  *Why:* Guarantees code quality and reproducibility for all following phases.  
  *Definition of Done (DoD):*  
    - New branch: `fix/phase-1-db`.  
    - CI runs: PHPStan, Laravel Pint, Pest/PHPUnit, ESLint/TypeScript, Prettier.  
    - CI passes for PRs.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/composer.json`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/package.json`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/phpunit.xml`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/.github/workflows/*` (to be created)

---

- [x] **Remove dev-only performance/test routes & files** (prio:P1) (area:route|cleanup) (type:cleanup)  
  *Task:* Delete or guard test/perf artifacts.  
  *Why:* Prevents accidental exposure in production and reduces noise.  
  *Definition of Done (DoD):*  
    - Files removed or wrapped in `if (app()->environment('local'))`:  
      - `routes/performance.php`  
      - `routes/performance-test.php`  
      - `public/performance-test.html`  
    - Any perf blocks in `routes/web.php` are guarded or removed.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/routes/  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/routes/performance.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/routes/performance-test.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/public/performance-test.html`

---

## Commit & Push
1. `git checkout -b fix/phase-0-baseline`  
2. Commit with message: `chore: setup CI and remove dev-only perf routes/files`  
3. `git push origin fix/phase-0-baseline`

**Reminder:** Open a PR titled “Phase 0 — Baseline & Housekeeping” and ensure CI is green before merge.
