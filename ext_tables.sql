#
# Add SQL definition of database tables
#


#
# Table structure for table 'tx_lsfapi'
#
CREATE TABLE tt_content(
        lsfcourseid varchar(255) DEFAULT '' NOT NULL,
        lsfcoursedata varchar(255) DEFAULT '' NOT NULL,
        
);

CREATE TABLE tx_lsfcoursedata (
	uid int(11) NOT NULL auto_increment,

	course_id varchar(255) DEFAULT '' NOT NULL,
	course_data longtext DEFAULT '' NOT NULL,
	
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
);

CREATE TABLE tx_lsfcoursefilter (
	uid int(11) NOT NULL auto_increment,
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
	pid int(11) DEFAULT '0' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,
    ident varchar(30) DEFAULT '' NOT NULL,

	courseId varchar(255) DEFAULT '' NOT NULL,
	courseName varchar(500) DEFAULT '' NOT NULL,
	title varchar(1500) DEFAULT '' NOT NULL,
	courseType varchar(255) DEFAULT '' ,
	semesterSC varchar(255) DEFAULT '' NOT NULL,
	semester varchar(255) DEFAULT '' NOT NULL,
	sws varchar(255) DEFAULT '' ,
	headerId varchar(255) DEFAULT '' NOT NULL,
	headerName varchar(500) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	
	KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign),
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);

CREATE TABLE tx_lsfscheduledata (
	uid int(11) NOT NULL auto_increment,

	allschedule_data longtext DEFAULT '' NOT NULL,
	
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
);