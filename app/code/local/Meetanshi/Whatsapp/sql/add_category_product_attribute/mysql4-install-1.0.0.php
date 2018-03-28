<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute('catalog_category', 'whatsapp_share',  array(
    'group'      => 'General Information',
    'type'       => 'int',
    'label'      => 'Whatsapp Share',
    'input'      => 'select',
    'source'     => 'whatsapp/entity_attribute_source_abstract_enabledisabled',
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'        => true,
    'required'       => true,
    'user_defined'   => 1,
    'default'        => 1
));
$installer->endSetup();

$installer->startSetup();
$installer->addAttribute('catalog_product', 'whatsapp_share',  array(
    'group'      => 'General',
    'type'       => 'int',
    'label'      => 'Whatsapp Share',
    'input'      => 'select',
    'source'     => 'whatsapp/entity_attribute_source_abstract_enabledisabled',
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'        => true,
    'required'       => true,
    'user_defined'   => 1,
    'default'        => 1
));
$installer->endSetup();