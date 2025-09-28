Refer to the corresponding Markdown file for the current phase (e.g., phases/phase-N-frontend.md). Work through all items in sequence. Prefer to complete the entire phase in one go. When finished, provide a summary of changes and testing steps. If doing the whole phase at once isn’t practical, propose a short sub-roadmap and proceed step by step. Do not skip or jump between phases. After completing the phase, commit your changes and push to origin, then return with the PR link and summary.



Instruction to AI Agents

Start by reading the project README.md (root) for overall context and conventions.

Then open phases/README-ISSUE.md,GERAYE_HEALTHCARE_COMPLETE_USER_GUIDE.md, DOCUMENTATION_INDEX.md, RBAC_AND_UI_CONSISTENCY_GUIDE.md,MODULE_SPECIFIC_SCENARIOS_GUIDE.md  to see the phase index and working rules.

Move to the current phase file phases/phase-0.md. Work through all tasks in order.

Prefer to complete the entire phase in one go. If that’s not practical, propose a very short sub-roadmap and proceed stepwise.

Use the referenced paths in each task to locate and modify the correct files.

Before committing, run:

./vendor/bin/pint (PHP auto-format)

./vendor/bin/phpstan analyse (PHP static analysis)

npm run lint (ESLint/TS/Vue lint)


After finishing the phase:

Check off items (- [x]) in the phase file.

Provide a summary of changes and testing steps I can follow.

Commit and push to origin using the branch name suggested in the phase file, and share the PR link.

Do not skip or jump phases. When a phase is merged, return to phases/README-ISSUE.md to identify the next phase.