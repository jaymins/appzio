<?php
/**
 * Created by PhpStorm.
 * User: trailo
 * Date: 2.10.18
 * Time: 9:30
 */

namespace packages\actionMfood\Components;


trait DivSelectYourCookDays
{

    public function getDivSelectYourCookDays(){
        return $this->getComponentText('Always rememebr to return values!');
    }

}