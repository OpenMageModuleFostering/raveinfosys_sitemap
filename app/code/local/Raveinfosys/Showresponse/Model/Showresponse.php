<?php

class Raveinfosys_Showresponse_Model_Showresponse extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('showresponse/showresponse');
    }
	
	public function insertdata($google_response, $bing_response, $window_response, $yahoo_response, $date)
	{
	  
	  $resource = Mage::getSingleton('core/resource');
      $writeConnection = $resource->getConnection('core_write');
      $tableName = $resource->getTableName('showresponse/showresponse');
	  $query_showmap_response = "INSERT INTO $tableName (`google_response`,`bing_response` ,`window_response`,      `yahoo_response`,`date`) VALUES ('$google_response', '$bing_response', '$window_response', '$yahoo_response', '$date')";
			   
      $writeConnection->query($query_showmap_response);
	 
	}
	
	public function getRow()
	{
	  
	  $resource = Mage::getSingleton('core/resource');  
      $readconnection = $resource->getConnection('core_read');
      $tableName = $resource->getTableName('showmap/showmap');
      $sql = "select * from $tableName
                 where id=(select max(id) from $tableName)";
	  $row = $readconnection->fetchRow($sql);
	  return $row;
	  
	}
}