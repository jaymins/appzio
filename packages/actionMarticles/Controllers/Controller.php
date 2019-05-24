<?php

/**
 * This example shows a simple registration form. Usually this action would be used in conjuction with
 * Mobilelogin action, which provides login and logout functionalities.
 *
 * Default controller for your action. If no other route is defined, the action will default to this controller
 * and its default method actionDefault() which must always be defined.
 *
 * In more complex actions, you would include different controller for different modes or phases. Organizing
 * the code for different controllers will help you keep the code more organized and easier to understand and
 * reuse.
 *
 * Unless controller has $this->no_output set to true, it should always return the view file name. Data from
 * the controller to view is passed as a second part of the return array.
 *
 * Theme's controller extends this file, so usually you would define the functions as public so that they can
 * be overriden by the theme controller.
 *
 */

namespace packages\actionMarticles\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMarticles\Views\View as ArticleView;
use packages\actionMarticles\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){
        $data = [];

        return ['View', $data];
    }

	public function getArticleMap() {
		return array(
			'text' => array(
				'component' => 'uiKitArticleText',
				'attributes' => array(
					'content',
                    'styles',
                    'params',
				)
			),
            'richtext' => array(
                'component' => 'uiKitArticleRichText',
                'attributes' => array(
                    'content',
                    'styles',
                    'params',
                )
            ),
            'wraprow' => array(
                'component' => 'uiKitArticleWrapRow',
                'attributes' => array(
                    'content',
                    'styles',
                    'params',
                )
            ),
			'QAsection' => array(
				'component' => 'uiKitArticleQABlock',
				'attributes' => array(
					'question',
					'answer'
				),
			),
			'image' => array(
				'component' => 'uiKitArticleImage',
				'attributes' => array(
					'image_id'
				),
			),
			'blockquote' => array(
				'component' => 'uiKitArticleBlockquote',
				'attributes' => array(
					'content'
				),
			),
			'notes_box' => array(
				'component' => 'uiKitArticleNote',
				'attributes' => array(
					'content',
					'background-color',
					'color',
				),
			),
			'gallery' => array(
				'component' => 'uiKitArticleGallery',
				'attributes' => array(
					'ref',
				),
			),
			'video' => array(
				'component' => 'uiKitArticleVideo',
				'attributes' => array(
					'video_link',
					'autostart'
				),
			),
		);
	}

}