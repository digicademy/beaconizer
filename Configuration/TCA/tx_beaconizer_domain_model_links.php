<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links',
		'label' 			=> 'provider',
		'label_alt'			=> 'target_identifier',
		'label_alt_force'	=> 1,
		'default_sortby'	=> 'ORDER BY uid ASC',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'rootLevel'			=> -1,
		'dividers2tabs'		=> 0,
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden',
		),
		'searchFields'		=> 'source_identifier,annotation,target_identifier',
		'iconfile' 			=> \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('beaconizer') . 'Resources/Public/Icons/tx_beaconizer_domain_model_links.png'
	),
	'interface' => array(
		'showRecordFieldList' => '
			hidden,
			provider,
			source_identifier,
			annotation,
			target_identifier,
		',
	),
	'types' => array(
		'1' => array('showitem' => '
			--div--;LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links.div1,
				hidden,
				provider,
				source_identifier,
				annotation,
				target_identifier,
		'),
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
		'source_identifier' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links.source_identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'annotation' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links.annotation',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim',
			),
		),
		'target_identifier' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links.target_identifier',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim',
			),
		),
		'provider' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_domain_model_links.provider',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_beaconizer_domain_model_providers',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			),
		),
	),
);
?>