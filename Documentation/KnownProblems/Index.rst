.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Known problems
--------------

You can report bugs at our `Github bugtracker <https://github.com/digicademy/beaconizer/issues>`_ .

Known problems in TYPO3 7.6
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Sometimes when using TYPO3 7.6 and the option [SYS][curlUse] some BEACON files can not be fetched
by the scheduler task. You will see errors like '403 forbidden' in Beaconizer log entries even
if you are sure that the BEACON files can be retrieved with a browser.

This problem has nothing to do with the Beaconizer extension. In most cases (for instance
with Wikipedia and Wikimedia) spam protection mechanisms are in effect that block access
to requests that do not identify themselves with a well known user agent. Unfortunately there
is no option in TYPO3 7.6 to configure curl with the appropriate options.

Please see https://forge.typo3.org/issues/28258 and the solution. From TYPO3 8.7
onwards there is no problem because the core uses GuzzleHttp instead of curl.
