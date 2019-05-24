<?php

namespace packages\actionMitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Search extends BootstrapView
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

        $this->layout->scroll[] = $this->getComponentRow(array(
        	$this->getComponentText('{#results#}', array(), array(
        		'padding' => '5 15 5 15',
        		'font-size' => '16',
        		'color' => '#333333',
	        ))
        ), array(), array(
        	'background-color' => '#d8d8d8'
        ));

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $items = $this->getData('items', 'array');

        if ( empty($items) ) {
        	$this->layout->scroll[] = $this->getComponentText('{#no_results#}', array(), array(
		        'color' => '#333333',
		        'text-align' => 'center',
		        'font-size' => '16',
		        'margin' => '10 0 10 0',
	        ));
        	
        	return $this->layout;
        }

        $icons = [
			'note' => 'uikit-icon-search-notes.png',
			'visit' => 'uikit-icon-search-visits-v2.png',
			'routine' => 'uikit-icon-routines-v2.png',
        ];
	    
	    foreach ( $items as $item ) {

            $onclick = new \stdClass();
            $onclick->action = 'open-action';
            $onclick->sync_open = 1;
            $onclick->sync_close = 1;
            $onclick->back_button = 1;

	    	$type = $item->type;

	    	if ( $type == 'routine' ) {
	    	    $path = 'routineedit';
            } else {
	    	    $path = 'view' . $type;
            }

		    $onclick->action_config = $this->model->getActionidByPermaname($path);
		    $onclick->id = $item->id;

        	$icon = ( isset($icons[$type]) ? $icons[$type] : 'icon-interval-gray.png' );
        	$date = date( 'd F', $item->date_added );
		    $this->layout->scroll[] = $this->components->uiKitSearchItem($icon, $item->name, $date, array(
			    'divider' => true,
			    'onclick' => $onclick,
		    ));
        }

        return $this->layout;
    }

}