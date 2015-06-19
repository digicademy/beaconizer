.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Resources
---------

BEACON origins and BEACON specification
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

BEACON originated around the German Wikpedia project with the need to share links between
Wikipedia articles and related sources on the web using identifiers from the authority file
of the `German National Library <https://en.wikipedia.org/wiki/Integrated_Authority_File>`_.
Since then an official `BEACON specification <https://github.com/gbv/beaconspec>`_ is in the
making. The plan ist to eventually submit it to the Internet Engineering Task Force as a RFC.
A HTML version of the specification can be found at:

`<http://gbv.github.io/beaconspec/beacon.html>`_.

The Beaconizer is closely modelled to this specification. For reasons of
standard compliancy it supports the structure of BEACON files and all metadata fields
exactly as suggested in the specification. Variant forms of BEACON files
out there in the wild can be harvested with the Beaconizer, but deviations in
structure or metadata fields will be ignored.

Further information
^^^^^^^^^^^^^^^^^^^

Here are some other interesting articles about BEACON:

- The `English Wikipedia <http://meta.wikimedia.org/wiki/Dynamic_links_to_external_resources>`_ has some very good explanations and examples of the BEACON file format

- This `page in the German Wikipedia <http://meta.wikimedia.org/wiki/Dynamic_links_to_external_resources>`_ is the most complete list of available BEACON files on the web (up to now)

- A `recent blog article (in German) <http://djgd.hypotheses.org/672>`_ about the usefulness of BEACON files