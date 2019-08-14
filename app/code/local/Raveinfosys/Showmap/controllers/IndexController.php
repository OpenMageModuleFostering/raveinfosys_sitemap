<?php
class Raveinfosys_Showmap_IndexController extends Mage_Core_Controller_Front_Action
{
   public function indexAction() 
    {

		  $this->loadLayout();
		  
		  $cats = Mage::getModel('catalog/category')->load(2)->getChildren();
          $catIds = explode(',',$cats);
		  Mage::register('catIds', $catIds);
		  
		  $product    = Mage::getModel('catalog/product');
          $products   = $product->getCollection()->addStoreFilter($storeId)->getData();
		  Mage::register('products', $products);
		  
		  $collection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId());
		  $collection->getSelect()
            ->where('is_active = 1');
	      Mage::register('collection', $collection);
		  
		  $this->renderLayout();
		
    }
}