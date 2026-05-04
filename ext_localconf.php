<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();
ExtensionManagementUtility::addTypoScriptSetup('
    module.tx_form {
       settings {
           yamlConfigurations {
               1732785702 = EXT:ns_hubspot/Configuration/Form/setup.yaml
           }
       }
    }
');