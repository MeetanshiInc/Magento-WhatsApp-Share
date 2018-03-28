<?php
class Meetanshi_Whatsapp_Model_System_Config_Source_Size
{
    public function toOptionArray(){
        return array(
            array('value' => 0, 'label'=>Mage::helper("whatsapp")->__('Icon')),
            array('value' => 1, 'label'=>Mage::helper("whatsapp")->__('Image')),
        );
    }
}
