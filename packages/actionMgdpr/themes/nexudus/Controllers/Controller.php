<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMgdpr\themes\nexudus\Controllers;

use packages\actionMgdpr\themes\nexudus\Views\View as ArticleView;
use packages\actionMgdpr\themes\nexudus\Models\Model as ArticleModel;

class Controller extends \packages\actionMgdpr\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

}
