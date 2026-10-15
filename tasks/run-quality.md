# Run quality checks

The repo has no PHPUnit, PHPStan or CS config. Available checks:

```bash
# PHP syntax, once per installed PHP version (8.2+)
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done

# composer.json (expected warning: deprecated licence id "GPL-2.0+")
composer validate --no-check-publish

# XLF and YAML well-formedness
xmllint --noout Resources/Private/Language/locallang.xlf
python3 -c "import yaml,glob;[yaml.safe_load(open(f)) for f in glob.glob('Configuration/**/*.yaml', recursive=True)]"
```

In Classic mode, run `typo3 dumpautoload` after adding a PHP class, then flush caches.

## Manual test (per TYPO3 major 13 / 14)

Setup: site set `nitsan/ns-hubspot` on the site. Never use a real token against the real HubSpot; point the
TypoScript constant `plugin.tx_nshubspot.settings.apiURL` at a local fake endpoint and use a dummy `pat-…` key.

1. Forms → open a form → "Edit form properties": Enable HubSpot, Portal ID, Form Id present; "Add finisher"
   lists "HubSpot Finisher"; a Text field shows "HubSpot Value".
2. Blue premium strip above the stage toolbar, once, for every user, only while "Enable HubSpot" is on.
   Toggle the checkbox off/on: the strip disappears/reappears without saving. As system maintainer,
   "Start Free Trial" opens the ns_license module and its trial wizard for `ns_hubspot`.
3. Frontend: submit the form → confirmation shown; fake endpoint receives the secure v3 URL, Bearer header and
   the mapped fields.
4. Disable HubSpot on the form → nothing sent, confirmation still shown.
5. Fake endpoint answers 400/401 → note the error page (known issue) and that later finishers don't run.
6. Check light and dark backend themes.

Remove test users, fake endpoint and overrides afterwards.
