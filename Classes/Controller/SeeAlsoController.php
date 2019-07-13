<?php

namespace ADWLM\Beaconizer\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Controller for outputting data as BEACON file
 */
class SeeAlsoController extends ActionController
{

    /**
     * BEACON link repository
     *
     * @var \ADWLM\Beaconizer\Domain\Repository\LinksRepository
     * @inject
     */
    protected $linksRepository;

    /**
     * beacon.findbuch.de webservice
     *
     * @var \ADWLM\Beaconizer\Service\BeaconFindbuchService
     * @inject
     */
    protected $beaconFindbuchService;

    /**
     * Walks through current request arguments (but only if no identifier arguments is set) based on TypoScript
     * configuration and, if a matching object is found, maps this object, tries to retrieve its identifier,
     * and sets it as identifier argument for the lookUp action
     *
     * @return void
     * @throws
     */
    public function initializeAction()
    {

        if ($this->settings['objectMapping'] && !$this->request->hasArgument('sourceIdentifier')) {
            foreach ($this->settings['objectMapping'] as $mappedObject => $values) {
                if ($values['argumentName']) {

                    if ($values['pluginNamespace']) {
                        $foreignPluginVars = GeneralUtility::_GPmerged($values['pluginNamespace']);
                        if ($foreignPluginVars[$values['argumentName']] > 0) {
                            $foreignObjectUid = (int)$foreignPluginVars[$values['argumentName']];
                        }
                    } else {
                        $getParameters = GeneralUtility::_GET();
                        $postParameters = GeneralUtility::_POST();
                        if ($getParameters[$values['argumentName']] > 0) {
                            $foreignObjectUid = (int)$getParameters[$values['argumentName']];
                        } elseif ($postParameters[$values['argumentName']] > 0) {
                            $foreignObjectUid = (int)$postParameters[$values['argumentName']];
                        }
                    }

                    if ($foreignObjectUid > 0) {
                        $propertyMapper = $this->objectManager->get(PropertyMapper::class);
                        $foreignObject = $propertyMapper->convert($foreignObjectUid, $mappedObject);
                        if ($foreignObject instanceof $mappedObject) {
                            $reflectionService = $this->objectManager->get(ReflectionService::class);
                            $sourceIdentifierGetter = 'get' . ucfirst($values['sourceIdentifierProperty']);
                            if ($reflectionService->hasMethod($mappedObject, $sourceIdentifierGetter)) {
                                $sourceIdentifier = $foreignObject->$sourceIdentifierGetter();
                                if ($sourceIdentifier) {
                                    $this->request->setArgument('sourceIdentifier', $sourceIdentifier);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($this->settings['objectMapping'] && !$this->request->hasArgument('sourceIdentifier')) {
            foreach ($this->settings['objectMapping'] as $mappedObject => $values) {
                if ($values['pluginNamespace'] && $values['argumentName']) {
                    $foreignPluginVars = GeneralUtility::_GPmerged($values['pluginNamespace']);
                    if ($foreignPluginVars[$values['argumentName']] > 0) {
                        $foreignObjectUid = (int)$foreignPluginVars[$values['argumentName']];
                        $propertyMapper = $this->objectManager->get(PropertyMapper::class);
                        $foreignObject = $propertyMapper->convert($foreignObjectUid, $mappedObject);
                        if ($foreignObject instanceof $mappedObject) {
                            $reflectionService = $this->objectManager->get(ReflectionService::class);
                            $sourceIdentifierGetter = 'get' . ucfirst($values['sourceIdentifierProperty']);
                            if ($reflectionService->hasMethod($mappedObject, $sourceIdentifierGetter)) {
                                $sourceIdentifier = $foreignObject->$sourceIdentifierGetter();
                                if ($sourceIdentifier) {
                                    $this->request->setArgument('sourceIdentifier', $sourceIdentifier);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Looks up links by incoming GND, either internally in DB or the against beacon.findbuch.de webservice.
     *
     * @param string $sourceIdentifier
     *
     * @return string The rendered view
     */
    public function lookUpAction($sourceIdentifier = '')
    {

        $providers = GeneralUtility::intExplode(',', $this->settings['general']['providers'], true);

        switch ((int)$this->settings['general']['mode']) {
            case 2:
                $links = $this->beaconFindbuchService->retrieveLinks($sourceIdentifier, $providers);
                break;
            case 1:
            default:
                $links = $this->linksRepository->findBySourceIdentifier($sourceIdentifier, $providers);
                break;
        }

        $this->view->assign('links', $links);
    }

}
