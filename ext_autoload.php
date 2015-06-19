<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('beaconizer');
return array(
	'ADWLM\Beaconizer\Task\HarvestingTask' => $extensionPath . 'Classes/Task/HarvestingTask.php',
	'ADWLM\Beaconizer\Task\AdditionalFieldProvider' => $extensionPath . 'Classes/Task/AdditionalFieldProvider.php',
);
?>