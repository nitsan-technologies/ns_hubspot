# ns_hubspot — Core facts

## Identity

| Item | Value |
|---|---|
| Extension key | `ns_hubspot` |
| Composer name | `nitsan/ns-hubspot` |
| Title (ext_emconf) / product name | TYPO3 HubSpot Integration / HubSpot |
| Namespace | `Nitsan\NsHubSpot\` → `Classes/` (note the capital S in `HubSpot`) |
| Version (ext_emconf) | 2.1.0 (latest tag 2.0.0) |
| Repository | https://github.com/nitsan-technologies/ns_hubspot (default branch `main`) |
| Licensing status (NITSAN Free/Freemium sheet) | **Freemium** — this repo is the free version |
| Premium version | Sheet: https://t3planet.de/typo3-hubspot-erweiterung · T3Planet catalog: €129, https://t3planet.de/en/typo3-hubspot-extension |

## Compatibility

| Source | TYPO3 | PHP |
|---|---|---|
| `ext_emconf.php` | `13.0.0-14.9.99` | not declared |
| `composer.json` | `cms-core ^13 \|\| ^14`, other cms packages `^13.4.20 \|\| ^14.0` | not declared |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — |

- Effective minimum is **TYPO3 13.4.20** (Composer constraint). `ext_emconf` still says 13.0.0.
- PHP 8.2+ through TYPO3 13 (code uses `mixed`, nullsafe `?->`, `str_starts_with`).
- Verified on TYPO3 14.3.7 classic mode (DDEV). v13 not tested in this workspace.

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core`, `-extbase`, `-fluid`, `-frontend`, `-install` | see above | core |
| `typo3/cms-form` | **not declared** | the whole extension extends EXT:form (finisher, form editor) |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | TYPO3 Shop module + free-trial deep link (`features/premium-cta.md`) |
| `guzzlehttp/guzzle` | not declared | HTTP client; shipped with TYPO3 core |

## Tooling

- No PHPUnit, PHPStan, CS fixer or package.json in the repo.
- CI: `.github/workflows/ter-release.yml` only (publishes tags to TER, PHP 7.4 runner, deprecated `set-output`).
- No Crowdin config. Labels: `Resources/Private/Language/locallang.xlf` only.

## Conventions

- Form integration is pure configuration (`Configuration/Form/setup.yaml`) plus one finisher class.
- Form editor additions are server-rendered Fluid partials and one ES module, no build step.
- New PHP: `declare(strict_types=1);`, `final` where possible, labels via `LLL:EXT:ns_hubspot/...`.

## Known issues (existing, not fixed)

Verified live on v14.3 (2026-10-12):

- A HubSpot error (4xx/5xx, timeout) is not caught: Guzzle throws, the visitor gets TYPO3's error page instead
  of the confirmation, and finishers after the HubSpot finisher (e.g. email) never run.
- Site setting "API URL" has no effect: TypoScript `plugin.tx_nshubspot.settings.apiURL` (default
  `https://api.hsforms.com`) overwrites it. Only the TypoScript constant changes the URL.
- Form YAML is registered via `plugin.tx_form` / `module.tx_form.settings.yamlConfigurations` (TypoScript and
  `ext_localconf.php`). Deprecated in TYPO3 14.2 (#109412), removed in v15. Migration: move the YAML to
  `Configuration/Form/<SetName>/config.yaml` (auto-discovered).
- Without the site set or static template the frontend doesn't know the finisher and settings, while the
  backend form editor (via `ext_localconf.php`) always offers them.

Code-read:

- `Checkbox` elements are skipped by the finisher although the form editor offers "HubSpot Value" for them.
- File uploads: only the last mapped upload is sent, always as HubSpot field `file_upload` (not its
  "HubSpot Value"), and only if the file sits in storage 1 under `/user_upload/<folder>/`. The upload's own
  form value (an Extbase `FileReference` object) is also cast to string; `AbstractDomainObject::__toString()`
  makes that `TYPO3\CMS\Extbase\Domain\Model\FileReference:<uid>`, which is sent under its "HubSpot Value".
- API keys that don't start with `pat-` are sent as `hapikey` query parameter. HubSpot retired API keys,
  so in practice only private-app tokens work.
- `client_apikey` is required even though HubSpot's non-secure submit endpoint needs no authentication.
- Setting `client_id` is defined (TypoScript, site set, label) but never used.
- `Configuration/Form/setup.yaml` also changes every form of the installation: it allows saving/deleting forms
  in `EXT:ns_basetheme/Resources/Private/Forms/` (leftover; ns_basetheme isn't a dependency), and sets
  Bootstrap 4 classes (`custom-control`, `custom-select`, `form-row`, `is-invalid`, …) on Checkbox, GridRow,
  MultiCheckbox, MultiSelect, RadioButton, SingleSelect, FileUpload, ImageUpload, DatePicker.
- `composer.json` licence `GPL-2.0+` is a deprecated SPDX id (README badge says GPL-3.0); description typo
  "Integratio".
- README is outdated (v1.0.2 badge, TYPO3 12, premium link differs from the sheet).
