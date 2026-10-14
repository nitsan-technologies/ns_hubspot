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

## 2026-10-14

- Premium strip now shows only while "Enable HubSpot" is on for the form (live toggle in the inspector, no save
  needed). Verified on v14.3 with `hubspotTest` (on → strip) and `mailSenderDemo` (off → none, toggle on/off/on
  → 1/0/1 strips).
- Premium strip colours changed from grey to light blue (same palette as ns_revolution_slider). Icon changed from star to rocket (`actions-rocket`). Design aligned with the ns_backup `feature/ns-license-premium-strip-highlight` strip (4px left accent, 10px padding). Headline now "You are using the free version" with an "Upgrade to Premium for ..." description.
