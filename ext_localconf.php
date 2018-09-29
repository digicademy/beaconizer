<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}

// CONFIGURE PLUGINS
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ADWLM.' . $_EXTKEY,
    'Generator',
    array(
        'Generator' => 'routing, beacon',
    ),
    array(
        'Generator' => '',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ADWLM.' . $_EXTKEY,
    'SeeAlso',
    array(
        'SeeAlso' => 'lookUp',
    ),
    array(
        'SeeAlso' => '',
    )
);

// register scheduler task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['ADWLM\\Beaconizer\\Task\\HarvestingTask'] = array(
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.description',
    'additionalFields' => 'ADWLM\\Beaconizer\\Task\\AdditionalFieldProvider'
);
