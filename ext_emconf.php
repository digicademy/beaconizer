<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Beaconizer',
    'description' => 'Harvest data from BEACON files and beaconize your data. See: https://meta.wikimedia.org/wiki/Dynamic_links_to_external_resources',
    'category' => 'fe',
    'author' => 'Torsten Schrade',
    'author_email' => 'Torsten.Schrade@adwmainz.de',
    'author_company' => 'Academy of Sciences and Literature | Mainz',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
