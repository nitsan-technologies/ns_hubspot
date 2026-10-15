# ns_hubspot — Operating Principles

*Every change to code, context or docs must serve the goals below.*

---

## Goal 1 — Nothing that works may break

The extension is declared for TYPO3 v13 and v14 at the same time (Composer and Classic mode).

- Every change must keep both majors working, in the form editor (backend) and on form submission (frontend).
- Existing form definitions store `renderingOptions.hubspotEnable/hubspotPortalId/hubspotFormId` and
  `properties.hubSpotValue`. Never rename these keys without a migration.
- Prefer the smallest change that fixes the root cause. No drive-by refactors.
- Run `tasks/run-quality.md` and re-test the affected flow before claiming done.

## Goal 2 — Token-cost efficiency (agent context)

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load on demand:** read `context/features/<area>.md` for the task, not everything.
- **Prune as you go:** keep `context/session-state.md` short.

## Goal 3 — Accuracy and trust

- Answer from context + code, never from invented API, setting or field names.
- Conflicting facts across files = fix the source file.

---

## Guardrails (non-negotiable)

- Never commit or log a HubSpot token (`client_apikey`). Test against a local fake endpoint, not the real
  `api.hsforms.com`. Note that the TypoScript `apiURL` wins over the site setting (`features/hubspot-finisher.md`).
- Keep the TypoScript constants and the site set settings (`Configuration/Sets/Ns_HubSpot/`) in sync when
  adding or renaming a setting.
- `Configuration/Form/setup.yaml` is merged into **every** form of the installation. Anything added there
  (classes, editors, persistence paths) affects all forms, not only HubSpot ones.
- Do not change ns_license from an ns_hubspot task; reuse its module and trial modal.
- All UI text goes into `Resources/Private/Language/locallang.xlf`, no hard-coded labels.

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature or bugfix (update the matching `context/features/*.md` + `session-state.md`)
