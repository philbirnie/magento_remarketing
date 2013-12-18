<?php


class Birnie_Remarketing_Model_Observer extends Varien_Object
{
    public function setRemarketingOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('remarketing');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}