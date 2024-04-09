<?php

$EM_CONF['ns_hubspot'] = [
    'title' => 'HubSpot',
    'description' => 'HubSpot Extension for TYPO3 forms easily connects with HubSpot CRM. This TYPO3 add-on for your forms ensures you capture all leads from your TYPO3 websites. Take advantage of your leads! Our integration of TYPO3 forms and HubSpot CRM offers a strong combination that will change the way you collect, nurture, and convert leads. 
    
    *** Live Demo: https://demo.t3planet.com/t3-extensions/typo3-hubspot *** Documentation & Free Support: https://t3planet.com/typo3-hubspot-extension',
    'category' => 'plugin',
    'author' => 'T3: Nilesh Malankiya, T3: Pradeepsinh Masani, QA: Krishna Dhapa',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'T3Planet // NITSAN',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'version' => '1.0.1',
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
