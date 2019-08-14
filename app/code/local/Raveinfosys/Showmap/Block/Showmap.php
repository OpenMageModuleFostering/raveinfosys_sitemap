<?php
class Raveinfosys_Showmap_Block_Showmap extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShowmap()     
     { 
        if (!$this->hasData('showmap')) {
            $this->setData('showmap', Mage::registry('showmap'));
        }
        return $this->getData('showmap');
        
    }
}