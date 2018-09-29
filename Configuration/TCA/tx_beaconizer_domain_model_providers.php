<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers',
        'label' => 'title',
        'label_alt' => '',
        'label_alt_force' => 0,
        'default_sortby' => 'ORDER BY title ASC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'dividers2tabs' => 1,
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'searchFields' => 'title,link_pattern,prefix,target,relation,message,annotation,description,creator,contact,homepage,feed,sourceset,targetset,name,institution',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('beaconizer') . 'Resources/Public/Icons/tx_beaconizer_domain_model_providers.png'
    ),
    'interface' => array(
        'showRecordFieldList' => '
			hidden,
			title,
			link_pattern,
			prefix,
			target,
			relation,
			message,
			annotation,
			description,
			creator,
			contact,
			homepage,
			feed,
			timestamp,
			update_information,
			sourceset,
			targetset,
			name,
			institution,
			harvesting_data,
			harvesting_timestamp,
		',
    ),
    'types' => array(
        '1' => array(
            'showitem' => '
			--div--;LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.div1,
				hidden,
				title,
				feed,
				link_pattern,
				harvesting_data,
				harvesting_timestamp,
			--div--;LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.div2,
				prefix,
				target,
				relation,
				message,
				annotation,
				description,
				creator,
				contact,
				homepage,
				timestamp,
				update_information,
				sourceset,
				targetset,
				name,
				institution,
		'
        ),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.title',
            'config' => array(
                'type' => 'input',
                'size' => 50,
                'eval' => 'required,trim'
            ),
        ),
        'feed' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.feed',
            'config' => array(
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim,required',
            ),
        ),
        'link_pattern' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.link_pattern',
            'config' => array(
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ),
        ),
        'prefix' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.prefix',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'target' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.target',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'relation' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.relation',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'message' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.message',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'annotation' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.annotation',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'description' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.description',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'creator' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.creator',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'contact' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.contact',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'homepage' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.homepage',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'timestamp' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.timestamp',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'update_information' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.update_information',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'sourceset' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.sourceset',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'targetset' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.targetset',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'name' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.name',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'institution' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.institution',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'harvesting_data' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.harvesting_data',
            'config' => array(
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ),
        ),
        'harvesting_timestamp' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_providers.harvesting_timestamp',
            'config' => array(
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ),
        ),
        // supported legacy tags for reharvesting determination
        'date' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'revisit' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
    ),
);
