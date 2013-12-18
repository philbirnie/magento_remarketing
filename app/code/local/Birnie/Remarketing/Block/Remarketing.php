<?php
/**
 * Remarketing Block
 *
 * @category   Birnie
 * @package    Birnie_Remarketing
 * @author     Phil Birnie <pbirnie@marketingadept.com>
 */

class Birnie_Remarketing_Block_Remarketing extends Mage_Core_Block_Template
{

    private $_products;
    private $_page_type;

    protected function _construct()
    {
        $this->_products = array('ids'=>array(), 'values'=>array());
    }

    protected function getRemarketingCode()
    {
        $data = '';
        /**
         * Set Page Type, as this directs how we pull the rest of our
         * product data.
         */
        $this->_setPageType();
        switch($this->_page_type)
        {
            case "product":
                $this->_setProductPageDetails();
                break;
            case "cart":
                $this->_setCartPageDetails();
                break;
            case "purchase":
                $this->_setSuccessPageDetails();
                break;
            default:
                $this->_setSiteViewDetails();
                break;
        }

        $data = $this->_compileRemarketingCode();
        $data .= $this->_getBaseRemarketingBlock();

        return $data;
    }

    private function _compileRemarketingCode()
    {
        $data = '<script type="text/javascript">'."\n";
        $data .= 'var google_tag_params = {'."\n";
        $data .= "ecomm_prodid: {$this->_getIdsString()},\n";
        $data .= "ecomm_pagetype: '{$this->_page_type}',\n";
        $data .= "ecomm_totalvalue: {$this->_getValuesString()}\n";
        $data .= '};
        </script>'."\n";
        return $data;
    }

    private function _getBaseRemarketingBlock()
    {
        $_base_code = '';
        $conversion_id = Mage::getStoreConfig('google/remarketing/conversion_id');
        if($conversion_id)
        {
            $_base_code = '
                <script type="text/javascript">
                /* <![CDATA[ */
                var google_conversion_id = '.$conversion_id.';
                var google_custom_params = window.google_tag_params;
                var google_remarketing_only = true;
                /* ]]> */
                </script>
                <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
                </script>
                <noscript>
                <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/'.$conversion_id.'/?value=0&amp;guid=ON&amp;script=0"/>
                </div>
                </noscript>
                ';

        }
        return $_base_code;
    }

    /**
     * Sets Page type variable depending upon handle
     */
    private function _setPageType()
    {
        $_handles = $this->getLayout()->getUpdate()->getHandles();
        if(is_array($_handles) && isset($_handles[3]))
        {
            $_handle = $_handles[3];
            switch($_handle)
            {
                case "catalog_product_view":
                    $this->_page_type = "product";
                    break;
                case "checkout_cart_index":
                    $this->_page_type = "cart";
                    break;
                case "checkout_onepage_success":
                case "checkout_multishipping_success":
                    $this->_page_type = "purchase";
                    break;
                default:
                    $this->_page_type = "siteview";
            }
        }
    }

    private function _setProductPageDetails()
    {
        $_product = Mage::registry('current_product');
        if($_product)
        {
            $this->_products["ids"][] = $_product->getSku();

            $this->_products["values"][] = $_product->getSpecialPrice() ?
                        sprintf("%01.2f",$_product->getSpecialPrice()) : sprintf("%01.2f",$_product->getPrice());
        }
    }

    private function _setSiteViewDetails()
    {
        $this->_products["ids"] = array();
        $this->_products["values"] = array();
    }


    private function _setCartPageDetails()
    {
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        $_cartItems = $_quote->getAllVisibleItems();
        foreach($_cartItems as $_product)
        {
            $this->_products["ids"][] = $_product->getSku();

            $this->_products["values"][] = $_product->getSpecialPrice() ?
                        sprintf("%01.2f",$_product->getSpecialPrice()) : sprintf("%01.2f",$_product->getPrice());
        }
    }

    private function _setSuccessPageDetails()
    {
        /**
         * Get Orders
         * @var [type]
         */
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));

        foreach($collection as $order)
        {
            $_orderItems = $order->getAllVisibleItems();
            foreach($_orderItems as $_product)
            {
                $this->_products["ids"][] = $_product->getSku();

                $this->_products["values"][] = $_product->getSpecialPrice() ?
                            sprintf("%01.2f",$_product->getSpecialPrice()) : sprintf("%01.2f",$_product->getPrice());
            }
        }
    }


    /**
     * Compile String of Ids for Remarketing Script
     * @return string Formatted String for Product Ids
     */
    private function _getValuesString()
    {
        $ids_string = "''";
        if(count($this->_products["values"]) == 1)
        {
            $ids_string = "{$this->_products["values"][0]}";
        }
        elseif(count($this->_products["values"]) > 1)
        {
            $ids_string = "[";
            $ids_string .= implode(",", $this->_products["values"]);
            $ids_string .= "]";
        }
        return $ids_string;
    }


    /**
     * Compile String of Ids for Remarketing Script
     * @return string Formatted String for Product Ids
     */
    private function _getIdsString()
    {
        $ids_string = "''";
        if(count($this->_products["ids"]) == 1)
        {
            $ids_string = "'{$this->_products["ids"][0]}'";
        }
        elseif(count($this->_products["ids"]) > 1)
        {
            $ids_string = "['";
            $ids_string .= implode("','", $this->_products["ids"]);
            $ids_string .= "']";
        }
        return $ids_string;
    }

}