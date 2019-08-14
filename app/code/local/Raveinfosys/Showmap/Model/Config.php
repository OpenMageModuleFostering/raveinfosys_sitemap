<?php

class Raveinfosys_Showmap_Model_Config extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct(); 
        $this->_init('showmap/showmap_config');

    }
	
	public function getRow()
	{
	
	    $resource = Mage::getSingleton('core/resource');
        $readconnection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('showmap/config');
	    $sql = "select * from $tableName
                 where id=(select max(id) from $tableName)";
	    $row = $readconnection->fetchRow($sql);
		return $row;
	}
	
	public function insertdata($category,$product,$cms,$other)
	{ 
	  
	  $resource = Mage::getSingleton('core/resource');
      $writeConnection = $resource->getConnection('core_write');
	  $tableName = $resource->getTableName('showmap/config');
	  $query = "INSERT INTO $tableName ( `category`,`product`,`cms`,`other`,`configured`) VALUES ('$category',                      '$product','$cms','$other',1)";
	  $writeConnection->query($query);
	  
	}
	

}