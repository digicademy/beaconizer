<?php

namespace ADWLM\Beaconizer\Task;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
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

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Database\ConnectionPool;

class HarvestingTask extends AbstractTask
{

    /**
     * Retrieves BEACON files from selected providers and harvests the links
     *
     * @return boolean
     */
    public function execute()
    {
        $executionResult = true;
        // iterate over selected providers
        foreach ($this->providersToHarvest as $uid) {

            $currentProvider = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_beaconizer_domain_model_providers')
                ->select(
                    ['*'], // fields
                    'tx_beaconizer_domain_model_providers', // from
                    [ 'uid' => (int)$uid ] // where
                )->fetch();

            if ($currentProvider === false || $currentProvider === null) {
                throw new \RuntimeException('Provider with uid ' . (int)$uid . ' could not be retrieved from database',
                    1428410051);
            }

            ($currentProvider['harvesting_data']) ? $harvestingData = unserialize($currentProvider['harvesting_data']) : $harvestingData = array();
            $fieldsValues = array();

            // case 1: there still exists a temporary file that needs to be fully imported
            if ($harvestingData['fileName']) {

                $temporaryBeaconFile = $harvestingData['fileName'];
                $GLOBALS['BE_USER']->simplelog('Continuing to harvest from provider ' . htmlspecialchars($currentProvider['title']),
                    $extKey = 'beaconizer', 0);

                // case 2: new import cycle
            } else {

                // fetch current providers BEACON file
                $report = array();
                $beaconFileContent = GeneralUtility::getUrl($currentProvider['feed'], 0, false, $report);

                if ($beaconFileContent === false) {

                    ($report['error'] > 0 && $report['message']) ? $errorMessage = '. ' . htmlspecialchars($report['message']) : $errorMessage = '';
                    $GLOBALS['BE_USER']->simplelog('Could not retrieve BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . $errorMessage,
                        $extKey = 'beaconizer', 2);
                    // $executionResult = FALSE;

                } else {
                    // determine if file should be harvested
                    $fileShouldBeHarvested = $this->shouldFileBeHarvested($beaconFileContent, $currentProvider);

                    // if harvesting is enforced or file should be harvested start a new import cycle ...
                    if ($this->forceHarvesting || $fileShouldBeHarvested === true) {

                        // ... by creating a temporary file ...
                        $temporaryBeaconFile = GeneralUtility::tempnam('beaconizer_');
                        $write = GeneralUtility::writeFileToTypo3tempDir($temporaryBeaconFile, $beaconFileContent);
                        if ($write !== null) {
                            $GLOBALS['BE_USER']->simplelog(htmlspecialchars($write), $extKey = 'beaconizer', 2);
                            $temporaryBeaconFile = '';
                        }

                        // ... and dropping stale temporary links of the current provider
                        GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getConnectionForTable('tx_beaconizer_domain_model_links')
                        ->delete(
                            'tx_beaconizer_domain_model_links', // from
                            [ 'pid' => 0, 'provider' =>  (int)$uid] // where
                        );

                    } else {
                        $GLOBALS['BE_USER']->simplelog('File from provider ' . htmlspecialchars($currentProvider['title']) . ' has not changed since last harvesting, doing nothing',
                            $extKey = 'beaconizer', 0);
                    }
                }
            }

            // either there should be a newly fetched or a still to be worked on temporary file
            if ($temporaryBeaconFile) {

                $file = GeneralUtility::makeInstance('SplFileObject', $temporaryBeaconFile);

                // set line pointer
                ($harvestingData['linePointer'] > 0) ? $linePointer = $harvestingData['linePointer'] : $linePointer = 0;

                // initialize import and logging variables
                $rowsToInsert = array();
                $wrongNumberOfSegments = false;
                $incorrectDataInLines = false;
                $unknownLineFormat = false;
                $emptyLines = false;

                if (is_object($file)) {

                    // seek pointer in file if set
                    if ($linePointer > 0) {
                        $file->seek($linePointer);
                    }

                    // go on until EOF...
                    while (!$file->eof()) {

                        // ...or totalPerRun is reached
                        if ($this->totalPerRun > 0 && $this->totalPerRun === count($rowsToInsert)) {
                            $noDelete = 1;
                            break;
                        }

                        // read line
                        $line = $file->fgets();

                        // empty line
                        if (strlen($line) === 1) {
                            $emptyLines = true;
                        }

                        // meta line
                        if ($line{0} == '#') {
                            // #PREFIX:
                            if (substr($line, 0, 8) == '#PREFIX:') {
                                $fieldsValues['prefix'] = trim(substr($line, 8));
                            }
                            // #TARGET:
                            if (substr($line, 0, 8) == '#TARGET:') {
                                $fieldsValues['target'] = trim(substr($line, 8));
                            }
                            // #LINK: (mapped to relation)
                            if (substr($line, 0, 6) == '#LINK:') {
                                $fieldsValues['relation'] = trim(substr($line, 6));
                            }
                            // #RELATION:
                            if (substr($line, 0, 10) == '#RELATION:') {
                                $fieldsValues['relation'] = trim(substr($line, 10));
                            }
                            // #MESSAGE:
                            if (substr($line, 0, 9) == '#MESSAGE:') {
                                $fieldsValues['message'] = trim(substr($line, 9));
                            }
                            // #ANNOTATION:
                            if (substr($line, 0, 12) == '#ANNOTATION:') {
                                $fieldsValues['annotation'] = trim(substr($line, 12));
                            }
                            // #DESCRIPTION:
                            if (substr($line, 0, 13) == '#DESCRIPTION:') {
                                $fieldsValues['description'] = trim(substr($line, 13));
                            }
                            // #CREATOR:
                            if (substr($line, 0, 9) == '#CREATOR:') {
                                $fieldsValues['creator'] = trim(substr($line, 9));
                            }
                            // #CONTACT:
                            if (substr($line, 0, 9) == '#CONTACT:') {
                                $fieldsValues['contact'] = trim(substr($line, 9));
                            }
                            // #HOMEPAGE:
                            if (substr($line, 0, 10) == '#HOMEPAGE:') {
                                $fieldsValues['homepage'] = trim(substr($line, 10));
                            }
                            // #FEED: is not parsed because it is set manually in the TYPO3 backend
                            // #TIMESTAMP:
                            if (substr($line, 0, 11) == '#TIMESTAMP:') {
                                $fieldsValues['timestamp'] = trim(substr($line, 11));
                            }
                            // #UPDATE:
                            if (substr($line, 0, 8) == '#UPDATE:') {
                                $fieldsValues['update_information'] = trim(substr($line, 8));
                            }
                            // #REVISIT:
                            if (substr($line, 0, 9) == '#REVISIT:') {
                                $fieldsValues['revisit'] = trim(substr($line, 9));
                            }
                            // #SOURCESET:
                            if (substr($line, 0, 11) == '#SOURCESET:') {
                                $fieldsValues['sourceset'] = trim(substr($line, 11));
                            }
                            // #TARGETSET:
                            if (substr($line, 0, 11) == '#TARGETSET:') {
                                $fieldsValues['targetset'] = trim(substr($line, 11));
                            }
                            // #NAME:
                            if (substr($line, 0, 6) == '#NAME:') {
                                $fieldsValues['name'] = trim(substr($line, 6));
                            }
                            // #INSTITUTION:
                            if (substr($line, 0, 13) == '#INSTITUTION:') {
                                $fieldsValues['institution'] = trim(substr($line, 13));
                            }
                            // supported legacy tags for better reharvesting determination
                            // #DATE:
                            if (substr($line, 0, 6) == '#DATE:') {
                                $fieldsValues['date'] = trim(substr($line, 6));
                            }
                            // #REVISIT:
                            if (substr($line, 0, 9) == '#REVISIT:') {
                                $fieldsValues['revisit'] = trim(substr($line, 9));
                            }
                        }

                        // if in the middle of a file set target from provider record (which btw. keeps it for next runs)
                        if (!$fieldsValues['target'] && $currentProvider['target']) {
                            $fieldsValues['target'] = $currentProvider['target'];
                        }

                        // data line (identifier); starts with 0-9, a-z, A-Z
                        if (preg_match('/^[0-9a-zA-Z]/', $line)) {

                            // get data segments
                            $data = GeneralUtility::trimExplode('|', $line);

                            // process data segments; rules @see: http://gbv.github.io/beaconspec/beacon.html#link-construction
                            $linkValues = array();
                            $validSourceIdentifier = true;

                            // segment 1 - always set mandatory source identifier (for GND '-' is also allowed in identifier)
                            (preg_match('/^[a-zA-Z0-9\-]+$/',
                                $data[0])) ? $linkValues['source_identifier'] = $data[0] : $validSourceIdentifier = false;

                            // one data segment (source identifier) given, construct the target identifier from #TARGET + source identifier
                            if (count($data) == 1 && $validSourceIdentifier == true && $fieldsValues['target']) {

                                ($fieldsValues['message']) ? $linkValues['annotation'] = $fieldsValues['message'] : $linkValues['annotation'] = '';
                                $linkValues['target_identifier'] = str_replace('{ID}', $linkValues['source_identifier'],
                                    $fieldsValues['target']);

                                // two data segments given; segment two might contain a full target identifier starting with http:|https:
                            } elseif (count($data) == 2 && $validSourceIdentifier == true) {

                                if (GeneralUtility::isValidUrl($data[1])) {
                                    ($fieldsValues['message']) ? $linkValues['annotation'] = $fieldsValues['message'] : $linkValues['annotation'] = '';
                                    $linkValues['target_identifier'] = $data[1];
                                } else {
                                    $linkValues['annotation'] = $data[1];
                                    $linkValues['target_identifier'] = str_replace('{ID}',
                                        $linkValues['source_identifier'], $fieldsValues['target']);
                                }

                                // three data segments given
                            } elseif (count($data) == 3 && $validSourceIdentifier == true) {

                                // in this case segment 2 is always used as annotation
                                $linkValues['annotation'] = $data[1];

                                // full URL in third segment
                                if (GeneralUtility::isValidUrl($data[2])) {
                                    $linkValues['target_identifier'] = $data[2];
                                    // support for legacy format: source identifier | count | annotation; @example: http://www.historische-kommission-muenchen-editionen.de/beacon_adr.txt
                                } elseif ((int)$data[1] > 0 && GeneralUtility::isValidUrl($data[2]) === false && $fieldsValues['target']) {
                                    $linkValues['target_identifier'] = str_replace('{ID}',
                                        $linkValues['source_identifier'], $fieldsValues['target']);
                                    $linkValues['annotation'] .= '|' . $data[2];
                                    // only identifier in third segment, construct target identifier from #TARGET + source identifier
                                } elseif ($data[2] && $fieldsValues['target']) {
                                    $constructedTargetIdentifier = str_replace('{ID}', $data[2],
                                        $fieldsValues['target']);
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

                                // data line contains wrong number of segments
                            } else {
                                $wrongNumberOfSegments = true;
                            }

                            // if processing of line was valid (based on source identifier and target identifier) prepare record for import
                            if ($validSourceIdentifier === true && GeneralUtility::isValidUrl($linkValues['target_identifier'])) {

                                $linkValues['provider'] = (int)$uid;
                                $linkValues['tstamp'] = time();
                                $linkValues['crdate'] = time();
                                $linkValues['pid'] = 0;
                                $linkValues['hidden'] = 1;

                                // collect new link record for later bulk insertion
                                $rowsToInsert[] = $linkValues;

                                // if it was not valid, data in the line is incorrect
                            } else {
                                $incorrectDataInLines = true;
                            }

                        }

                        // the line is neither empty, nor starts with #, a-z, A-Z, 0-9
                        if (preg_match('/^(?![0-9a-zA-Z\#\n])/', $line)) {
                            $unknownLineFormat = true;
                        }

                        // raise line count
                        $linePointer = $file->key();

                    }
                }

                // logging for incorrect line formats
                if ($emptyLines === true) {
                    $GLOBALS['BE_USER']->simplelog('BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . ' contains some empty lines that were skipped',
                        $extKey = 'beaconizer', 2);
                }
                if ($unknownLineFormat === true) {
                    $GLOBALS['BE_USER']->simplelog('BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . ' contains some lines with an unknown format that were skipped',
                        $extKey = 'beaconizer', 2);
                }
                if ($wrongNumberOfSegments === true) {
                    $GLOBALS['BE_USER']->simplelog('BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . ' contains some lines with wrong number of data segments that were skipped',
                        $extKey = 'beaconizer', 2);
                }
                if ($incorrectDataInLines === true) {
                    $GLOBALS['BE_USER']->simplelog('BEACON file from provider ' . htmlspecialchars($currentProvider['title']) . ' contains some lines with incorrect data that were skipped',
                        $extKey = 'beaconizer', 2);
                }

                // insert new records in a batch
                if (count($rowsToInsert) > 0) {

                    // since doctrine-dbal works with placeholders and there is a placeholder limit
                    // of 65,535 (2^16-1) for MySQL/MariaDB a workaround is needed: chunk the array
                    // into smaller units and bulk insert them one after the other
                    // @see: https://stackoverflow.com/questions/18100782/import-of-50k-records-in-mysql-gives-general-error-1390-prepared-statement-con
                    if (count($rowsToInsert) > 8000) {
                        $bulksToInsert = array_chunk($rowsToInsert, 8000);
                    } else {
                        $bulksToInsert = [0 => $rowsToInsert];
                    }

                    // bulk insert records
                    foreach ($bulksToInsert as $bulk) {

                        GeneralUtility::makeInstance(ConnectionPool::class)
                            ->getConnectionForTable('tx_beaconizer_domain_model_links')
                            ->bulkInsert(
                                'tx_beaconizer_domain_model_links', // from
                                $bulk, // data
                                [
                                    'source_identifier',
                                    'annotation',
                                    'target_identifier',
                                    'provider',
                                    'tstamp',
                                    'crdate',
                                    'pid',
                                    'hidden'
                                ] // columns
                            );

                    }
                }

                // log action
                $GLOBALS['BE_USER']->simplelog(count($rowsToInsert) . ' links have been harvested from provider ' . htmlspecialchars($currentProvider['title']),
                    $extKey = 'beaconizer', 0);

                // in case we have to continue with the next scheduler run keep the temporary file
                if ($noDelete === 1) {

                    $harvestingData['fileName'] = $temporaryBeaconFile;
                    $harvestingData['totalImported'] = $harvestingData['totalImported'] + count($rowsToInsert);
                    $harvestingData['linePointer'] = $linePointer;
                    $fieldsValues['harvesting_data'] = serialize($harvestingData);
                    $fieldsValues['harvesting_timestamp'] = time();

                } else {

                    GeneralUtility::unlink_tempfile($temporaryBeaconFile);
                    $fieldsValues['harvesting_data'] = '';
                    $fieldsValues['harvesting_timestamp'] = time();
                    $GLOBALS['BE_USER']->simplelog('Finished harvesting file from provider ' . htmlspecialchars($currentProvider['title']),
                        $extKey = 'beaconizer', 0);

                    // throw away 'old' links for current provider
                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getQueryBuilderForTable('tx_beaconizer_domain_model_links');
                    $rows = $queryBuilder
                       ->delete('tx_beaconizer_domain_model_links')
                       ->where(
                          $queryBuilder->expr()->gt('pid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                          $queryBuilder->expr()->eq('provider', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
                       )
                       ->execute();

                    // rotate newly harvested links from pid 0 to target pid
                    ($this->importOnPid > 0) ? $pid = (int)$this->importOnPid : $pid = (int)$currentProvider['pid'];

                    GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getConnectionForTable('tx_beaconizer_domain_model_links')
                        ->update(
                            'tx_beaconizer_domain_model_links',
                            [ 'pid' => (int)$pid, 'hidden' => 0 ], // set
                            [ 'provider' => (int)$uid ] // where
                        );

                    $GLOBALS['BE_USER']->simplelog('Rotating harvested links for provider ' . htmlspecialchars($currentProvider['title']) . ' to page with ID ' . $pid,
                        $extKey = 'beaconizer', 0);
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
                GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable('tt_content')
                    ->update(
                        'tx_beaconizer_domain_model_providers',
                        $fieldsValues, // set
                        [ 'uid' => (int)$uid ] // where
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
    public function getAdditionalInformation()
    {
        $selectedProviders = array();
        foreach ($this->providersToHarvest as $uid) {

            $currentProvider = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_beaconizer_domain_model_providers')
                ->select(
                    ['title'], // fields
                    'tx_beaconizer_domain_model_providers', // from
                    [ 'uid' => (int)$uid ] // where
                )->fetch();

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
     * @param array  $provider
     *
     * @return boolean
     */
    private function shouldFileBeHarvested($beaconFileContent, $provider)
    {

        $result = false;
        $metadataTagMatches = array();

        // if file carries a timestamp, always take this as basis for comparison
        preg_match('/^(#TIMESTAMP:)(.*?)$/m', $beaconFileContent, $timestampMatches);
        // if timestamp in file is newer than timestamp in provider record, harvest
        if (strftime(trim($provider['timestamp'])) < strftime(trim($timestampMatches[2]))) {
            $result = true;
            $metadataTagMatches['timestamp'] = $timestampMatches[2];
        }

        // if no timestamp could be retrieved, check if file has UPDATE metadata information
        preg_match('/^(#UPDATE:)(.*?)$/m', $beaconFileContent, $updateMatches);
        if ($result === false && is_array($updateMatches)) {
            $metadataTagMatches['update'] = $updateMatches[2];
            // get the time difference between now and the (last) harvesting timestamp
            $timeDifference = time() - (int)$provider['harvesting_timestamp'];
            switch (trim($updateMatches[2])) {
                case 'always':
                    $result = true;
                    break;
                case 'hourly':
                    if ($timeDifference > 3600) {
                        $result = true;
                    }
                    break;
                case 'daily':
                    if ($timeDifference > 86400) {
                        $result = true;
                    }
                    break;
                case 'weekly':
                    if ($timeDifference > 604800) {
                        $result = true;
                    }
                    break;
                case 'monthly':
                    if ($timeDifference > 2629743) {
                        $result = true;
                    }
                    break;
                case 'yearly':
                    if ($timeDifference > 31556926) {
                        $result = true;
                    }
                    break;
                case 'never':
                default:
                    // nothing done, result stays FALSE
                    break;
            }
        }

        // if legacy date tag is newer than date tag in provider record, harvest
        preg_match('/^(#DATE:)(.*?)$/m', $beaconFileContent, $dateMatches);
        if ($result === false && is_array($dateMatches) && strftime(trim($provider['date'])) < strftime(trim($dateMatches[2]))) {
            $result = true;
            $metadataTagMatches['date'] = $dateMatches[2];
        }
        /*
         * if legacy revisit tag is newer than revisit tag in provider record, harvest; revisit just treated as another timestamp and not in the sense of a
         * 'the time now is past revisit time, please harvested' mechanism since it is very often not updated in older BEACON files which
         * would result in the file always being harvested
         */
        preg_match('/^(#REVISIT:)(.*?)$/m', $beaconFileContent, $revisitMatches);
        if ($result === false && is_array($revisitMatches) && strftime(trim($provider['revisit'])) < strftime(trim($revisitMatches[2]))) {
            $result = true;
            $metadataTagMatches['revisit'] = $revisitMatches[2];
        }

        // if none of the four date/time base metadata tags could be found, reharvesting cannot be determined
        if (empty($metadataTagMatches)) {
            $GLOBALS['BE_USER']->simplelog('Neither a timestamp, an update, date or revisit field could be found in file from provider ' . htmlspecialchars($provider['title']) . '. Harvesting cannot be determined',
                $extKey = 'beaconizer', 0);
        }

        return $result;
    }
}
