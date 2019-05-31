<?php

namespace packages\actionDitems\themes\fans\Components;

trait uiKitIntroWithButtons {

    public function uiKitIntroWithButtons(array $items, array $params=array()){

        foreach ($items as $item){
            $swipe[] = $this->uiKitIntroWithButtonsGetItem($item);
        }

        if(isset($swipe)){
            return $this->getComponentColumn(array(
	            $this->getComponentSwipe($swipe, array(
	            	'hide_scrollbar' => 1
	            ),array())
            ));
        }

        return $this->getComponentText('no items');
    }

    public function uiKitIntroWithButtonsGetItem($item){

    	$view_height = $this->screen_height - 80;

    	if ( $item['view_type'] == 'fullview' ) {
		    if(isset($item['icon'])){
			    $col[] = $this->getComponentColumn(array(
				    $this->getComponentImage($item['icon'], array(), array(
				        'height' => 'auto'
                    ))
			    ), array(), array(
			        'text-align' => 'center',
                    'margin' => '10 0 10 0',
                    'height' => '23%',
                ));
		    }

		    if(isset($item['title'])){
			    $col[] = $this->getComponentText($item['title'], array('style' => 'uikit_intro_title'));
		    }

		    if(isset($item['description'])){
			    $col[] = $this->getComponentColumn(array(
				    $this->getComponentText($item['description'], array(
				    	'style' => 'uikit_intro_description'
				    )),
			    ), array(), array(
				    'margin' => '0 0 50 0',
				    'floating' => '1',
			    	'vertical-align' => 'bottom',
			    ));
		    }
	    }

	    if (
	    	$item['view_type'] == 'splitview' AND
	        ( isset($item['rows']) AND $item['rows'] AND is_array($item['rows']) )
	    ) {
			$rows = $item['rows'];
			$item_height = $view_height / count($rows);

			if ( isset($item['buttons']) )
				$item_height = $item_height - 40;

		    foreach ( $rows as $row ) {
			    $col[] = $this->getComponentColumn(array(
			    	$this->getComponentSpacer( 20 ),
				    $this->getComponentRow(array(
                        $this->getComponentImage($row['icon'], array('style' => 'uikit_intro_icon'))
                    ), array(), array('text-align' => 'center')),
				    $this->getComponentColumn(array(
					    $this->getComponentText($row['description'], array(
						    'style' => 'uikit_intro_description'
					    )),
				    ), array(), array(
					    'margin' => '45 0 0 0',
				    )),
			    ), array(), array(
				    'height' => $item_height,
			    ));
			}

	    }

	    if(isset($item['buttons']) AND $item['buttons'] ){
        	if ( $buttons_output =  $this->uiKitIntroButtons( $item['buttons'] ) ) {
		        $col[] = $buttons_output;
	        }
        }

        if(isset($col)){
            return $this->getComponentColumn($col, array(
                'scrollable' => 1
            ), array(
            	'height' => $view_height,
	            'margin' => '0 0 0 0',
            ));
        } else {
            return $this->getComponentText('no info');
        }

    }

    public function uiKitIntroButtons( $buttons ) {
    	
    	$buttons_array = [];

	    foreach ( $buttons as $button ) {

	    	if ( !isset($button['title']) ) {
	    		continue;
		    }

		    $buttons_array[] = $this->getComponentText($button['title'], array(
		    	'style' => 'add_item_button',
			    'onclick' => $button['onclick'],
				'noanimate' => true
		    ));
    	}

    	if ( empty($buttons_array) ) {
	    	return false;
	    }

	    return $this->getComponentColumn($buttons_array, array(),array(
		    'floating' => '1',
		    'vertical-align' => 'bottom',
		    'width' => $this->screen_width,
		    'text-align' => 'center',
		    'margin' => '0 0 30 0'
	    ));
    }

}