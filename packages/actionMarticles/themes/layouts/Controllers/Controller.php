<?php

namespace packages\actionMarticles\themes\layouts\Controllers;

use packages\actionMarticles\themes\uikit\Views\Categorylisting as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMarticles\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        // $this->model->flushActionRoutes();
    }

	/**
	 * This is the default action inside the controller. This function gets called, if
	 * nothing else is defined for the route
	 * @return array
	 */
	public function actionDefault(){
		$data = [];
		return ['Mainview', $data];
	}
	
	public function actionSimpleGrid() {
        $data = [];
        return ['Simplegrid', $data];
    }

    public function actionExtendedGrid() {
        $data = [];
        return ['Extendedgrid', $data];
    }

    public function actionCheckboxes() {
        $data = [];
        return ['Checkboxes', $data];
    }

    public function actionOverlay() {
        $data = [];
        return ['Overlay', $data];
    }

    public function actionSwipers() {
        $data = [];
        return ['Swipers', $data];
    }

}