<?php

namespace ADWLM\Beaconizer\Domain\Repository;

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
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Doctrine\DBAL\Connection;

/**
 * Repository for BEACON links
 */
class LinksRepository extends Repository
{

    /**
     * Performs a findBy for the submitted source identifier, possibly taking selected providers into account as constraint
     *
     * @param string $sourceIdentifier
     * @param array  $providers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     * @throws
     */
    public function findBySourceIdentifier($sourceIdentifier, $providers = array())
    {

        // initialize query object
        $query = $this->createQuery();

        // prepare constraints array
        $constraints = array();

        // possibly set provider constraint
        if (!empty($providers)) {
            $constraints[] = $query->like('sourceIdentifier', $sourceIdentifier);
            $constraints[] = $query->in('provider', $providers);
            $query->matching($query->logicalAnd($constraints));
        } else {
            $query->matching($query->like('sourceIdentifier', $sourceIdentifier));
        }

        // set ordering
        $query->setOrderings(
            array('provider.title' => QueryInterface::ORDER_ASCENDING)
        );

        // execute the query
        return $query->execute();
    }

    /**
     * Gets rows from the specified table and returns them to the controller
     *
     * @param string $tableName The name of the table to output BEACON data for
     * @param object $cObj      Instance of the current content object
     *
     * @return mixed
     */
    public function findRowsToMap($tableName, $cObj)
    {
        $pages = GeneralUtility::intExplode(',', $cObj->data['pages']);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $result = $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->in('pid', ':pages')
            )
            ->setParameter('pages', $pages, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        return $result;

    }
}
