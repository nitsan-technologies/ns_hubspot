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
