# Feature — Premium strip and ns_license free trial

*Status: Done. Verified live on TYPO3 14.3 (2026-10-12). Requires ns_license 14.5.4 (deep-link support) to be released.*

## Licensing status

Freemium per the NITSAN Free/Freemium sheet. Premium product "HubSpot" (T3Planet catalog, €129):
https://t3planet.de/en/typo3-hubspot-extension (not linked from the UI; the trial stays inside the backend).

Premium adds (catalog): HubSpot CRM sync and segmentation, drag-and-drop HubSpot forms, slide-ins and banners,
live chat, email automation, analytics dashboard. The free version only has the form finisher.

## Dependency

`composer.json` `"nitsan/ns-license": "^14.5.4"`, `ext_emconf.php` `'ns_license' => '14.5.4-14.9.99'`.

## Strip

- **Where:** Forms module → form editor, top of the stage (canvas), above the preview/edit and step toolbar
  (`[data-identifier="panelHeading"]`).
- **When:** only while the form's "Enable HubSpot" (`renderingOptions.hubspotEnable`) is on, with the same truthy
  values as `ApiFinisher::isHubSpotEnabled()` (`true`, `1`, `'true'`, `'1'`, `'yes'`, `'on'`). Toggling the
  inspector checkbox adds/removes the strip immediately, without saving (verified live on v14.3).
- **Who:** every backend user who can open the form editor, same as ns_feedback and ns_backup (no
  system-maintainer check, removed on request). The ns_license module itself is system-maintainer only, so other
  users who click "Start Free Trial" get TYPO3's access-denied message.
- Content: `actions-rocket` core icon, headline and description on two lines, `btn btn-primary`
  "Start Free Trial →" (the arrow is `aria-hidden` markup, not part of the label; same tab).
- Wording and design are the team-wide standard from the 2026-10-14 review, identical in every NITSAN strip
  except for the extension name.
- Labels in `locallang.xlf`:
  - `premium.strip.headline`: "You're using the free version of NS HubSpot"
  - `premium.strip.description`: "Unlock premium features and get more out of NS HubSpot."
  - `premium.strip.trial`: "Start Free Trial"
- Look: `.ns-hubspot-premium-strip` (+ `-icon`, `-headline`, `-description`) in
  `Resources/Public/Css/form-editor.css` — light-blue card (`#e7f2fc`, border `#8eb6e8`, 4px `#2b6cb0` left
  accent, 6px radius, padding 14px 18px, text `#0d2c4a`), semibold 1.1em headline, `light-dark()` for dark mode,
  button `#007bff` (hover `#0062cc`, no wrap).

## How it is wired (no core template override)

1. `setup.yaml` adds `NsHubSpot-PremiumStrip: NsHubSpot/PremiumStrip` to `formEditor.formEditorPartials` and
   the extension's partial root path. EXT:form renders it into a `<template data-template-name=…>` on the page.
2. `formEditor.dynamicJavaScriptModules.additionalViewModelModules` loads
   `@nitsan/ns-hubspot/form-editor-premium-strip.js`. Its `bootstrap(formEditorApp)` clones the strip from the
   template once and inserts it before the stage heading (fallback: top of `stageContainerInner`) when
   `formEditorApp.getRootFormElement().get('renderingOptions.hubspotEnable')` is on, otherwise removes it.
   It re-checks (deferred with `setTimeout`) on `core/formElement/somePropertyChanged`, undo/redo clicks and
   stage re-renders. The node is added/removed rather than hidden because Bootstrap's `d-flex !important`
   overrides the `hidden` attribute.
3. `formEditor.stylesheets` loads `form-editor.css`.

The strip is not a form element or inspector editor, so it never changes or dirties the form definition.

## Trial link

`f:be.uri(route: 'nitsan_nslicensemodule', parameters: {trial: 'ns_hubspot'})` + `#free-section`.
ns_license's `maybeOpenTrialDeepLink()` strips `trial`, opens the Extensions catalog tab and the "Get New License"
wizard for `ns_hubspot` (verified). The link navigates the module frame, so the page tree stays the form tree.

Limitation: `f:be.uri` throws if the ns_license route is missing, which would break the form editor; the
declared dependency guarantees the route.
