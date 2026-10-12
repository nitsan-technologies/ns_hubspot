# ns_hubspot — Architecture

No plugin, no content element, no backend module, no database tables. Everything hangs off EXT:form.

## Registration of the form YAML (`Configuration/Form/setup.yaml`)

| Where | Key | Scope |
|---|---|---|
| `ext_localconf.php` → `addTypoScriptSetup()` | `module.tx_form.settings.yamlConfigurations.1732785702` | Backend form editor, always (no site needed) |
| `Configuration/TypoScript/Extensions/Form/setup.typoscript` | `plugin.tx_form…104` + `module.tx_form…104` | Frontend + backend, only via static template or site set |

Both mechanisms are deprecated since TYPO3 14.2 (`core.md`).

## What `setup.yaml` adds (prototype `standard`)

| Part | Content |
|---|---|
| `finishersDefinition.HubSpotFinisher` | `Nitsan\NsHubSpot\Finishers\ApiFinisher`, label "HubSpot Finisher", no options |
| `Form.formEditor.editors` 500/510/520 | Form-level "Enable HubSpot" (checkbox), "HubSpot Portal ID", "HubSpot Form Id" → `renderingOptions.hubspotEnable/hubspotPortalId/hubspotFormId` |
| `Form.formEditor.editors.900` + `propertyCollections.finishers` | "HubSpot Finisher" in the "Add finisher" select; header + remove button only |
| `<Element>.formEditor.editors.6100` | "HubSpot Value" text field → `properties.hubSpotValue` on Text, Textarea, Email, Number, Telephone, Hidden, Url, Checkbox, MultiCheckbox, MultiSelect, RadioButton, SingleSelect, FileUpload, ImageUpload |
| `formEditor` (stylesheets, `additionalViewModelModules`, partial root path, `formEditorPartials`) | Premium strip (`features/premium-cta.md`) |
| Global side effects | `persistenceManager` paths for ns_basetheme, Bootstrap 4 classes on several elements (`core.md`) |

## Files

| File | Role |
|---|---|
| `Classes/Finishers/ApiFinisher.php` | The only runtime logic: builds and POSTs the HubSpot submission (`features/hubspot-finisher.md`) |
| `Configuration/TypoScript/{constants,setup}.typoscript` | Static template "[Nitsan] Nitsan HubSpot": `plugin.tx_nshubspot.settings.{apiURL,client_apikey,client_id}` + form YAML include |
| `Configuration/TCA/Overrides/sys_template.php` | Registers that static template |
| `Configuration/Sets/Ns_HubSpot/` | Site set `nitsan/ns-hubspot` ("EXT:hubspot : Basic Setup"): imports the static constants/setup, defines site settings `nshubspot.*` |
| `Configuration/JavaScriptModules.php` | Import map `@nitsan/ns-hubspot/` → `Resources/Public/JavaScript/` |
| `Configuration/Services.yaml` | Autowires `Nitsan\NsHubSpot\` |
| `Resources/Private/Backend/Partials/FormEditor/NsHubSpot/PremiumStrip.html` | Premium strip markup (form editor template `NsHubSpot-PremiumStrip`) |
| `Resources/Public/JavaScript/form-editor-premium-strip.js` | Form editor view-model module that places the strip |
| `Resources/Public/Css/form-editor.css` | Strip styling (form editor only) |
| `Resources/Private/Language/locallang.xlf` | Setting labels + premium strip labels |
| `ext_tables.php` | Empty (guard only) |
