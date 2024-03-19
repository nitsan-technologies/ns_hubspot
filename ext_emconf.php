<?php

$EM_CONF['ns_hubspot'] = [
    'title' => '[NITSAN] HubSpot TYPO3 Extension',
    'description' => 'Easily install and configure your TYPO3 form with HubSpot CRM. Demo: https://demo.t3planet.com/t3-extensions/typo3-hubspot-crm You can download PRO version for more-features & free-support at https://t3planet.com/typo3-hubspot-extension',
    'category' => 'plugin',
    'author' => 'T3D: Pradeepsinh Masani, Nilesh Malankiya',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Nitsan\\NsHubSpot\\' => 'Classes'
        ],
    ],
];
