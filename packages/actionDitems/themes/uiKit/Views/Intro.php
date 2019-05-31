<?php

namespace packages\actionDitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Intro extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->model->rewriteActionConfigField('background_color', '#FFCC00');

        $content = array(
            array(
                'icon' => 'uikit-intro-icon-welcome-v2.png',
                'title' => 'WELCOME TO !MPROVE',
                'description' => 'THE APP TO SUPPORT YOUR CONTINUOUS IMPROVEMENT JOURNEY',
	            'view_type' => 'fullview',
            ),
            array(
            	'rows' => array(
		            array(
			            'icon' => 'uikit-intro-icon-welcome.png',
			            'description' => 'CONDUCT GUIDED PERFORMANCE DIALOG VISITS',
		            ),
		            array(
			            'icon' => 'uikit-intro-icon-list.png',
			            'description' => 'CAPTURE OBSERVATIONS, SNAPSHOTS AND NOTES',
		            ),
	            ),
                'view_type' => 'splitview',
            ),
	        array(
		        'rows' => array(
			        array(
				        'icon' => 'uikit-intro-icon-letter.png',
				        'description' => 'SHARE YOUR FINDINGS, ASSIGN ACTIONS AND SET PERSONAL FOLLOW-UP REMINDERS',
			        ),
			        array(
				        'icon' => 'uikit-intro-icon-tag.png',
				        'description' => 'USE TAGS TO EASILY ORGANIZE AND SEARCH YOUR OBSERVATIONS',
			        ),
		        ),
		        'view_type' => 'splitview',
	        ),
	        array(
		        'rows' => array(
			        array(
				        'icon' => 'uikit-intro-icon-24.png',
				        'description' => 'SET UP YOUR PERSONAL CONTINUOUS IMPROVEMENT SCHEDULE',
			        ),
			        array(
				        'icon' => 'uikit-intro-icon-books.png',
				        'description' => 'GET SUPPORT – FIND FIRST CHOICE EXPERTS OR BROWSE OUR KNOWLEDGE BASE',
			        ),
		        ),
		        'buttons' => array(
			        array(
				        'title' => '{#get_started#}',
				        'onclick' => $this->getOnclickOpenAction('home', $this->model->getActionidByPermaname('home'))
			        )
		        ),
		        'view_type' => 'splitview',
	        ),
        );

        $this->layout->scroll[] = $this->components->uiKitIntroWithButtons($content);

        $this->layout->overlay[] = $this->components->introScreenOverlay();

        return $this->layout;
    }

}