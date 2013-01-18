<?php

$installer = $this;

$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('datadrivendesign_knowledgebase_document')};
CREATE TABLE {$this->getTable('datadrivendesign_knowledgebase_document')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `doc_type` varchar(255) NOT NULL default '',
  `published` varchar(12),
  `doc_id` varchar(255) NOT NULL default '',
  `access_key` varchar(255) NOT NULL default '',
  `secret_password` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `category_ids` varchar(255) NOT NULL default '',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 