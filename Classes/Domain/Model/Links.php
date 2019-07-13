<?php

namespace ADWLM\Beaconizer\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Harvested BEACON links
 */
class Links extends AbstractEntity
{

    /**
     * Source identifier (GND, VIAF, etc.)
     *
     * @var string $sourceIdentifier
     * @validate NotEmpty
     */
    protected $sourceIdentifier;

    /**
     * Provider of the link
     *
     * @var \ADWLM\Beaconizer\Domain\Model\Providers $provider
     */
    protected $provider;

    /**
     * Annotation
     *
     * @var string $annotation
     */
    protected $annotation;

    /**
     * Target identifier
     *
     * @var string $targetIdentifier
     */
    protected $targetIdentifier;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Returns the sourceIdentifier
     *
     * @return string
     */
    public function getSourceIdentifier()
    {
        return $this->sourceIdentifier;
    }

    /**
     * Sets the sourceIdentifier
     *
     * @param string $sourceIdentifier
     *
     * @return void
     */
    public function setSourceIdentifier($sourceIdentifier)
    {
        $this->sourceIdentifier = $sourceIdentifier;
    }

    /**
     * Returns the provider
     *
     * @return \ADWLM\Beaconizer\Domain\Model\Providers $provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Sets the provider
     *
     * @param \ADWLM\Beaconizer\Domain\Model\Providers $provider
     *
     * @return void
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Returns the annotation
     *
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * Sets the annotation
     *
     * @param string $annotation
     *
     * @return void
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * Returns the targetIdentifier
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return $this->targetIdentifier;
    }

    /**
     * Sets the targetIdentifier
     *
     * @param string $targetIdentifier
     *
     * @return void
     */
    public function setTargetIdentifier($targetIdentifier)
    {
        $this->targetIdentifier = $targetIdentifier;
    }

}
