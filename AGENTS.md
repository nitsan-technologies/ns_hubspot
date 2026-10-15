# AGENTS.md — ns_hubspot (TYPO3 HubSpot Integration)

Agent router for the HubSpot free extension. Load only what your task needs.

> **Principles:** `context/principles.md`
> **Session log:** `context/session-state.md`
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | Guardrails, "nothing that works may break" rule |
| `context/core.md` | Identity, TYPO3/PHP/ns_license constraints, tooling, known issues |

---

## Task router

| Task | Load |
|---|---|
| Form editor options, HubSpot finisher, field mapping, API call, settings (site set / TypoScript) | `context/features/hubspot-finisher.md` |
| Premium strip / free trial / ns_license dependency | `context/features/premium-cta.md` |
| Files, registration, form YAML, assets | `context/architecture.md` |
| Lint / validate / manual test | `tasks/run-quality.md` |
| Update agent context after a change | `tasks/context-update.md` |

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| HubSpot finisher: sends EXT:form submissions to the HubSpot Forms v3 API | Done | `context/features/hubspot-finisher.md` |
| Form editor options: Enable HubSpot, Portal ID, Form Id, per-field "HubSpot Value" | Done | `context/features/hubspot-finisher.md` |
| Premium strip in the form editor + ns_license free-trial deep link | Done (needs ns_license 14.5.4 release) | `context/features/premium-cta.md` |

There is **no plugin, content element or backend module**. The extension only extends EXT:form.

---

## Quick commands

```bash
cd packages/ns_hubspot             # or typo3conf/ext/ns_hubspot in Classic mode
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done
composer validate --no-check-publish
xmllint --noout Resources/Private/Language/locallang.xlf
```
