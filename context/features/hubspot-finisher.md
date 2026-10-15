# Feature — HubSpot finisher (EXT:form → HubSpot Forms API)

*Status: Done. Main path verified live on TYPO3 14.3 against a local fake HubSpot endpoint (2026-10-12).*

The free version sends form submissions to one HubSpot form. Everything else (HubSpot-built forms,
live chat, email marketing, dashboard, …) is premium (`premium-cta.md`).

## Setup an editor needs

1. Site: add the site set `nitsan/ns-hubspot` (or include the static template "[Nitsan] Nitsan HubSpot").
   Without it the frontend doesn't know the finisher.
2. Site Settings → "HubSpot Configuration" → "Client API Key": a HubSpot **private-app token** (`pat-…`).
3. Forms module → form → "Edit form properties": tick **Enable HubSpot**, enter **HubSpot Portal ID** and
   **HubSpot Form Id**, add the **HubSpot Finisher** (before Confirmation / Email finishers).
4. Each field to send: set **HubSpot Value** to the HubSpot internal property name (`firstname`, `email`, …).

Example: `fileadmin/form_definitions/hubspotTest.form.yaml` in this workspace (fake portal/form IDs).

## What happens on submit (`ApiFinisher::executeInternal()`)

1. Reads `renderingOptions.hubspotEnable/hubspotPortalId/hubspotFormId` of the form. Returns silently if
   HubSpot is disabled (bool, 1, or "true"/"1"/"yes"/"on" count as enabled).
2. Resolves settings (`resolveHubSpotConfiguration()`), later sources overwrite earlier ones, empty values skipped:
   built-in defaults (`apiURL` `https://api.hsforms.com`) → site settings `nshubspot.*` → TypoScript
   `plugin.tx_nshubspot.settings.*`. Because the TypoScript `apiURL` constant is never empty, the site
   setting "API URL" never applies.
3. Returns silently if API key, portal ID or form ID is empty.
4. Builds the field list from all elements recursively, skipping `Page`, `GridRow`, `Fieldset`, `Checkbox`,
   `StaticText`, `Recaptcha`, `Honeypot`. An element is sent if it has a non-empty `hubSpotValue`
   (rendering option or property) and a non-empty submitted value. Multi-value answers are joined with `", "`.
5. File upload: for a `FileUpload` or `ImageUpload` (both use the core `FileUpload` class) with `hubSpotValue`,
   the public URL of `1:/user_upload/<upload folder>/<file name>` is sent as field `file_upload` (last upload
   wins).
6. Returns silently if no field is left.
7. POSTs JSON `{"fields":[{"name":…,"value":…}]}` with Guzzle (timeout 10 s):
   - token `pat-…` → `{apiURL}/submissions/v3/integration/secure/submit/{portalId}/{formId}` with
     `Authorization: Bearer <token>` (verified);
   - any other key → `{apiURL}/submissions/v3/integration/submit/{portalId}/{formId}?hapikey=<key>`
     (legacy, HubSpot retired API keys).
8. No response handling: success → next finisher runs (visitor sees the confirmation); HubSpot error →
   uncaught exception, TYPO3 error page, later finishers skipped (verified with 401 and 400).

No context, page URL, IP, `hutk` cookie or consent data is sent; only the mapped fields.

## Settings

| Site setting (`settings.definitions.yaml`) | TypoScript constant | Default | Used |
|---|---|---|---|
| `nshubspot.apiURL` "API URL" | `plugin.tx_nshubspot.settings.apiURL` | `https://api.hsforms.com` | yes, but TypoScript always wins |
| `nshubspot.client_apikey` "Client API Key" | `plugin.tx_nshubspot.settings.client_apikey` | empty | yes |
| `nshubspot.client_id` "Client ID" | `plugin.tx_nshubspot.settings.client_id` | empty | no |

To test without HubSpot, point the TypoScript constant `plugin.tx_nshubspot.settings.apiURL` at a local
fake endpoint (the site setting is ignored, see step 2).
