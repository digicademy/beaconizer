<?php

namespace ADWLM\Beaconizer\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  2018 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
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
 * BEACON Providers
 */
class Providers extends AbstractEntity
{

    /**
     * Title
     *
     * @var string $title
     * @validate NotEmpty
     */
    protected $title;

    /**
     * Link pattern
     *
     * @var string $linkPattern
     */
    protected $linkPattern;

    /**
     * Prefix
     *
     * @var string $prefix
     */
    protected $prefix;

    /**
     * Target
     *
     * @var string $target
     */
    protected $target;

    /**
     * Relation
     *
     * @var string $relation
     */
    protected $relation;

    /**
     * Message
     *
     * @var string $message
     */
    protected $message;

    /**
     * Annotation
     *
     * @var string $annotation
     */
    protected $annotation;

    /**
     * Details to the links
     *
     * @var string $description
     */
    protected $description;

    /**
     * Creator
     *
     * @var string $creator
     */
    protected $creator;

    /**
     * Contact
     *
     * @var string $contact
     */
    protected $contact;

    /**
     * Homepage
     *
     * @var string $homepage
     */
    protected $homepage;

    /**
     * Feed
     *
     * @var string $feed
     */
    protected $feed;

    /**
     * Timestamp
     *
     * @var \DateTime $timestamp
     */
    protected $timestamp;

    /**
     * Update
     *
     * @var string $updateInformation
     */
    protected $updateInformation;

    /**
     * Sourceset
     *
     * @var string $sourceset
     */
    protected $sourceset;

    /**
     * Targetset
     *
     * @var string $targetset
     */
    protected $targetset;

    /**
     * Name
     *
     * @var string $name
     */
    protected $name;

    /**
     * Institution
     *
     * @var string $institution
     */
    protected $institution;

    /**
     * HarvestingData
     *
     * @var string $harvestingData
     */
    protected $harvestingData;

    /**
     * HarvestingTimestamp
     *
     * @var \DateTime $harvestingTimestamp
     */
    protected $harvestingTimestamp;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the linkPattern
     *
     * @return string
     */
    public function getLinkPattern()
    {
        return $this->linkPattern;
    }

    /**
     * Sets the linkPattern
     *
     * @param string $linkPattern
     *
     * @return void
     */
    public function setLinkPattern($linkPattern)
    {
        $this->linkPattern = $linkPattern;
    }

    /**
     * Returns the prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix
     *
     * @param string $prefix
     *
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns the target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the target
     *
     * @param string $target
     *
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Returns the relation
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Sets the relation
     *
     * @param string $relation
     *
     * @return void
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * Returns the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message
     *
     * @param string $message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
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
     * Returns the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the creator
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Sets the creator
     *
     * @param string $creator
     *
     * @return void
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * Returns the contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Sets the contact
     *
     * @param string $contact
     *
     * @return void
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Returns the homepage
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Sets the homepage
     *
     * @param string $homepage
     *
     * @return void
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * Returns the feed
     *
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Sets the feed
     *
     * @param string $feed
     *
     * @return void
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * Returns the timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return void
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Returns the updateInformation
     *
     * @return string
     */
    public function getUpdateInformation()
    {
        return $this->updateInformation;
    }

    /**
     * Sets the updateInformation
     *
     * @param string $updateInformation
     *
     * @return void
     */
    public function setUpdateInformation($updateInformation)
    {
        $this->updateInformation = $updateInformation;
    }

    /**
     * Returns the sourceset
     *
     * @return string
     */
    public function getSourceset()
    {
        return $this->sourceset;
    }

    /**
     * Sets the sourceset
     *
     * @param string $sourceset
     *
     * @return void
     */
    public function setSourceset($sourceset)
    {
        $this->sourceset = $sourceset;
    }

    /**
     * Returns the targetset
     *
     * @return string
     */
    public function getTargetset()
    {
        return $this->targetset;
    }

    /**
     * Sets the targetset
     *
     * @param string $targetset
     *
     * @return void
     */
    public function setTargetset($targetset)
    {
        $this->targetset = $targetset;
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Sets the institution
     *
     * @param string $institution
     *
     * @return void
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    /**
     * Returns the harvestingData
     *
     * @return string
     */
    public function getHarvestingData()
    {
        return $this->harvestingData;
    }

    /**
     * Sets the harvestingData
     *
     * @param string $harvestingData
     *
     * @return void
     */
    public function setHarvestingData($harvestingData)
    {
        $this->harvestingData = $harvestingData;
    }

    /**
     * Returns the harvestingTimestamp
     *
     * @return \DateTime
     */
    public function getHarvestingTimestamp()
    {
        return $this->harvestingTimestamp;
    }

    /**
     * Sets the harvestingTimestamp
     *
     * @param \DateTime $harvestingTimestamp
     *
     * @return void
     */
    public function setHarvestingTimestamp($harvestingTimestamp)
    {
        $this->harvestingTimestamp = $harvestingTimestamp;
    }

}
