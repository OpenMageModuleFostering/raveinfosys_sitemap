<?php

class Raveinfosys_Showmap_Model_Showmap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();  
        $this->_init('showmap/showmap');

    }
	
	public function insertdata($ping_interval,$format,$google,$bing,$window,$yahoo,$yahoo_appid)
	{

	  $resource = Mage::getSingleton('core/resource');
      $writeConnection = $resource->getConnection('core_write');
      $tableName = $resource->getTableName('showmap/showmap');
	  $query_showmap = "INSERT INTO $tableName ( `ping_interval`,`format`,`google`,`bing`,`window`,`yahoo`,`yahoo_id`) VALUES   ($ping_interval,'$format','$google','$bing','$window','$yahoo','$yahoo_appid')";
	  $writeConnection->query($query_showmap);
	  
	}
	
	public function getRow()
	{
	  $resource = Mage::getSingleton('core/resource');  
      $readconnection = $resource->getConnection('core_read');
      $tableName = $resource->getTableName('showmap/showmap');
      $sql = "select * from $tableName
                 where showmap_id=(select max(showmap_id) from $tableName)";
	  $row = $readconnection->fetchRow($sql);
	  return $row;
	}
	

	
	public function update()
	{
	      
 			 $google_response = 'Not Configured';
		  	 $yahoo_response = 'Not Configured';
		  	 $bing_response = 'Not Configured';
		  	 $window_response = 'Not Configured';
		  	 $arr_row = $this->getRow();	 
		 
		     $ping_interval = $arr_row['ping_interval'];
			 $format = $arr_row['format'];
		     $google = $arr_row['google'];
			 $yahoo = $arr_row['yahoo'];
			 $bing = $arr_row['bing'];
			 $window = $arr_row['window'];
          
		    
			$base_url = $this->get_url()."sitemap.xml";
   		    $isCurl = function_exists(curl_init);
   		    if($isCurl)
	  	    {
   		      
	 		    
				if($google=='yes')
	 		    {

	    		   $url_google = "http://www.google.com/webmasters/tools/ping?sitemap=".$base_url;
				   $string = $this->get_web_page($url_google);
				   $DOM = new DOMDocument;
   				   $DOM->loadHTML($string);

   				   $items = $DOM->getElementsByTagName('body');
				   $array = array();
  				   for ($i = 0; $i < $items->length; $i++)
       			   $array = $items->item($i)->nodeValue ;
				   $google_response = substr($array,'0',105);
					 
				
	 		    }
	 
			
			    if($yahoo=='yes')
			    {

	               $url_yahoo = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
	  			   $yahoo_response = $this->get_web_page($url_yahoo);
	  			
	 			}
	
			
			    if($bing=='yes')
			    {

				   $url_bing = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
	  			   $bing_response = $this->get_web_page($url_bing);
	  			
	 		    }
				
				if($window=='yes')
			    {

				   $url_window = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
	  			   $window_response = $this->get_web_page($url_window);
	  			
	 		    }
	
	
			 
		    }
	 			 
		  
		    
		  
		  
		  
		  if($google=='yes' || $yahoo=='yes' || $bing=='yes' )
		  {
		  
		       $date = date('Y-m-d h:i:s a', time());
               $showresponse = Mage::getModel('showresponse/showresponse');
			   $showresponse->insertdata($google_response, $bing_response, $window_response, $yahoo_response, $date);
			   
		  }
  	  
	  
	}
	
	
	
 function get_web_page( $url )
 {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $content;
}
 
 
 

 	public function get_url()
 	{
      	$string = Mage::getBaseURL();
	    $string1 = str_replace("index.php/","", $string);
	 	$string2 = str_replace('/','%2F',$string1);
	    $string3 = str_replace(':','%3A',$string2);
	 	return $string3;
	  
 	}
}