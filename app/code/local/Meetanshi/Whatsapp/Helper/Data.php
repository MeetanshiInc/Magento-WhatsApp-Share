<?php
class Meetanshi_Whatsapp_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductWise(){
        return Mage::getStoreConfig('whatsappshare/whatsappconfig/product_wise');
    }
    public function getCategoryWise()
    {
        return Mage::getStoreConfig('whatsappshare/whatsappconfig/category_wise');
    }
    public function getSize(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/image_size');
    }
    public function getCustomMessage(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/custom_mesaage');
    }
    public function getProductNameEnable(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/productname_enable');
    }
    public function getProductDescriptionEnable(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/productdescription_enable');
    }
    public function getProductPriceEnable(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/productprice_enable');
    }
    public function getDealOn(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/deal_on');
    }
    public function getDiscountMessage(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/discount_message');
    }
    public function getSpecialPriceMessage(){
        return Mage::getStoreConfig('whatsappshare/sharingoption/specialprice_message');
    }
    public function getUtmTrackingEnable(){
        return Mage::getStoreConfig('whatsappshare/utmtracking/utmtracking_enable');
    }
    public function getUtmSource(){
        return Mage::getStoreConfig('whatsappshare/utmtracking/utm_source');
    }
    public function getUtmMedium(){
        return Mage::getStoreConfig('whatsappshare/utmtracking/utm_medium');
    }
    public function getUtmCampaign(){
        return Mage::getStoreConfig('whatsappshare/utmtracking/utm_campaign');
    }
    public function getGoogleShortUrlEnable(){
        return Mage::getStoreConfig('whatsappshare/googleshorturl/googleshorturl_enable');
    }
    public function getApiKey(){
        return trim(Mage::getStoreConfig('whatsappshare/googleshorturl/apikey'));
    }
    protected $apiURL = 'https://www.googleapis.com/urlshortener/v1/url';
    public function __construct(){
        $this->GoogleURLAPI();
    }
    protected function GoogleURLAPI() {
        $key = $this->getApiKey();
        $apiURL = $this->apiURL;
        $this->apiURL = $apiURL.'?key='.$key;
    }
    public function shorten($url) {
        $response = $this->send($url);//print_r($response);
        return isset($response['id']) ? $response['id'] : false;
    }
    public function send($url,$shorten = true) {
        $ch = curl_init();
        if($shorten) {
            curl_setopt($ch,CURLOPT_URL,$this->apiURL);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$url)));
            curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        }
        else {
            curl_setopt($ch,CURLOPT_URL,$this->apiURL.'&shortUrl='.$url);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        try{
            $result = curl_exec($ch);
            curl_close($ch);
        }catch(Exception $e){
            Mage::log($e->getMessage(),null,'whatsapp.log',true);
        }
        return json_decode($result,true);
    }
    public function getMessage($product){
        try{
            $productName = trim($product->getName());
            $productUrl = trim($product->getProductUrl());
            $productDescription = trim($product->getShortDescription());
            $currencyCode =   Mage::app()->getStore()->getCurrentCurrencyCode(). ' ';
            if($product->isGrouped()):
                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                $prices = [];
                $specialPrices = [];
                $productCount = 0;
                foreach($associatedProducts as $item) {
                    $id = $item->getId();
                    $associatedItem = Mage::getModel('catalog/product')->load($id);
                    $productCount++;
                    if ($associatedItem->getPrice() && $associatedItem->getSpecialPrice()) {
                        $prices[] = $associatedItem->getPrice();
                        $specialPrices[] = $associatedItem->getSpecialPrice();
                    } else {
                        $prices[] = $associatedItem->getPrice();
                    }
                }
                sort($prices);
                if(isset($specialPrices) && $productCount == sizeof($specialPrices)):
                    sort($specialPrices);
                    $productPrice = $prices[0];
                    $productSpecialPrice = $specialPrices[0];
                else:
                    $productSpecialPrice = '';
                    $productPrice = $prices[0];
                endif;
            else:
                $productPrice = trim($product->getPrice());
                $productSpecialPrice = trim($product->getSpecialPrice());
            endif;
            if($this->getUtmTrackingEnable()):
                $productUrl = $productUrl."?utm_source=".$this->getUtmSource();
                $utmMedium = $this->getUtmMedium();
                $utmCampaign = $this->getUtmCampaign();
                if($utmMedium != ''):
                    $productUrl = $productUrl."&utm_medium=".$utmMedium;
                endif;
                if($utmCampaign != ''):
                    $productUrl = $productUrl."&utm_campaign=".$utmCampaign;
                endif;
            endif;
            if($this->getGoogleShortUrlEnable()):
                $productUrl = $this->shorten($productUrl);
            endif;
            $messageData = $productUrl."\r\n\r\n";
            if( ($customMessage = $this->getCustomMessage()) != ''):
                $messageData = $messageData.trim($customMessage)."\r\n\r\n";
            endif;
            if($this->getProductNameEnable()):
                $messageData = $messageData.$productName."\r\n\r\n";
            endif;
            if($this->getProductDescriptionEnable()):
                $productDescription = substr_replace($productDescription,'',256);
                $messageData = $messageData.$productDescription."\r\n\r\n";
            endif;
            if( $this->getProductPriceEnable()):
                $productPrice = round($productPrice,2);
                if($product->isGrouped()):
                    $messageData = $messageData.'Starting at : '.$currencyCode.$productPrice."\r\n\r\n";
                else:
                    $messageData = $messageData.$currencyCode.$productPrice."\r\n\r\n";
                endif;
            endif;
            if($productSpecialPrice != '' && ($this->getDealOn())):
                $specialPriceMessage = $this->getSpecialPriceMessage();
                $message = str_split($specialPriceMessage);
                $startPosition = 0;
                $length = 0;
                foreach($message as $key => $letter){
                    if($letter == '{'){
                        $startPosition = $key;
                        break;
                    }
                }
                foreach($message as $key => $letter){
                    if($letter == '}'){
                        $length = ($key + 3) - $startPosition;
                        break;
                    }
                }
                if(($startPosition) && ($length)):
                    $productSpecialPrice = round($productSpecialPrice,2);
                    $specialPriceMessage = substr_replace($specialPriceMessage,$currencyCode.$productSpecialPrice,$startPosition,$length);
                endif;
                $messageData = $messageData.$specialPriceMessage."\r\n\r\n";
            elseif($this->getDealOn() == '2'):
                $messageData = $messageData.$this->getDiscountMessage()."\r\n\r\n";
            endif;
            return $messageData  = str_replace('+',' ',urlencode($messageData));
        }catch (Exception $e) {
            Mage::log($e->getMessage(),null,'whatsapp.log',true);
        }
    }
}