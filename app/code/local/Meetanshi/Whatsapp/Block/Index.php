<?php
class Meetanshi_Whatsapp_Block_Index extends Mage_Core_Block_Template
{
    public function getMessage($product){
        return Mage::helper('whatsapp')->getMessage($product);
    }
}