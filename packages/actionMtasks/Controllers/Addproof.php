<?php

namespace packages\actionMtasks\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMtasks\Views\View as ArticleView;
use packages\actionMtasks\Models\Model as ArticleModel;

class Addproof extends Tasklist {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    /* note: saving of a new proof is inside tasklist.php */

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){
        return ['Addproof',$this->setMyProofData()];
    }


}
