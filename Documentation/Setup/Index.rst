.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Setup
-----

All three components of the TYPO3 Beaconizer can be used independently from each other. There is
no obligation to harvest links from BEACON files. You can use the *SeeAlso plugin* in standalone
mode together with the webservice at *beacon.findbuch.de*. The BEACON generator can also be used
standalone. Everything really depends on your use case and your data.

Only two things are needed:

- Your data (pages, records in an extbase extensions etc.) should use identifiers
  from an authority file (`GND <http://www.dnb.de/DE/Standardisierung/GND/gnd_node.html>`_, `VIAF <http://viaf.org/>`_ etc.)
- You need to create some BEACON provider records which point to the BEACON files
  you wish to use

Please read the following sections of the manual on how to do that.

.. toctree::
	:maxdepth: 5
	:titlesonly:
	:glob:

	CreatingProviders/Index
	HarvestingLinks/Index
	GeneratorPlugin/Index
	SeeAlsoPlugin/Index