<?php

namespace packages\actionMitems\themes\cityapp\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

class Listing extends BootstrapController
{
	/* @var ArticleModel */
	public $model;

	/**
	 * Default action entry point.
	 *
	 * @return array
	 */
	public function actionDefault()
	{
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        // $this->collectLocation(43200);

        $data['lat'] = 42.671357;
        $data['lon'] = 23.3181261;

		return ['Listing', $data];
	}

}