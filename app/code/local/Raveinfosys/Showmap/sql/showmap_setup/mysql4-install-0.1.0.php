<?php

$installer = $this;

$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('showmap')};    
	CREATE TABLE {$this->getTable('showmap')} (
  `showmap_id` int(11) NOT NULL auto_increment,  
   `ping_interval` int(12),
   `format` varchar(20),
   `google` varchar(20),
   `bing` varchar(20),
   `window` varchar(20),
   `yahoo` varchar(20),
   `yahoo_id` varchar(20),
   `internal_company_id` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`showmap_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- DROP TABLE IF EXISTS {$this->getTable('showmap_config')};    
	CREATE TABLE {$this->getTable('showmap_config')} (
  `id` int(11) NOT NULL auto_increment,
  `product` varchar(20), 
  `category` varchar(20),
  `cms` varchar(20),
  `other` varchar(20),
  `configured` int(2) NOT NULL DEFAULT 0,
   PRIMARY KEY  (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 