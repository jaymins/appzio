<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;
use Bootstrap\Models\BootstrapModel;

Trait Pr
{

    /* return users personal records. Note that values are always saved in metric
    units, and converted to imperial only for the view */

    public function getPr(){
        $pr = PrModel::model()->with('pr_user')->findAllByAttributes(['app_id' => $this->appid],['order' => 'title']);

        $units = $this->getSavedVariable('units');

        /* unit conversions */
        foreach($pr as $key=>$row){
            if($units == 'imperial') {
                if ($row->unit == 'kg') {
                    $row->unit = 'lbs';
                    if (isset($row->pr_user->value)) {
                        $row->pr_user->value = @round($row->pr_user->value * "2,2046226218", 0);
                    }
                }
            } else {
                if (isset($row->pr_user->value)) {
                    $row->pr_user->value = round($row->pr_user->value, 0);
                }
            }
        }

        if($pr){
            return $pr;
        }

        return array();
    }

    public function savePr($pr,$value){

        if($this->getSavedVariable('units') == 'imperial'){
            $value = $value/"2,2046226218";
        }

        $obj = new PrUserModel();
        $obj->play_id = $this->playid;
        $obj->value = $value;
        $obj->pr_id = $pr;
        $obj->date = time();
        @$obj->insert();
    }


}