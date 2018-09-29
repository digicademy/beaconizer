<?php

namespace ADWLM\Beaconizer\Task;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class AdditionalFieldProvider implements AdditionalFieldProviderInterface
{

    /**
     * Gets additional fields to render in the form to add/edit a task
     *
     * @param array                                                     $taskInfo        Values of the fields from the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask                    $task            The task object being edited. Null when adding a task!
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     *
     * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule)
    {

        // forceHarvesting field
        if ($schedulerModule->CMD == 'edit') {
            if ($task->forceHarvesting === true) {
                $checked = 'checked="checked" ';
            }
        } else {
            $checked = '';
        }

        $fieldName = 'tx_scheduler[beaconizer_forceHarvesting]';
        $fieldId = 'beaconizer_forceHarvesting';
        $fieldHtml = '<input name="' . $fieldName . '" id="' . $fieldId . '" ' . $checked . ' class="checkboxes" type="checkbox" />';
        $additionalFields[$fieldId] = array(
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.forceHarvesting',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId
        );

        // totalPerRun field
        if ($schedulerModule->CMD == 'edit') {
            if ((int)$task->totalPerRun <= 0) {
                $totalPerRunValue = '';
            } else {
                $totalPerRunValue = (int)$task->totalPerRun;
            }
        }

        $fieldName = 'tx_scheduler[beaconizer_totalPerRun]';
        $fieldId = 'beaconizer_totalPerRun';
        $fieldHtml = '<input name="' . $fieldName . '" id="' . $fieldId . '" type="input" size="15" value="' . $totalPerRunValue . '" />';
        $additionalFields[$fieldId] = array(
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.totalPerRun',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId
        );

        // importOnPid field
        if ($schedulerModule->CMD == 'edit') {
            if ((int)$task->importOnPid <= 0) {
                $importOnPid = '';
            } else {
                $importOnPid = (int)$task->importOnPid;
            }
        }

        $fieldName = 'tx_scheduler[beaconizer_importOnPid]';
        $fieldId = 'beaconizer_importOnPid';
        $fieldHtml = '<input name="' . $fieldName . '" id="' . $fieldId . '" type="input" size="5" value="' . $importOnPid . '" />';
        $additionalFields[$fieldId] = array(
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.importOnPid',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId
        );

        // providersToHarvest field
        if (empty($taskInfo['beaconizer_providersToHarvest'])) {
            $taskInfo['beaconizer_providersToHarvest'] = array();
            if ($schedulerModule->CMD == 'add') {
            } elseif ($schedulerModule->CMD == 'edit') {
                // In case of editing the task, set to currently selected value
                $taskInfo['beaconizer_providersToHarvest'] = $task->providersToHarvest;
            }
        }

        // provider selection
        $fieldName = 'tx_scheduler[beaconizer_providersToHarvest][]';
        $fieldId = 'beaconizer_providersToHarvest';
        $fieldOptions = $this->getFieldProviderOptions($taskInfo['beaconizer_providersToHarvest']);
        $fieldHtml = '<select name="' . $fieldName . '" id="' . $fieldId . '" class="wide" size="10" multiple="multiple">' . $fieldOptions . '</select>';
        $additionalFields[$fieldId] = array(
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.providersToHarvest',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId
        );

        return $additionalFields;
    }

    /**
     * Validates the additional fields' values
     *
     * @param array                                                     $submittedData   An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     *
     * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule)
    {
        $result = true;

        if (!isset($submittedData['beaconizer_forceHarvesting'])) {
            $result = true;
        } elseif ($submittedData['beaconizer_forceHarvesting'] === 'on') {
            $result = true;
        } else {
            $result = false;
        }

        $submittedData['beaconizer_totalPerRun'] = (int)$submittedData['beaconizer_totalPerRun'];
        $submittedData['beaconizer_importOnPid'] = (int)$submittedData['beaconizer_importOnPid'];

        if (!is_array($submittedData['beaconizer_providersToHarvest'])) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.invalidProviderSelection'),
                FlashMessage::ERROR);
            $result = false;
        } else {
            $availableProviders = $this->getAvailableProviders();
            foreach ($availableProviders as $provider) {
                $validProviderUids[] = $provider['uid'];
            }
            foreach ($submittedData['beaconizer_providersToHarvest'] as $submittedProvider) {
                if (!in_array($submittedProvider, $validProviderUids)) {
                    $schedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:beaconizer/Resources/Private/Language/locallang_db.xlf:tx_beaconizer_harvestingTask.invalidProviderUid'),
                        FlashMessage::ERROR);
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Takes care of saving the additional fields' values in the task's object
     *
     * @param array                                  $submittedData An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task          Reference to the scheduler backend module
     *
     * @return void
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
// @TODO: sanitize $task->providersToHarvest ?
        $task->providersToHarvest = $submittedData['beaconizer_providersToHarvest'];
        $task->totalPerRun = (int)$submittedData['beaconizer_totalPerRun'];
        $task->importOnPid = (int)$submittedData['beaconizer_importOnPid'];
        $task->forceHarvesting = $submittedData['beaconizer_forceHarvesting'] === 'on' ? true : false;
    }

    /**
     * Build select options of available BEACON providers and set currently selected providers
     *
     * @param array $selectedProviders Selected providers
     *
     * @return string HTML of selectbox options
     */
    protected function getFieldProviderOptions(array $selectedProviders)
    {
        $options = array();

        // get available providers DB query
        $availableProviders = $this->getAvailableProviders();

        foreach ($availableProviders as $provider) {
            if (in_array($provider['uid'], $selectedProviders)) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $options[] = '<option value="' . (int)$provider['uid'] . '" ' . $selected . '>' . htmlspecialchars($provider['title']) . '</option>';
        }

        return implode('', $options);
    }

    /**
     * Get the available BEACON providers in the system
     *
     * @return array The available providers uids and titles
     */
    protected function getAvailableProviders()
    {

        // get available providers by DB query
        $availableProviders = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid,title',
            'tx_beaconizer_domain_model_providers',
            'deleted = 0 AND hidden = 0',
            null,
            'title ASC',
            null,
            null
        );

        if (!is_array($availableProviders)) {
            $availableProviders = array();
        }

        return $availableProviders;
    }

}
