<?php
class Meetanshi_Whatsapp_Model_System_Config_Source_Product
{
    public function toOptionArray(){
        return array(
            array('value' => 0, 'label'=>Mage::helper("whatsapp")->__('Globally')),
            array('value' => 1, 'label'=>Mage::helper("whatsapp")->__('Category Specific')),
            array('value' => 2, 'label'=>Mage::helper("whatsapp")->__('Product Specific')),
        );
    }
}
