<?php

namespace packages\actionMitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Home extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->setBackgroundColor('#ffffff');

        $this->layout->header[] = $this->uiKitSearchField(array(
	        'hint' => '{#type_in_your_search#}',
            'onclick_submit' => 'Controller/default/search',
            'onclick_close' => $this->getOnclickSetVariables(array('searchterm' => '')),
        ));

        $this->getTabs(1);

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->uiKitInformationTile($this->getData('performanceDialogs', 'int'), array(
                'icon' => 'pd_visit_home.png',
                'subtitle' => 'Performance Dialogs',
	            'onclick' => $this->getOnclickOpenAction('visits'),
            ), array(
            	'background-color' => '#ffffff'
            )),
            $this->uiKitInformationTile($this->getData('notes', 'int'), array(
                'icon' => 'icon-notes.png',
                'subtitle' => 'Notes',
                'onclick' => $this->getOnclickOpenAction('notelist'),
            ), array(
	            'background-color' => '#ffffff'
            )),
        ), array(), array(
            'text-align' => 'center',
            'margin' => '40 0 0 0'
        ));

	    $this->layout->scroll[] = $this->getComponentRow(array(
		    $this->uiKitInformationTile($this->getData('routines', 'int'), array(
			    'icon' => 'icon-home-routines.png',
			    'subtitle' => 'Routines',
			    'onclick' => $this->getOnclickOpenAction('routinelist'),
		    ), array(
			    'background-color' => '#ffffff'
		    )),
	    ), array(), array(
		    'text-align' => 'left',
		    'margin' => '0 0 0 0'
	    ));

        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentImage('icon-fc-circle.png', array(
                'onclick' => $this->getOnclickOpenAction('fcexperts', $this->model->getActionidByPermaname('fcexperts'))
            ), array(
                'width' => '70',
                'shadow-color' => '#DDE2DE',
                'shadow-radius' => '1',
                'shadow-offset' => '0 3',
            )),
            $this->getComponentImage('icon-learning-circle.png', array(
                'onclick' => $this->getOnclickOpenAction('materialscategorylisting')
            ), array(
                'width' => '70',
                'shadow-color' => '#DDE2DE',
                'shadow-radius' => '1',
                'shadow-offset' => '0 3',
            )),
            $this->getComponentImage('icon-yeammer-button.png', array(
                'onclick' => $this->getOnclickOpenUrl('https://www.yammer.com/dhl.com/groups/2134723')
            ), array(
                'width' => '70',
                'shadow-color' => '#DDE2DE',
                'shadow-radius' => '1',
                'shadow-offset' => '0 3',
            )),
        ), array(), array(
            'text-align' => 'right',
            'margin' => '0 10 10 0'
        ));

        return $this->layout;
    }

    protected function getTabs($tab)
    {
        $this->layout->scroll[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#actions#}'),
                'onclick' => $this->getOnclickOpenAction('home'),
                'active' => $tab == 1
            ),
            array(
                'text' => strtoupper('{#my_to-dos#}'),
                'onclick' => $this->getOnclickOpenAction('mytodos', false, array(
                	'sync_open' => 1
                )),
                'active' => $tab == 2
            )
        ));
    }

}