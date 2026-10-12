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
  (`[data-identifier="panelHeading"]`). Shown once per form editor load, for every form.
- **Who:** every backend user who can open the form editor, same as ns_feedback and ns_backup (no
  system-maintainer check, removed on request). The ns_license module itself is system-maintainer only, so other
  users who click "Start Free Trial" get TYPO3's access-denied message.
- Content: `actions-star` core icon, headline + one-line description, `btn btn-primary` "Start Free Trial"
  (same tab).
- Labels in `locallang.xlf`: `premium.strip.headline` "HubSpot Premium", `premium.strip.description`
  "Get more out of your HubSpot integration with the premium version. Try it free.",
  `premium.strip.trial` "Start Free Trial".
- Look: gray bar like the ns_backup / ns_feedback strip (`light-dark()` colours, `8px 16px` padding,
  button `#007bff` / hover `#0062cc`), in `Resources/Public/Css/form-editor.css`.

## How it is wired (no core template override)

1. `setup.yaml` adds `NsHubSpot-PremiumStrip: NsHubSpot/PremiumStrip` to `formEditor.formEditorPartials` and
   the extension's partial root path. EXT:form renders it into a `<template data-template-name=…>` on the page.
2. `formEditor.dynamicJavaScriptModules.additionalViewModelModules` loads
   `@nitsan/ns-hubspot/form-editor-premium-strip.js`. Its `bootstrap()` clones the strip from the template
   before the stage heading (fallback: top of `stageContainerInner`; nothing if neither exists).
3. `formEditor.stylesheets` loads `form-editor.css`.

The strip is not a form element or inspector editor, so it never changes or dirties the form definition.

## Trial link

`f:be.uri(route: 'nitsan_nslicensemodule', parameters: {trial: 'ns_hubspot'})` + `#free-section`.
ns_license's `maybeOpenTrialDeepLink()` strips `trial`, opens the Extensions catalog tab and the "Get New License"
wizard for `ns_hubspot` (verified). The link navigates the module frame, so the page tree stays the form tree.

Limitation: `f:be.uri` throws if the ns_license route is missing, which would break the form editor; the
declared dependency guarantees the route.
