#
# Table structure for table 'tx_beaconizer_domain_model_links'
#
CREATE TABLE tx_beaconizer_domain_model_links (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	source_identifier varchar(255) DEFAULT '' NOT NULL,
	annotation varchar(255) DEFAULT '' NOT NULL,
	target_identifier varchar(255) DEFAULT '' NOT NULL,

	provider int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY provider (provider),
	KEY source_identifier (source_identifier)
);

#
# Table structure for table 'tx_beaconizer_domain_model_providers'
#
CREATE TABLE tx_beaconizer_domain_model_providers (

	# TYPO3 fields

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	link_pattern varchar(255) DEFAULT '' NOT NULL,
	harvesting_data varchar(255) DEFAULT '' NOT NULL,
	harvesting_timestamp int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	# BEACON meta fields according to http://gbv.github.io/beaconspec/beacon.html#meta-fields

	# link construction
	prefix varchar(255) DEFAULT '' NOT NULL,
	target varchar(255) DEFAULT '' NOT NULL,
	relation varchar(255) DEFAULT '' NOT NULL,
	message varchar(255) DEFAULT '' NOT NULL,
	annotation varchar(255) DEFAULT '' NOT NULL,

	# link dump
	description varchar(255) DEFAULT '' NOT NULL,
	creator varchar(255) DEFAULT '' NOT NULL,
	contact varchar(255) DEFAULT '' NOT NULL,
	homepage varchar(255) DEFAULT '' NOT NULL,
	feed varchar(255) DEFAULT '' NOT NULL,
	timestamp varchar(80) DEFAULT '' NOT NULL,
	update_information varchar(80) DEFAULT '' NOT NULL,
	revisit varchar(80) DEFAULT '' NOT NULL,
	date varchar(80) DEFAULT '' NOT NULL,

	# source dataset
	sourceset varchar(255) DEFAULT '' NOT NULL,

	# target dataset
	targetset varchar(255) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	institution varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY link_pattern (link_pattern)
);