.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Installation
------------

Requirements
^^^^^^^^^^^^

- You need at least TYPO3 6.2, TYPO3 7.x is supported

Installation
^^^^^^^^^^^^

Import the extension from TER and install. It will add
two new tables to the system (*tx_beaconizer_domain_model_providers* and
*tx_beaconizer_domain_model_links*).

Include the static TypoScript of this extension in your TS template:

.. figure:: ../Images/ts-template.png

Depending on what you want to do you can now create a scheduler task
for harvesting links or put some plugins on your pages for creating BEACON files
or a seeAlso widget.

For the plugins you need to add some additional TypoScript mapping. Please
refer to the according section in the "Setup" chapter of this documentation.