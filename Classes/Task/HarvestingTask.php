<?php
namespace ADWLM\Beaconizer\Task;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Scheduler\Task\AbstractTask;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ArrayUtility;

class HarvestingTask extends AbstractTask {

	/**
	 * Retrieves BEACON files from selected providers and harvests the links
	 *
	 * @return boolean
	 */
	public function execute() {
		$executionResult = TRUE;
			// iterate over selected providers
		foreach ($this->providersToHarvest as $uid) {

			$currentProvider = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_beaconizer_domain_model_providers', 'uid=' . (int) $uid);

			if ($currentProvider === FALSE || $currentProvider === NULL) {
				throw new \RuntimeException('Provider with uid '. (int) $uid . ' could not be retrieved from database', 1428410051);
			}

			($currentProvider['harvesting_data']) ? $harvestingData = unserialize($currentProvider['harvesting_data']) : $harvestingData = array();
			$fieldsValues = array();

				// case 1: there still exists a temporary file that needs to be fully imported
			if ($harvestingData['fileName']) {

				$temporaryBeaconFile = $harvestingData['fileName'];
				$GLOBALS['BE_USER']->simplelog('Continuing to harvest from provider ' . htmlspecialchars($currentProvider['title']), $extKey='beaconizer', 0);

				// case 2: new import cycle
			} else {

					// fetch current providers BEACON file
				$report = array();
				$beaconFileContent = GeneralUtility::getUrl($currentProvider['feed'], 0, FALSE, $report);

				if ($beaconFileContent === FALSE) {
					($report['error'] > 0 && $report['message']) ? $errorMessage = '. ' . htmlspecialchars($report['message']) : $errorMessage = '';
					$GLOBALS['BE_USER']->simplelog('Could not retrieve BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . $errorMessage, $extKey='beaconizer', 2);
					// $executionResult = FALSE;
				} else {
						// determine if file should be harvested
					$fileShouldBeHarvested = $this->shouldFileBeHarvested($beaconFileContent, $currentProvider);

						// if harvesting is enforced or file should be harvested start a new import cycle ...
					if ($this->forceHarvesting || $fileShouldBeHarvested === TRUE) {
							// ... by creating a temporary file ...
						$temporaryBeaconFile = GeneralUtility::tempnam('beaconizer_');
						$write = GeneralUtility::writeFileToTypo3tempDir($temporaryBeaconFile, $beaconFileContent);
						if ($write !== NULL) {
							$GLOBALS['BE_USER']->simplelog(htmlspecialchars($write), $extKey='beaconizer', 2);
							$temporaryBeaconFile = '';
						}
							// ... and dropping stale temporary links of the current provider
						$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_beaconizer_domain_model_links', 'pid = 0 AND provider=' . (int) $uid);
					} else {
						$GLOBALS['BE_USER']->simplelog('File from provider ' . htmlspecialchars($currentProvider['title']) . ' has not changed since last harvesting, doing nothing', $extKey='beaconizer', 0);
					}
				}
			}

				// either there should be a newly fetched or a still to be worked upon temporary file
			if ($temporaryBeaconFile) {

				$file = GeneralUtility::makeInstance('SplFileObject', $temporaryBeaconFile);

					// set line pointer and import count
				($harvestingData['linePointer'] > 0) ? $l = $harvestingData['linePointer'] : $l = 0;
				$i = 0;
				$rowsToInsert = array();
				$wrongNumberOfSegments = FALSE;
				$incorrectDataInLines = FALSE;

				if (is_object($file)) {
						// seek pointer in file if set
					if ($l > 0) $file->seek($l);
						// go on until EOF or totalPerRun is reached
					while (!$file->eof()) {

						if ($this->totalPerRun > 0 && $this->totalPerRun === $i) {
							$noDelete = 1;
							break;
						}

						$line = $file->fgets();

							// empty line - skip
						if ($line == '') continue;
							// meta line
						if ($line{0} == '#') {
								// #PREFIX:
							if (substr($line, 0, 8) == '#PREFIX:') $fieldsValues['prefix'] = trim(substr($line, 8));
								// #TARGET:
							if (substr($line, 0, 8) == '#TARGET:') $fieldsValues['target'] = trim(substr($line, 8));
								// #LINK: (mapped to relation)
							if (substr($line, 0, 6) == '#LINK:') $fieldsValues['relation'] = trim(substr($line, 6));
								// #RELATION:
							if (substr($line, 0, 10) == '#RELATION:') $fieldsValues['relation'] = trim(substr($line, 10));
								// #MESSAGE:
							if (substr($line, 0, 9) == '#MESSAGE:') $fieldsValues['message'] = trim(substr($line, 9));
								// #ANNOTATION:
							if (substr($line, 0, 12) == '#ANNOTATION:') $fieldsValues['annotation'] = trim(substr($line, 12));
								// #DESCRIPTION:
							if (substr($line, 0, 13) == '#DESCRIPTION:') $fieldsValues['description'] = trim(substr($line, 13));
								// #CREATOR:
							if (substr($line, 0, 9) == '#CREATOR:') $fieldsValues['creator'] = trim(substr($line, 9));
								// #CONTACT:
							if (substr($line, 0, 9) == '#CONTACT:') $fieldsValues['contact'] = trim(substr($line, 9));
								// #HOMEPAGE:
							if (substr($line, 0, 10) == '#HOMEPAGE:') $fieldsValues['homepage'] = trim(substr($line, 10));
								// #FEED: is not parsed because it is set manually in the TYPO3 backend
								// #TIMESTAMP:
							if (substr($line, 0, 11) == '#TIMESTAMP:') $fieldsValues['timestamp'] = trim(substr($line, 11));
								// #UPDATE:
							if (substr($line, 0, 8) == '#UPDATE:') $fieldsValues['update_information'] = trim(substr($line, 8));
								// #REVISIT:
							if (substr($line, 0, 9) == '#REVISIT:') $fieldsValues['revisit'] = trim(substr($line, 9));
								// #SOURCESET:
							if (substr($line, 0, 11) == '#SOURCESET:') $fieldsValues['sourceset'] = trim(substr($line, 11));
								// #TARGETSET:
							if (substr($line, 0, 11) == '#TARGETSET:') $fieldsValues['targetset'] = trim(substr($line, 11));
								// #NAME:
							if (substr($line, 0, 6) == '#NAME:') $fieldsValues['name'] = trim(substr($line, 6));
								// #INSTITUTION:
							if (substr($line, 0, 13) == '#INSTITUTION:') $fieldsValues['institution'] = trim(substr($line, 13));
								// supported legacy tags for better reharvesting determination
								// #DATE:
							if (substr($line, 0, 6) == '#DATE:') $fieldsValues['date'] = trim(substr($line, 6));
								// #REVISIT:
							if (substr($line, 0, 9) == '#REVISIT:') $fieldsValues['revisit'] = trim(substr($line, 9));
						}

							// if in the middle of a file set target from provider record (which btw. keeps it for next runs)
						if (!$fieldsValues['target'] && $currentProvider['target']) $fieldsValues['target'] = $currentProvider['target'];

							// data line
						if (preg_match('/^[0-9a-zA-Z]/', $line)) {

								// get data segments
							$data = GeneralUtility::trimExplode('|', $line);
							if (count($data) < 1 && count($data) > 3) {
								$wrongNumberOfSegments = TRUE;
								continue;
							}

								// process data segments
								// rules for construction @see: http://gbv.github.io/beaconspec/beacon.html#link-construction
							$linkValues = array();
							$validSourceIdentifier = TRUE;

								// segment 1 - always set source_identifier; mandatory
							(preg_match('/^[a-zA-Z0-9]+$/', $data[0])) ? $linkValues['source_identifier'] = $data[0] : $validSourceIdentifier = FALSE;

								// one data segment (source identifier) given, construct the target identifier from #TARGET + source identifier
							if (count($data) == 1 && $validSourceIdentifier == TRUE && $fieldsValues['target']) {
								($fieldsValues['message']) ? $linkValues['annotation'] = $fieldsValues['message'] : $linkValues['annotation'] = '';
								$linkValues['target_identifier'] = str_replace('{ID}', $linkValues['source_identifier'], $fieldsValues['target']);
								// two data segments given; segment two might contain a full target identifier starting with http:|https:
							} elseif (count($data) == 2 && $validSourceIdentifier == TRUE) {
								if (GeneralUtility::isValidUrl($data[1])) {
									($fieldsValues['message']) ? $linkValues['annotation'] = $fieldsValues['message'] : $linkValues['annotation'] = '';
									$linkValues['target_identifier'] = $data[1];
								} else {
									$linkValues['annotation'] = $data[1];
									$linkValues['target_identifier'] = str_replace('{ID}', $linkValues['source_identifier'], $fieldsValues['target']);
								}
								// three data segments given
							} else {
									// in this case segment 2 is always used as annotation
								$linkValues['annotation'] = $data[1];

									// full URL in third segment
								if (GeneralUtility::isValidUrl($data[2])) {
									$linkValues['target_identifier'] = $data[2];
									// support for legacy format: source identifier | count | annotation; @example: http://www.historische-kommission-muenchen-editionen.de/beacon_adr.txt
								} elseif ((int) $data[1] > 0 && GeneralUtility::isValidUrl($data[2]) === FALSE && $fieldsValues['target']) {
									$linkValues['target_identifier'] = str_replace('{ID}', $linkValues['source_identifier'], $fieldsValues['target']);
									$linkValues['annotation'] .= '|' . $data[2];
									// only identifier in third segment, construct target identifier from #TARGET + source identifier
								} elseif ($data[2] && $fieldsValues['target']) {
									$constructedTargetIdentifier = str_replace('{ID}', $data[2], $fieldsValues['target']);
									if (GeneralUtility::isValidUrl($constructedTargetIdentifier)) {
										$linkValues['target_identifier'] = $constructedTargetIdentifier;
									} else {
										($linkValues['annotation']) ? $linkValues['annotation'] .= '|' . $data[2] : $linkValues['annotation'] = $data[2];
									}
								} else {
									($linkValues['annotation']) ? $linkValues['annotation'] .= '|' . $data[2] : $linkValues['annotation'] = $data[2];
								}

									// set standard value for annotation if not set up to this point
								if ($linkValues['annotation'] == '' && $fieldsValues['message']) {
									$linkValues['annotation'] = $fieldsValues['message'];
								}
							}

								// if processing of line was valid (based on source identifier and target identifier) prepare record for import, else skip this line
							if ($validSourceIdentifier === TRUE && GeneralUtility::isValidUrl($linkValues['target_identifier'])) {
								$linkValues['provider'] = (int) $uid;
								$linkValues['tstamp'] = time();
								$linkValues['crdate'] = time();
								$linkValues['pid'] = 0;
								$linkValues['hidden'] = 1;
									// collect new link record for later bulk insertion
								$rowsToInsert[] = $linkValues;
							} else {
								$incorrectDataInLines = TRUE;
								continue;
							}

								// raise import count
							$i++;
						} else {
							$incorrectDataInLines = TRUE;
						}
							// raise line count
						$l++;
					}
				}

					// some logging if corrupt lines were in the file
				if ($wrongNumberOfSegments === TRUE) $GLOBALS['BE_USER']->simplelog('BEACON file from provider '. htmlspecialchars($currentProvider['title']) . ' contains some lines with wrong number of data segments that were skipped', $extKey='beaconizer', 2);
				if ($incorrectDataInLines === TRUE) $GLOBALS['BE_USER']->simplelog('BEACON file from provider '. htmlspecialchars($currentProvider['title']) . ' contains some lines with incorrect data that were skipped', $extKey='beaconizer', 2);

				// insert new records in a batch (values will be escaped internally)
				if (count($rowsToInsert) > 0) $GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows(
					'tx_beaconizer_domain_model_links',
					array('source_identifier', 'annotation', 'target_identifier', 'provider', 'tstamp', 'crdate', 'pid', 'hidden'),
					$rowsToInsert
				);

					// log action
				$GLOBALS['BE_USER']->simplelog((int) $i . ' links have been harvested from provider ' . htmlspecialchars($currentProvider['title']), $extKey='beaconizer', 0);

					// in case we have to continue with the next scheduler run keep the temporary file
				if ($noDelete === 1) {
					$harvestingData['fileName'] = $temporaryBeaconFile;
					$harvestingData['totalImported'] = $harvestingData['totalImported'] + $i;
					$harvestingData['linePointer'] = $l-1;
					$fieldsValues['harvesting_data'] = serialize($harvestingData);
					$fieldsValues['harvesting_timestamp'] = time();
				} else {
					GeneralUtility::unlink_tempfile($temporaryBeaconFile);
					$fieldsValues['harvesting_data'] = '';
					$fieldsValues['harvesting_timestamp'] = time();
					$GLOBALS['BE_USER']->simplelog('Finished harvesting file from provider ' . htmlspecialchars($currentProvider['title']), $extKey='beaconizer', 0);
						// throw away 'old' links for current provider
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_beaconizer_domain_model_links', 'pid > 0 AND provider=' . (int) $uid);
						// rotate newly harvested links from pid 0 to target pid
					($this->importOnPid > 0) ? $pid = (int) $this->importOnPid : $pid = (int) $currentProvider['pid'];
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_beaconizer_domain_model_links', 'provider=' . (int) $uid, array('pid' => $pid, 'hidden' => 0));
					$GLOBALS['BE_USER']->simplelog('Rotating harvested links for provider ' . htmlspecialchars($currentProvider['title']) . ' to page with ID ' . $pid, $extKey='beaconizer', 0);
				}

					// if $fieldsValues contains more than target, harvesting_data and harvesting_timestamp, this means there is incoming meta data from BEACON file
				if (count($fieldsValues) > 3) {
						// clean provider metadata - otherwise values would be kept even if they are no longer in the BEACON file
					$cleanMetadata = array(
						'prefix' => '',
						'relation' => '',
						'message' => '',
						'annotation' => '',
						'description' => '',
						'creator' => '',
						'contact' => '',
						'homepage' => '',
						'timestamp' => '',
						'update_information' => '',
						'sourceset' => '',
						'targetset' => '',
						'name' => '',
						'institution' => '',
						'date' => '',
						'revisit' => '',
					);
					ArrayUtility::mergeRecursiveWithOverrule($cleanMetadata, $fieldsValues);
					$fieldsValues = $cleanMetadata;
				}

					// update current provider with meta information (values will be escaped internally by API function)
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'tx_beaconizer_domain_model_providers',
					'uid=' . (int) $uid,
					$fieldsValues
				);
			}
		}

		return $executionResult;
	}

	/**
	 * Displays the selected providers for the current task
	 *
	 * @return string
	 */
	public function getAdditionalInformation() {
		$selectedProviders = array();
		foreach ($this->providersToHarvest as $uid) {
			$currentProvider = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('title', 'tx_beaconizer_domain_model_providers', 'uid=' . (int) $uid);
			$selectedProviders[] = htmlspecialchars($currentProvider['title']);
		}
		return 'Selected providers: ' . implode(', ', $selectedProviders);
	}

	/**
	 * Determines if a file should be harvested by comparing TIMESTAMP or UPDATE data of the current
	 * provider record and the current BEACON file. The two legacy fields DATE and REVISIT are also
	 * supported since they are still used a lot and help to determine if a file should be harvested
	 * again.
	 *
	 * @param string $beaconFileContent
	 * @param array $provider
	 *
	 * @return boolean
	 */
	private function shouldFileBeHarvested($beaconFileContent, $provider) {

		$result = FALSE;
		$metadataTagMatches = array();

			// if file carries a timestamp, always take this as basis for comparison
		preg_match('/^(#TIMESTAMP:)(.*?)$/m', $beaconFileContent, $timestampMatches);
			// if timestamp in file is newer than timestamp in provider record, harvest
		if (strftime(trim($provider['timestamp'])) < strftime(trim($timestampMatches[2]))) {
			$result = TRUE;
			$metadataTagMatches['timestamp'] = $timestampMatches[2];
		}

			// if no timestamp could be retrieved, check if file has UPDATE metadata information
		preg_match('/^(#UPDATE:)(.*?)$/m', $beaconFileContent, $updateMatches);
		if ($result === FALSE && is_array($updateMatches)) {
			$metadataTagMatches['update'] = $updateMatches[2];
				// get the time difference between now and the (last) harvesting timestamp
			$timeDifference = time() - (int) $provider['harvesting_timestamp'];
			switch (trim($updateMatches[2])) {
				case 'always':
						$result = TRUE;
					break;
				case 'hourly':
						if ($timeDifference > 3600) $result = TRUE;
					break;
				case 'daily':
						if ($timeDifference > 86400) $result = TRUE;
					break;
				case 'weekly':
						if ($timeDifference > 604800) $result = TRUE;
					break;
				case 'monthly':
						if ($timeDifference > 2629743) $result = TRUE;
					break;
				case 'yearly':
						if ($timeDifference > 31556926) $result = TRUE;
					break;
				case 'never':
				default:
						// nothing done, result stays FALSE
					break;
			}
		}

			// if legacy date tag is newer than date tag in provider record, harvest
		preg_match('/^(#DATE:)(.*?)$/m', $beaconFileContent, $dateMatches);
		if ($result === FALSE && is_array($dateMatches) && strftime(trim($provider['date'])) < strftime(trim($dateMatches[2]))) {
			$result = TRUE;
			$metadataTagMatches['date'] = $dateMatches[2];
		}
			/*
			 * if legacy revisit tag is newer than revisit tag in provider record, harvest; revisit just treated as another timestamp and not in the sense of a
			 * 'the time now is past revisit time, please harvested' mechanism since it is very often not updated in older BEACON files which
			 * would result in the file always being harvested
			 */
		preg_match('/^(#REVISIT:)(.*?)$/m', $beaconFileContent, $revisitMatches);
		if ($result === FALSE && is_array($revisitMatches) && strftime(trim($provider['revisit'])) < strftime(trim($revisitMatches[2]))) {
			$result = TRUE;
			$metadataTagMatches['revisit'] = $revisitMatches[2];
		}

			// if none of the four date/time base metadata tags could be found, reharvesting cannot be determined
		if (empty($metadataTagMatches)) $GLOBALS['BE_USER']->simplelog('Neither a timestamp, an update, date or revisit field could be found in file from provider ' . htmlspecialchars($provider['title']) . '. Harvesting cannot be determined', $extKey='beaconizer', 0);

		return $result;
	}
}
?>