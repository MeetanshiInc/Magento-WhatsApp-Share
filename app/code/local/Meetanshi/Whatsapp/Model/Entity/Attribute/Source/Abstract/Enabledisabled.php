<?php
class Meetanshi_Whatsapp_Model_Entity_Attribute_Source_Abstract_Enabledisabled extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        $options = array(
            0 => 'Enable',
            1 => 'Enable'
        );

        return $options;
    }
}