<?php
class Raveinfosys_Showresponse_Block_Showresponse extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShowresponse()     
     { 
        if (!$this->hasData('showresponse')) {
            $this->setData('showresponse', Mage::registry('showresponse'));
        }
        return $this->getData('showresponse');
        
    }
}