<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_beaconizer_domain_model_links'] = array(
	'ctrl' => $TCA['tx_beaconizer_domain_model_links']['ctrl'],
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
			--div--;LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links.div1,
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
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links.source_identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'annotation' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links.annotation',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim',
			),
		),
		'target_identifier' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links.target_identifier',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim',
			),
		),
		'provider' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links.provider',
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