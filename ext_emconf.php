<?php

########################################################################
# Extension Manager/Repository config file for ext "beaconizer".
#
# Auto generated 23-01-2014 08:58
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Beaconizer',
	'description' => 'Harvest data from BEACON providers and BEACONize your data. See: https://meta.wikimedia.org/wiki/Dynamic_links_to_external_resources',
	'category' => 'fe',
	'author' => 'Torsten Schrade',
	'author_email' => 'Torsten.Schrade@adwmainz.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'Academy of Sciences and Literature | Mainz',
	'version' => '0.3.0',
	'constraints' => array(
		'depends' => array(
		'typo3' => '6.2.0-7.9.99',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:2:{s:12:"ext_icon.gif";s:4:"bbb3";s:40:"Classes/ViewHelpers/FilterViewHelper.php";s:4:"47e2";}',
	'suggests' => array(
	),
);

?>