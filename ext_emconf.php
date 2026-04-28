<?php

$EM_CONF['ns_hubspot'] = [
    'title' => 'TYPO3 HubSpot Integration',
    'description' => 'Seamlessly connect your TYPO3 forms with HubSpot CRM to capture, manage, and convert leads efficiently. Improve your marketing automation and sales pipeline with direct integration.', 
    
    'category' => 'plugin',
    'author' => 'Team T3Planet',
    'author_email' => 'info@t3planet.de',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-14.9.99',
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
