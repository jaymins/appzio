<?php


namespace packages\actionMsubscription\Models;

use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel
{

    use Purchase;

    public function handlePurchase()
    {

        if (!isset($_REQUEST['purchase_product_id'])) {
            return false;
        }

        if($this->getConfigParam('data_mode') == 'db' OR $this->data_mode == 'db'){
            $this->savePurchase($_REQUEST['purchase_product_id']);
        }

        $id = $_REQUEST['purchase_product_id'];

        $onetime_ios = $this->getCode('ios','once');
        $onetime_android = $this->getCode('android','once');

        $monthly_ios = $this->getCode('ios','monthly');
        $monthly_android = $this->getCode('android','monthly');

        $yearly_ios = $this->getCode('ios','yearly');
        $yearly_android = $this->getCode('android','yearly');

        if ($id == $onetime_ios OR $id == $onetime_android) {
            $this->saveVariable('purchase_once', 1);
        }

        if ($id == $monthly_ios OR $id == $monthly_android) {
            $this->saveVariable('purchase_monthly', 1);
        }

        if ($id == $yearly_ios OR $id == $yearly_android) {
            $this->saveVariable('purchase_yearly', 1);
        }

        return true;
    }



}