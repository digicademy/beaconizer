<?php

namespace ADWLM\Beaconizer\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use ADWLM\Beaconizer\Domain\Model\Links;
use ADWLM\Beaconizer\Domain\Model\Providers;

/**
 * Service that connects to the beacon.findbuch.de webservice and looks up submitted sourceIdentifiers.
 * If BEACON providers are submitted, the response from the webservice is filtered for links from
 * this providers using the link pattern specified in each provider. The Service returns an object
 * storage with link objects to the calling method.
 */
class BeaconFindbuchService implements SingletonInterface
{

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Fetches links from the beacon.findbuch.de webservice, possibly filtering the result by
     * specific providers.
     *
     * @param string $sourceIdentifier
     * @param array  $providers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function retrieveLinks($sourceIdentifier = '', $providers = array())
    {
        // initialize
        $result = $this->objectManager->get(ObjectStorage::class);
        // if sourceIdentifier was submitted, ask the webservice
        if ($sourceIdentifier) {
            // send request to beacon.findbuch.de/pnd-aks
            $response = $this->sendRequest($sourceIdentifier);
            // if the webservice returned a result (= DBN link exists as first entry in the result array)
            if ($response[0]) {
                // use property mapper for building link objects
                $propertyMapper = $this->objectManager->get(PropertyMapper::class);
                // traverse result and transform to link object
                foreach ($response[3] as $key => $targetIdentifier) {
                    $linkObject = $propertyMapper->convert(array(
                        'sourceIdentifier' => $sourceIdentifier,
                        'targetIdentifier' => $targetIdentifier
                    ), Links::class);
                    $providerObject = $propertyMapper->convert(array('title' => $response[1][$key]), Providers::class);
                    $linkObject->setProvider($providerObject);
                    $result->attach($linkObject);
                }
                // possibly filter result by provider link patterns
                if (!empty($providers)) {
                    $result = $this->filterResultByProvider($result, $providers);
                }
            }
        }

        // return results
        return $result;
    }

    /**
     * Sends submitted sourceIdentifier to the beacon.findbuch.de webservice and returns the result
     * as json decoded array.
     *
     * @param string $sourceIdentifier
     *
     * @return array
     */
    private function sendRequest($sourceIdentifier)
    {
        return json_decode(GeneralUtility::getUrl('http://beacon.findbuch.de/seealso/pnd-aks?format=seealso&id=' . $sourceIdentifier,
            false));
    }

    /**
     * Reconstitutes the submitted providers (uids) to provider objects. Returns an array where the keys are
     * the uids and the values the provider objects. This is used during filtering of link objects where the
     * current provider object is attached to a link object if the link pattern of the provider matches.
     *
     * @param array $providers
     *
     * @return array
     */
    private function reconstituteProviders($providers)
    {
        $reconstitutedProviders = array();
        $propertyMapper = $this->objectManager->get(PropertyMapper::class);
        foreach ($providers as $provider) {
            $providerObject = $propertyMapper->convert($provider, Providers::class);
            $reconstitutedProviders[$providerObject->getUid()] = $providerObject;
        }

        return $reconstitutedProviders;
    }

    /**
     * Filters an object storage of link objects by link patterns from submitted providers. The pattern
     * matching compares the beginning of links with the string given in the $linkPattern property of each
     * provider. The beginning of BEACON links is a comparatively good identifier for specific providers
     * since all links of a provider ideally should be in the same (the provider's) "namespace".
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $result
     * @param array                                        $providers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    private function filterResultByProvider(ObjectStorage $result, $providers)
    {
        $filteredResult = $this->objectManager->get(ObjectStorage::class);
        $reconstitutedProviders = $this->reconstituteProviders($providers);
        $linkPatterns = array();
        foreach ($reconstitutedProviders as $provider) {
            if ($provider->getLinkPattern()) {
                $linkPatterns[$provider->getUid()] = $provider->getLinkPattern();
            }
        }
        if (!empty($linkPatterns)) {
            foreach ($result as $linkObject) {
                $targetIdentifier = $linkObject->getTargetIdentifier();
                foreach ($linkPatterns as $key => $pattern) {
                    if (GeneralUtility::isFirstPartOfStr($targetIdentifier, $pattern)) {
                        $linkObject->setProvider($reconstitutedProviders[$key]);
                        $filteredResult->attach($linkObject);
                        break;
                    }
                }
            }
        }

        return $filteredResult;
    }
}
