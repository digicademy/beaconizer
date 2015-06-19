.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Introduction
------------

What does it do?
^^^^^^^^^^^^^^^^

With the Beaconizer you can harvest links based on authority files (BEACON files), enrich
your detail views with context related links ("see also" links) and open
your data to external applications and webservices via dynamically generated BEACON files.

BEACON what? BEACON is a simple file format to exchange hyperlinks. A BEACON file
contains a 1-to-1 (or 1-to-n) mapping from identifiers to links. Each link consists of
at least an URL with an optional annotation (you can read `more about the
format and it's purposes in Wikipedia <https://meta.wikimedia.org/wiki/Dynamic_links_to_external_resources>`_).

Why is this cool? Because with a BEACON file you connect your data to the outside world via harvestable
links. At the same time you can provide your users with context related links in your detail views.
Have a look at `this poster <http://eprints.rclis.org/15407/2/isi2011_beacon_poster.pdf>`_ which
succinctly explains the benefits of using BEACON.

Features
^^^^^^^^

The Beaconizer features

- a scheduler job for harvesting links from BEACON files

- a BEACON generator for your data (you can map any table)

- a SeeAlso plugin for inclusion in your detail views (usable with any Extbase extension)

Screenshots
^^^^^^^^^^^

The SeeAlso plugin automatically enriches your detail views with links to further information
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

.. figure:: ../Images/see-also.png
   :alt: SeeAlso plugin

Scheduler task for harvesting link data from BEACON file providers
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

.. figure:: ../Images/scheduler-task.png
   :alt: Scheduler task

Credits
^^^^^^^

This extension is being developed by the `Digital Academy <http://www.digitale-akademie.de/>`_
of the `Academy of Sciences and Literature | Mainz <http://www.adwmainz.de>`_ in the context of
our `Digital Humanities Projects <http://www.digitale-akademie.de/projekte/matrix.html>`_.


Join development
^^^^^^^^^^^^^^^^

The Beaconizer is *beta software* at the moment. This means, all features are considered stable
enough to be used in production. But now is the time to test, test, test.. The development takes
place on `Github <https://github.com/digicademy/beaconizer>`_. You are very welcome to join us
if you wish to take part in the development or if you have found a bug.