<?php
namespace ADWLM\Beaconizer\Controller;
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

use \TYPO3\CMS\Extbase\Exception;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for outputting data as BEACON file
 */
class GeneratorController extends ActionController {

	/**
	 * BEACON link repository
	 *
	 * @var \ADWLM\Beaconizer\Domain\Repository\LinksRepository
	 * @inject
	 */
	protected $linksRepository;

	/**
	 * Routes to the beacon action setting format and type parameters
	 *
	 * @return void
	 */
	public function routingAction() {
		$pluginUid = $this->configurationManager->getContentObject()->data['uid'];
		$this->uriBuilder->setRequest($this->request);
		$this->uriBuilder->setTargetPageType(1789);
		$this->uriBuilder->setFormat('txt');
		$uri = $this->uriBuilder->uriFor('beacon', array('plugin' => $pluginUid));
		$this->redirectToURI($uri);
	}

	/**
	 * Outputs records from a configured table generically as BEACON file
	 *
	 * @return string The rendered view
	 */
	public function beaconAction() {

			/*
			 * the following would have been nice but there is a problem due to cached TS: once a tablename mapping is defined in TS,
			 * it will apply to all instances of the plugin. Handling two different mapped tables on different pages therefore fails for
			 * the second table
			 */
		// $objects = $this->generatorRepository->findAll();

		$configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$tableName = $configuration['persistence']['classes']['ADWLM\Beaconizer\Domain\Model\GeneratedLinks']['mapping']['tableName'];

		if ($tableName) {
			$cObj = $this->configurationManager->getContentObject();
			$rowsToMap = $this->linksRepository->findRowsToMap($tableName, $cObj);
			if (count($rowsToMap) > 0) {
				$dataMapper = $this->objectManager->get('\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Mapper\\DataMapper');
				$links = $dataMapper->map('\\ADWLM\\Beaconizer\\Domain\\Model\\GeneratedLinks', $rowsToMap);
				$this->view->assign('links', $links);
			}
		} else {
			throw new Exception('No table name provided in the TypoScript configuration for the plugin.', 1391578331);
		}

			// make sure to return text/plain
		$this->response->setHeader('Content-Type', 'text/plain', TRUE);
	}

}
?>