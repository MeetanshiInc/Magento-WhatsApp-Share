<?php
class Meetanshi_Whatsapp_Model_System_Config_Source_Deal
{
    public function toOptionArray(){
        return array(
            array('value' => 0, 'label'=>Mage::helper("whatsapp")->__('Disable')),
            array('value' => 1, 'label'=>Mage::helper("whatsapp")->__('Special Price')),
            array('value' => 2, 'label'=>Mage::helper("whatsapp")->__('Discount')),
        );
    }
}
