<?php

class Raveinfosys_Showmap_Adminhtml_ShowmapController extends Mage_Adminhtml_Controller_action
{

 public function indexAction()  
 {
        
		$config = Mage::getModel('showmap/config');
		$row = $config->getRow();
		if($row['configured'] == 0)$this->_redirect('*/*/config');
		$content = file_get_contents('sitemap/sitemap.xml');
        $content = str_replace('</urlset>','',$content);
		$content .= '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $count = 0;
		if($row['category'] == 'yes')
		{
		 $cats = Mage::getModel('catalog/category')->load(2)->getChildren();
         $catIds = explode(',',$cats);
		 Mage::register('catIds', $catIds);
		 foreach($catIds as $catId)
		 {
		  if($catId !='')
		  {
		   $category = Mage::getModel('catalog/category')->load($catId);
		   $subCats = Mage::getModel('catalog/category')->load($category->getId())->getChildren();
		   $subCatIds = explode(',',$subCats);
		   $url=$category->getUrl();
		   if(strpos($content,$url) === false)
		   {
            $content .= '<url><loc>'.$url.'</loc>';
            $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	  	    $content .= '<changefreq>daily</changefreq>';
		    $content .= '<priority>0.50</priority>';
		    $content .= '</url>';
		    $count++;
           }
		   if(count($subCatIds) > 1)
		   {
		     foreach($subCatIds as $subCat)
		     {
		       $subCategory = Mage::getModel('catalog/category')->load($subCat);
		       $url=$subCategory->getUrl();
		       if(strpos($content,$url) === false)
		       {
                $content .= '<url><loc>'.$url.'</loc>';
                $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	  	        $content .= '<changefreq>daily</changefreq>';
		        $content .= '<priority>0.50</priority>';
		        $content .= '</url>';
		        $count++;
               }
		     }
	 	   } 
		  } 
		}
	  }
		
		if($row['product'] == 'yes')
		{
		 $product    = Mage::getModel('catalog/product');
         $products   = $product->getCollection()->addStoreFilter($storeId)->getData();
		 Mage::register('products', $products);
	     foreach ($products as $pro) 
         {   
		 
		   $Stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($pro['entity_id'])->getIsinStock();
		   if($Stock)
		   {
		    $_product = $product->load($pro['entity_id']);
	        $url=Mage::getUrl().$_product->getUrlPath();
		    if(strpos($content,$url) === false && $_product->getStatus()!=2)
		    {
             $content .= '<url><loc>'.$url.'</loc>';
             $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	  	     $content .= '<changefreq>daily</changefreq>';
		     $content .= '<priority>0.50</priority>';
		     $content .= '</url>';
		     $count++;
            }
		  }
	     }
	    }
		
		if($row['cms'] == 'yes')
		{
		 $collection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId());
		 $collection->getSelect()
           ->where('is_active = 1');
	     Mage::register('collection', $collection);
	     foreach (Mage::registry('collection') as $page)
	     {
          $PageData = $page->getData();
          if($PageData['identifier']!='no-route' && $PageData['identifier']!='enable-cookies') 
		  {
            $url = Mage::getUrl(). $PageData['identifier'];
		    if(strpos($content,$url) === false)
		    {
             $content .= '<url><loc>'.$url.'</loc>';
             $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	  	     $content .= '<changefreq>daily</changefreq>';
		     $content .= '<priority>0.50</priority>';
		     $content .= '</url>';
		     $count++;
            }
         } 
        }
	 }
		 
		$content .= '</urlset>';
		$url_dir = Mage::getBaseDir()."/sitemap.xml";
		$file_handle = fopen($url_dir,'w');
		fwrite($file_handle,$content);
		fclose($file_handle);
		
        $this->_forward('edit');
			
 }

 public function editAction()
 {
		

		$this->loadLayout();
		$this->_setActiveMenu('showmap/items');
			 
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit'))
			->_addLeft($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_tabs'));

		$this->renderLayout();
		
 }
 
 
 public function saveAction()
 {			

			
         if ($data = $this->getRequest()->getPost()) 
   		 {
		    $google='no';$yahoo='no';$bing='no';$window='no';
			$google_response = 'Not Configured';
			$yahoo_response = 'Not Configured';
			$bing_response = 'Not Configured';
			$window_response = 'Not Configured';
		    $ping_interval=$this->getRequest()->getPost('ping_interval');
		    $format=$this->getRequest()->getPost('format');
		    $ch_google=$this->getRequest()->getPost('google');
			$ch_bing=$this->getRequest()->getPost('bing');
			$ch_window=$this->getRequest()->getPost('window');
			$ch_yahoo=$this->getRequest()->getPost('yahoo');
		    $yahoo_appid=$this->getRequest()->getPost('yahoo_appid');
            $base_url = $this->get_url()."sitemap.xml";
   		    $isCurl = function_exists(curl_init);
   		    if($isCurl)
	  	    {
	 		    
				if($ch_google=='google')
	 		    {
			  	   $google='yes';
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
	 
			
			    if($ch_yahoo=='yahoo' && $yahoo_appid!='')
			    {
	   		       $yahoo='yes';
	               $url_yahoo = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
	  			   $yahoo_response = $this->get_web_page($url_yahoo);
				   
			    }
	
			
			    if($ch_bing=='bing')
			    {
	  			   $bing='yes';
				   $url_bing = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
	  			   $bing_response = $this->get_web_page($url_bing);
				   
	  			
	 		    }
	
	
			    if($ch_window=='window')
			    {
	  			   $window='yes';
				   $url_window = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$base_url;
				   $window_response = $this->get_web_page($url_window);
				 
	  		    }
			 
	 			 
		  
		 }  
		  
		  
		  
		 if($google=='yes' || $yahoo=='yes' || $bing=='yes' || $window=='yes')
		 {
		       $date = date('Y-m-d h:i:s a', time());
		       $showmap = Mage::getModel('showmap/showmap');
			   $showmap->insertdata($ping_interval,$format,$google,$bing,$window,$yahoo,$yahoo_appid);
			   $this->xml();
            	
			   $showresponse = Mage::getModel('showresponse/showresponse');
			   $showresponse->insertdata($google_response, $bing_response, $window_response, $yahoo_response, $date);
			   Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showmap')->__('sitemap was successfully submitted'));
			   if($ch_yahoo=='yahoo' && $yahoo_appid=='' )
			   {
			    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showmap')->__('Please enter yahoo app id for submitting on yahoo'));
			   }
		 }
		 else
		 if($ch_yahoo=='yahoo')
		 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showmap')->__('Please enter yahoo app id'));
		 else
		 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showmap')->__('Please choose at least 1 channel'));
  	  
	  
	  
	 
	} 
   $this->_redirect('*/*/');
 }
	

 public function configAction()
 {
      
	 	$this->loadLayout();
		$this->_setActiveMenu('showmap/items');
			 
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('showmap/adminhtml_showmap_config'))
				->_addLeft($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_config'));
				
				
		if ($data = $this->getRequest()->getPost()) 
   		{
           
		   //$store = $this->getRequest()->getPost('store');
		   $category = $this->getRequest()->getPost('category');
		   $product = $this->getRequest()->getPost('product');
		   $cms = $this->getRequest()->getPost('cms');
		   $other = $this->getRequest()->getPost('other');
		   $config = Mage::getModel('showmap/config');
		   $config->insertdata($category,$product,$cms,$other);
		   Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showmap')->__('Sitemap was successfully configured'));
		   $this->_redirect('*/*/config');
		   
		}

		$this->renderLayout();
 
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
 
 
 public function xml()
 {   
	
	    $obj = Mage::getModel('showmap/showmap');
		$showmap = $obj->getRow();
		if($showmap['format'] == 'days')
		{ 
		  $ping_interval = $showmap['ping_interval'];
		  $cron = '0 0 */'.$ping_interval.' * *';
		}
		if($showmap['format'] == 'hours')
		{
		  $ping_interval = $showmap['ping_interval'];
		  $cron = '0 */'.$ping_interval.' * * *';
		}
		$url = Mage::getBaseDir()."/app/code/local/Raveinfosys/Showmap/etc/config.xml";
		$endreXML = simplexml_load_file($url);
		$endreXML->crontab[0]->jobs[0]->Raveinfosys_Showmap[0]->schedule[0]->cron_expr = $cron;
		file_put_contents($url, $endreXML->asXML());	
	  
 }
 
}