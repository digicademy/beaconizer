<?php
if (!defined ('TYPO3_MODE')){
	die ('Access denied.');
}

// REGISTER PLUGIN

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'ADWLM.' . $_EXTKEY,
	'Generator',
	'Beaconizer: Generator'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'ADWLM.' . $_EXTKEY,
	'SeeAlso',
	'Beaconizer: SeeAlso'
);

// TYPOSCRIPT
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Beaconizer: Generator');

// FLEXFORMS
$TCA['tt_content']['types']['list']['subtypes_addlist']['beaconizer_generator'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('beaconizer_generator', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/GeneratorPlugin.xml');

$TCA['tt_content']['types']['list']['subtypes_addlist']['beaconizer_seealso'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('beaconizer_seealso', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/SeeAlsoPlugin.xml');

// TABLE DEFINITIONS

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_beaconizer_domain_model_providers', 'EXT:beaconizer/Resources/Private/Language/locallang_csh_providers.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_beaconizer_domain_model_providers');
$TCA['tx_beaconizer_domain_model_providers'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_providers',
		'label' 			=> 'title',
		'label_alt'			=> '',
		'label_alt_force'	=> 0,
		'default_sortby'	=> 'ORDER BY title ASC',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'dividers2tabs'		=> 1,
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden',
		),
		'searchFields'		=> 'title,link_pattern,prefix,target,relation,message,annotation,description,creator,contact,homepage,feed,sourceset,targetset,name,institution',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Providers.php',
		'iconfile' 			=> \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_beaconizer_domain_model_providers.png'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_beaconizer_domain_model_links', 'EXT:beaconizer/Resources/Private/Language/locallang_csh_links.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_beaconizer_domain_model_links');
$TCA['tx_beaconizer_domain_model_links'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xml:tx_beaconizer_domain_model_links',
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
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Links.php',
		'iconfile' 			=> \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_beaconizer_domain_model_links.png'
	),
);

?>