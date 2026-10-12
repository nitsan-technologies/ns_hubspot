# ns_hubspot — Session state

## 2026-10-12 — activation test, ns_license integration, premium strip, context

- Live test on TYPO3 14.3.7 classic mode with a local fake HubSpot endpoint: submit, disabled, error
  responses, settings precedence (results in `features/hubspot-finisher.md`, `core.md`).
- Branch `feature/ns-license-premium-strip` (from `main` @ 98f1e23), not committed yet.
- Added `nitsan/ns-license ^14.5.4` / `ns_license 14.5.4-14.9.99`.
- Premium strip at the top of the form editor canvas; trial deep link verified. First placed in the form
  settings panel, moved to the canvas on request. A system-maintainer-only check was added and then removed
  on request, so all form editor users see it (same as ns_feedback).
- Created agent context (`AGENTS.md`, `CLAUDE.md`, `context/`, `tasks/`); no previous context existed.
- Open: ns_license 14.5.4 not yet published; TYPO3 13 not tested.
