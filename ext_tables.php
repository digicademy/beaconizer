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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_beaconizer_domain_model_links', 'EXT:beaconizer/Resources/Private/Language/locallang_csh_links.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_beaconizer_domain_model_links');
?>