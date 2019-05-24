<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class SingleMobileproperties extends MobilepropertiesController
{
    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;
    public $settings;

    public function tab1()
    {
        $this->initModel();
        $this->data = new stdClass();

        $this->rewriteActionConfigField('background_color', '#f6f6f6');
        $property_id = false;

        $this->settings = MobilepropertiesSettingModel::findOrFail($this->playid, $this->gid);

        if (preg_match('~property-id-~', $this->menuid)) {
            $property_id = str_replace('property-id-', '', $this->menuid);
            $this->sessionSet('property_id_temp', $property_id);
        } else {
            $property_id = $this->sessionGet('property_id_temp');
        }

        if (strstr($this->menuid, 'rental-like-')) {
            $id = str_replace('rental-like-', '', $this->menuid);
            $property_id = $id;
            MobilepropertiesBookmarkModel::like($id, $this->playid, $this->gid);
        }

        if (strstr($this->menuid, 'rental-dislike-')) {
            $id = str_replace('rental-dislike-', '', $this->menuid);
            $property_id = $id;
            MobilepropertiesBookmarkModel::skip($id, $this->playid, $this->gid);
        }

        if (strstr($this->menuid, 'save-edit-')) {

            $this->propertyModel->saveFullPropertyAddress();

            $this->prepopulateSavedValues();

            $id = str_replace('save-edit-', '', $this->menuid);
            $images = $this->getPropertyImages();
            $this->validatePropertyInput($images);

            if (!empty($this->errors)) {
                $this->showProperty($id);
                return $this->data;
            }

	        $current_property = $this->propertyModel->findByPk( $id );
	        $this->propertyModel->submitvariables = $this->submitvariables;
	        $this->propertyModel->saveSubmit('update', $id, $images);

            /*
            $trigger_upsell = false;

	        // Unset the full_management option prior saving
	        if ( !$current_property->is_premium AND $this->getSubmittedVariableByName('temp_do_advertising') ) {
		        $trigger_upsell = true;
	        	$this->submitvariables['temp_do_advertising'] = 0;
		        $this->propertyModel->submitvariables = $this->submitvariables;
	        }

	        $this->propertyModel->saveSubmit('update', $id, $images);

	        // Check whether this is premium property and if the user tries to add the "full management" option
	        // and Redirect to Upsell screen
	        if ( $trigger_upsell ) {
		        $onclick = new stdClass();
		        $onclick->id = 'edit|' . $id;
		        $onclick->action = 'open-action';
		        $onclick->action_config = $this->getActionidByPermaname('property-upsells');
		        $onclick->sync_open = 1;
		        $this->data->onload[] = $onclick;
		        return $this->data;
	        }
            */

        }

        if (strstr($this->menuid, 'toggle-available-')) {
            $id = str_replace('toggle-available-', '', $this->menuid);
            $this->propertyModel->toggleAvailable($id);
            $this->showProperty($id);
            return $this->data;
        }

        if (strstr($this->menuid, 'delete-')) {
            $id = str_replace('delete-', '', $this->menuid);
            $this->delete($id);
            $this->no_output = true;
            return $this->data;
        }

        if (strstr($this->menuid, 'remove-bookmark-')) {
            $id = str_replace('remove-bookmark-', '', $this->menuid);
            MobilepropertiesBookmarkModel::deleteBookmark($id, $this->playid, $this->gid);
            $this->showPropertyDetails($id, 'remove');
            return $this->data;
        }

        if (strstr($this->menuid, 'open-')) {
            $this->clearPropertyImages();
            $this->showProperty();
            return $this->data;
        }

        if (stristr($this->menuid, '-imgdel-')) {
            $params = explode('-imgdel-', $this->menuid);

            if (empty($params[0])) {
                $this->getPropertyForm($this->propertyModel);
                return $this->data;
            }

            $this->propertyModel->deleteImage($params[0], $params[1]);
            $this->clearPropertyImages();
            $this->showProperty($params[0]);
            return $this->data;
        }

        if (strstr($this->menuid, 'del-images-')) {
            $id = str_replace('del-images-', '', $this->menuid);
            if (!empty($id)) {
                $this->showProperty($id);
            } else {
                $this->getPropertyForm($this->propertyModel);
            }

            return $this->data;
        }

        $status = MobilepropertiesBookmarkModel::getStatus($property_id, $this->playid, $this->gid);
        $this->showPropertyDetails($property_id, $status);
        return $this->data;
    }

    /**
     * Show single property
     *
     * @param $id
     */
    public function showProperty($id = null)
    {
        if (is_null($id)) {
            $id = str_replace('open-', '', $this->menuid);
        }

        if (!is_numeric($id)) {
            $this->data->scroll[] = $this->getText('Something went wrong ..', array('style' => 'rentals-info-message-text'));
            return true;
        }

        $this->rewriteActionField('subject', 'Edit Property');

        $property = MobilepropertiesModel::model()->findByPk($id);

        if (!is_object($property)) {
            $this->data->scroll[] = $this->getText('Something went wrong ..', array('style' => 'rentals-info-message-text'));
            return true;
        }

        $this->currentId = $id;
        $this->setPropertyImages($property->images);
        $this->setGridWidths();

        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if (strstr($this->menuid, 'del-images-') || $this->deleting) {
            $user[] = $this->getText('{#click_on_images_to_delete#}', array('vertical-align' => 'top', 'font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user, array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        }

        $this->data->scroll[] = $this->getSpacer(15);
        $this->data->scroll[] = $this->getImageGrid();
        $this->data->scroll[] = $this->getSpacer(15);

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if ($this->menuid == 'del-images-' || $this->deleting) {
            $onclick->id = 'open-' . $id;
        } else {
            $onclick->id = 'del-images-' . $id;
        }

        if ($this->getImageCount() > 1) {
            // This will be the delete images button with the proper image
            $this->data->scroll[] = $this->getImage('del-photos.png', array('margin' => '10 0 10 0', 'height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }

        $this->getPropertyForm($property, 'edit');

        $button = '{#unavailable#}';
        $toggle_button_class = 'toggle-button';
        if (!$property->available) {
            $button = '{#available#}';
            $toggle_button_class = 'toggle-button-green';
        }

        // Print validation errors
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $this->data->scroll[] = $this->getText($error, array(
                	'padding' => '3 0 3 0',
                	'text-align' => 'center',
	                'color' => '#FF0000',
	                'font-size' => '13',
                ));
            }
        }

//        $buttonRow[] = $this->getTextbutton(strtoupper('{#save#}'), array('style' => 'update-button', 'id' => 'insert', 'onclick' => $this->getOnclick('id', true, 'save-edit-' . $property->id)));
//        $this->data->scroll[] = $this->getRow($buttonRow, array(
//            'border-radius' => 3,
//            'shadow-color' => '#33000000',
//            'shadow-radius' => 1,
//            'shadow-offset' => '0 1',
//            'margin' => '10 10 0 10'
//        ));

        $column = [];

        $leftButton[] = $this->getTextbutton(strtoupper($button), array('style' => $toggle_button_class, 'id' => 'insert', 'onclick' => $this->getOnclick('id', true, 'toggle-available-' . $property->id)));
        $column[] = $this->getRow($leftButton, array(
            'border-radius' => 3,
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'margin' => '10 5 10 10',
            'width' => $this->screen_width / 2.17,
        ));

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('properties');
        $onclick->sync_open = 1;

        $delete[] = $this->getOnclick('id', true, 'delete-' . $property->id);
        $delete[] = $onclick;

//        $rightButton[] = $this->getTextbutton(strtoupper('{#delete#}'), array('style' => 'delete-button', 'id' => 'delete', 'onclick' => $delete));
//        $column[] = $this->getRow($rightButton, array(
//            'border-radius' => 3,
//            'shadow-color' => '#33000000',
//            'shadow-radius' => 1,
//            'shadow-offset' => '0 1',
//            'margin' => '10 10 10 5',
//            'width' => $this->screen_width / 2.17,
//        ));

        $rightButton[] = $this->getTextbutton(strtoupper('{#save#}'), array(
            'style' => 'update-button',
            'id' => 'insert',
            'onclick' => $this->getOnclick('id', true, 'save-edit-' . $property->id)
        ));

        $column[] = $this->getRow($rightButton, array(
            'border-radius' => 3,
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'margin' => '10 10 10 5',
            'width' => $this->screen_width / 2.17,
        ));

        $this->data->scroll[] = $this->getRow($column);

    }

    /**
     * Show single property details
     *
     * @param $id
     * @param $status
     * @return bool
     */
    public function showPropertyDetails($id, $status)
    {
        if (!is_numeric($id)) {
            $this->data->scroll[] = $this->getText('Something went wrong ..', array('style' => 'rentals-info-message-text'));
            return true;
        }

        $this->rewriteActionConfigField('background_color', '#f6f6f6');

        $property = MobilepropertiesModel::model()->findByPk($id);

        if (empty($property)) {
        	$this->data->scroll[] = $this->getText('{#something_went_wrong#}', array( 'style' => 'general_example_text' ));
            return false;
        }

        $this->getPropertyHeader( $property->name );

        $available = true;

        if ($property->available == 0) {
            $col[] = $this->getImage('icon-warning.png', array(
                'width' => '20',
                'margin' => '0 5 0 0'
            ));
            $col[] = $this->getText('{#this_property_is_not_available_at_this_moment#}', array(
                'text-align' => 'center',
                'color' => "#ffffff",
                'font-size' => '14',
                'margin' => '0 0 0 0'
            ));

            $this->data->header[] = $this->getRow($col, array(
                'text-align' => 'center',
                'vertical-align' => 'middle',
                'padding' => '20 15 20 15',
                'background-color' => '#FA170C'
            ));
            $available = false;

        }

        $images = (array)json_decode($property->images);

        $this->data->scroll[] = $this->getImageSlider($images, $available);

        if ($this->getVariable('role') == 'tenant') {
            $price = $property->price_per_month;
            $priceText = 'p/m';

            if ($this->settings && $this->settings->filter_price_per_week == 'price_per_week') {
                $price = $property->price_per_week;
                $priceText = 'p/w';
            }

            $area = $property->square_ft;
            $areaText = ' ft2';

            if ($this->settings && $this->settings->filter_sq_ft == 'sq_meter') {
                $area = $property->square_meters;
                $areaText = ' m2';
            }
        } else {
            $areaSettings = $this->getVariable('area_settings');
            $areaText = $areaSettings == 'sq_ft' ? 'ft2' : 'm2';
            $area = $areaSettings == 'sq_ft' ? $property->square_ft : $property->square_meters;

            $priceSettings = $this->getVariable('price_settings');
            $priceText = $priceSettings == 'price_per_month' ? 'p/m' : 'p/w';
            $price = $priceSettings == 'price_per_month' ? $property->price_per_month : $property->price_per_week;
        }

        $rowparams = array(
            'background-color' => '#ffffff',
            'padding' => '8 7 8 7',
            'vertical-align' => 'middle',
        );

        if (!$available) {
            $rowparams['opacity'] = '0.5';
        }

        $this->data->scroll[] = $this->getRow(array_merge(
			array(
				$this->getImage('icon-bed-green.png', array('width' => 17, 'vertical-align' => 'middle', 'margin' => '0 7 0 0')),
				$this->getText($property->num_bedrooms, array('style' => 'property-line-text')),
				$this->getVerticalSpacer(15),
				$this->getImage('icon-bath-green.png', array('width' => 17, 'vertical-align' => 'middle', 'margin' => '0 7 0 0')),
				$this->getText($property->num_bathrooms, array('style' => 'property-line-text')),
				$this->getVerticalSpacer(15),
			),
			$this->getPropertySize( $area, $areaText ),
			array(
				$this->getRow(array(
					$this->getText('£ ' . $price, array('style' => 'property-line-text-right-green')),
					$this->getText(' ' . $priceText, array('style' => 'property-line-text-right-green')),
				), array(
					'width' => '30%',
					'text-align' => 'right',
					'floating' => 1,
					'float' => 'right',
					'padding' => '0 10 0 0',
				)),
			)
        ), $rowparams);

	    $rows = $this->getFeaturesRows($property);

	    $this->data->scroll[] = $this->getImage('stripe.png', array('width' => '100%', 'margin' => '0 0 0 0'));
	    $this->renderFeatureRows($rows, $property, true);
	    $this->data->scroll[] = $this->getImage('div-line.png', array('width' => '100%', 'margin' => '10 0 20 0'));
	    $this->data->scroll[] = $this->getText(mb_strtoupper($property->description), array('style' => 'mobileproperty_details_text'));
	    $this->renderFeatureRows($rows, $property, false);

	    /*
		if (strlen($property->offer_code) > 7) {
			$secondRowContent[] = $this->getColumn(array(
				$this->getText('OFFER CODE', array('style' => 'property-meta-text-bold-static')),
				$this->getText($property->offer_code, array('style' => 'property-meta-text-gray-static')),
			), array('style' => 'property-meta-box-large'));
		} else {
			$secondRowContent[] = $this->getRow(array(
				$this->getText('OFFER CODE', array('style' => 'property-meta-text-bold')),
				$this->getText($property->offer_code, array('style' => 'property-meta-text-gray')),
			), array('style' => 'property-meta-box-large'));
		}
		*/

        $secondRowContent[] = $this->getRow(array(
            $this->getText('TYPE', array('style' => 'property-meta-text-bold')),
            $this->getText(ucfirst($property->property_type), array('style' => 'property-meta-text-gray')),
        ), array('style' => 'property-meta-box-large'));

        $secondRowContent[] = $this->getRow(array(
            $this->getText('PRICE', array('style' => 'property-meta-text-bold')),
            $this->getText('£ ' . $price . ' ' . $priceText, array('style' => 'property-meta-text-green')),
        ), array('style' => 'property-meta-box-large'));

        $box[] = $this->getRow($secondRowContent, array(
            'margin' => '10 5 20 5'
        ));

        $box[] = $this->getRow(array_merge(
	        $this->getPropertySize( $area, $areaText, 'bubble' ),
	        array(
		        $this->getRow(array(
			        $this->getImage('icon-bed.png', array('style' => 'property-meta-icon')),
			        $this->getText($property->num_bedrooms, array('style' => 'property-meta-text-gray')),
		        ), array('style' => 'property-meta-box')),
		        $this->getVerticalSpacer(10),
		        $this->getRow(array(
			        $this->getImage('icon-bath.png', array('style' => 'property-meta-icon')),
			        $this->getText($property->num_bathrooms, array('style' => 'property-meta-text-gray')),
		        ), array('style' => 'property-meta-box')),
	        )
        ), array(
            'margin' => '0 12 10 12',
        ));

        $thirdRowContent[] = $this->getRow(array(
            $this->getText(strtoupper('{#available#}'), array('style' => 'property-meta-text-bold')),
            $this->getText(date('d-M-Y', strtotime($property->available_date)), array('style' => 'property-meta-text-gray')),
        ), array('style' => 'property-meta-box-large-full-width'));

        $box[] = $this->getRow($thirdRowContent, array(
            'margin' => '10 5 15 5'
        ));

        $tenancy_option_text = '';

        if ($property->tenancy_option_shortlet AND $property->tenancy_option_longlet) {
            $tenancy_option_text = '{#shortlet#} / {#longlet#}';
        } else if ($property->tenancy_option_longlet) {
            $tenancy_option_text = '{#longlet#}';
        } else if ($property->tenancy_option_shortlet) {
            $tenancy_option_text = '{#shortlet#}';
        }

        $tenancy_option[] = $this->getRow(array(
            $this->getText(strtoupper('{#tenancy#}'), array('style' => 'property-meta-text-bold')),
            $this->getText($tenancy_option_text, array('style' => 'property-meta-text-gray')),
        ), array('style' => 'property-meta-box-large-full-width'));

        $box[] = $this->getRow($tenancy_option, array(
            'margin' => '0 5 15 5'
        ));

        $box[] = $this->getRow(array(
            $this->getRow(array(
                $this->getText(strtoupper('{#ref#}'), array('style' => 'property-meta-text-bold')),
                $this->getText($property->offer_code, array('style' => 'property-meta-text-gray')),
            ), array('style' => 'property-meta-box-large-full-width'))
        ), array(
            'margin' => '0 5 20 5'
        ));

        if ($property->tenant_fee OR $property->tenant_fee != 0) {
            $fourthRowContent[] = $this->getRow(array(
                $this->getText(strtoupper('{#fee#}'), array('style' => 'property-meta-text-bold')),
                $this->getText('£' . $property->tenant_fee, array('style' => 'property-meta-text-gray')),
            ), array('style' => 'property-meta-box-large-full-width'));

            $box[] = $this->getRow($fourthRowContent, array(
                'margin' => '0 5 20 5'
            ));
        }

        /*
        if ($property->offer_code) {
            $fourthRowContent[] = $this->getRow(array(
                $this->getText(strtoupper('{#offer_code#}'), array('style' => 'property-meta-text-bold')),
                $this->getText($property->offer_code, array('style' => 'property-meta-text-gray')),
            ), array('style' => 'property-meta-box-large-full-width'));

            $box[] = $this->getRow($fourthRowContent, array(
                'margin' => '0 5 20 5'
            ));
        }
        */

        $this->data->scroll[] = $this->getColumn($box, array('style' => 'propertydetail-shadowbox'));

        if ($status == 'like') {
            $onclick = new stdClass();
            $onclick->action = 'open-action';
            $onclick->id = $this->getTwoWayChatId($property->play_id);
            $onclick->action_config = $this->getConfigParam('chat');
            $onclick->back_button = 1;
            $onclick->sync_open = 1;

            $this->data->scroll[] = $this->getButtonWithIcon('rental-chat-button-icon.png', 'chat', strtoupper('{#chat_with_landlord#}'),
                array('id' => 'chat', 'style' => 'property_chat_btn'), array('color' => '#ffffff'), $onclick);

            $this->data->scroll[] = $this->getButtonWithIcon('remove-from-liked.png', 'remove-bookmark-' . $property->id, strtoupper('{#remove_from_liked#}'),
                array('style' => 'property_remove_bookmark_btn'), array('color' => '#ffffff'));
        } else {
            if ($this->getVariable('role') == 'tenant') {

                $col[] = $this->getVerticalSpacer('25%');

                $col[] = $this->getImage('icon-dislike.png', array('width' => '20%',
                    'onclick' => $this->getOnclick('id', false, 'rental-dislike-' . $property->id)));

                $col[] = $this->getVerticalSpacer('10%');

                $col[] = $this->getImage('icon-like.png', array('width' => '20%',
                    'onclick' => $this->getOnclick('id', false, 'rental-like-' . $property->id)));

                $col[] = $this->getVerticalSpacer('25%');


                $this->data->scroll[] = $this->getRow($col, array('text-align' => 'center', 'margin' => '20 0 30 0'));
            }
        }


        if ($property->play_id == $this->playid) {
            // $this->data->footer[] = $this->getTextbutton('{#back#}', array( 'style' => 'back-button', 'id' => 'go-back' ) );
            $this->data->footer[] = $this->getTextbutton(strtoupper('{#edit#}'), array('id' => 'edit', 'style' => 'submit-button-gray', 'onclick' => $this->getOnclick('id', true, 'open-' . $property->id)));
        }
    }

    public function renderFeatureRows($rows, $property, $onlyAddress = false)
    {
    	
        foreach ($rows as $row_items) {
            $current_output = array();

            $count = 0;

            foreach ($row_items as $property_key => $item) {
                if ($property_key == 'district' && !$onlyAddress) {
                    continue;
                }

                $title = $item['title'];

	            if ( empty($title) AND ( isset($property->name) AND $property->name ) ) {
	            	$title = $property->name;
	            }
                
                $count++;

                if ($title == '{dynamic}') {
                    $title = ucfirst($property->{$property_key});
                }

                $item_args = array(
                    'padding' => '12 0 12 10',
                    'width' => '50%',
                    'text-align' => 'left',
                    'vertical-align' => 'middle',
                );

                if ($property_key == 'district' AND $property->district_lat AND $property->district_lng) {
                    $coords = $property->district_lat . ',' . $property->district_lng;
                    $url = 'https://www.google.com/maps/search/?api=1&query=' . $coords;

                    // $url = 'geo:'. $coords .'?z=16';
                    // if ( isset($this->varcontent['system_source']) AND $this->varcontent['system_source'] != 'client_android' ) {
                    //     $url = 'http://maps.apple.com/?ll=' . $coords;
                    // }

                    $onclick = new StdClass();
                    $onclick->action = 'open-url';
                    $onclick->action_config = $url;

                    $title_row[] = $this->getText($title, array(
                        'padding' => '0 0 0 10',
                        'text-align' => 'left',
                        'vertical-align' => 'middle',
                        'color' => '#33497d',
                        'width' => '100%',
                        'font-size' => '19',
                        'font-weight' => 'bold',
                    ));

                    if ($property->full_address AND $this->getSavedVariable('role') != 'tenant') {
                        $title_row[] = $this->getText($property->full_address, array(
                            'color' => '#33497d',
                            'font-size' => '14',
                            'padding' => '0 0 5 10',
                            'width' => '100%'
                        ));
                    }

                    $current_output[] = $this->getRow(array(
                        $this->getImage($item['icon'] . '.png', array(
                            'width' => 40,
                            'margin' => '10 0 0 10'
                        )),
                        $this->getColumn($title_row, array(
                            'width' => 'auto',
                            'vertical-align' => 'middle',
                        ))
                    ), array(
                        'width' => '100%',
                        'vertical-align' => 'middle',
                        'onclick' => $onclick,
                    ));

                } else {
                    $current_output[] = $this->getRow(array(
                        $this->getImage($item['icon'] . '.png', array(
                            'width' => 20
                        )),
                        $this->getText($title, array(
                            'padding' => '0 0 0 10',
                            'text-align' => 'left',
                            'vertical-align' => 'middle',
                            'color' => '#a6a6a6',
                        )),
                    ), $item_args);
                }

                if ($count == 1) {
                    $current_output[] = $this->getVerticalSpacer('1', array('background-color' => '#ededed', 'padding' => '12 0 12 0'));
                }

                unset($item_args);
            }

            if ($property->available) {
                $pp = array();
            } else {
                $pp = array('opacity' => '0.5');
            }

            $this->data->scroll[] = $this->getRow($current_output, $pp);

            if ($onlyAddress) {
                return true;
            }

            $this->data->scroll[] = $this->getHairline('#ededed');

            unset($current_output);
        }

        return true;
    }

    public function getFeaturesRows($property)
    {

        $features = $this->getAvailableFeatures($property->attributes);

        $features['district'] = $property->district;

        if ($property->furnished OR $property->feature_furnished) {
            $features['feature_furnished'] = 'Furnished';
        }

        $balconies = array('balcony', 'terrace', 'patio', 'garden');
        $types = array();

        foreach ($balconies as $key => $balcony) {
            if ($property->{'balcony_type_' . $balcony}) {
                $types[] = ucfirst($balcony);
            }
        }

        $types = implode(', ', $types);
        if (!empty($types)) {
            $features['balcony_type'] = $types;
        }

        $icons_map = array(
            'district' => array(
                'icon' => 'google-map-icon',
                'title' => $property->district
            ),
            'feature_furnished' => array(
                'icon' => 'icon-feature-furnished',
                'title' => 'Furnished',
            ),
            'feature_air_conditioner' => array(
                'icon' => 'icon-feature-ac',
                'title' => 'AC',
            ),
            'feature_dish_washer' => array(
                'icon' => 'icon-feature-dishwasher',
                'title' => 'Dishwasher',
            ),
            'balcony_type' => array(
                'icon' => 'icon-feature-balcony',
                'title' => '',
            ),
            'feature_pets_allowed' => array(
                'icon' => 'icon-pets-allowed',
                'title' => 'Pets Allowed'
            ),
            'feature_lift' => array(
                'icon' => 'icon-lift',
                'title' => 'Lift'
            ),
            'feature_concierge' => array(
                'icon' => 'icon-concierge',
                'title' => 'Concierge'
            ),
            'feature_private_parking' => array(
                'icon' => 'icon-parking',
                'title' => 'Private Parking'
            ),
            'feature_students_welcome' => array(
                'icon' => 'icon-student',
                'title' => 'Students Welcome'
            ),
            'feature_bills_included' => array(
                'icon' => 'icon-bills',
                'title' => 'Bills Included'
            ),
            'feature_washing_machine' => array(
                'icon' => 'icon-washing-machine',
                'title' => 'Washing Machine'
            )
        );

        $sorted_data = array();
        foreach ($icons_map as $key => $value) {
            if (!isset($features[$key])) {
                continue;
            }

            if ($key == 'balcony_type') {
                $value['title'] = $features['balcony_type'];
            }

            $sorted_data[$key] = $value;
        }

        $first_el = $this->arrayShiftAssoc($sorted_data);
        $chunks = array_chunk($sorted_data, 2, true);
        array_unshift($chunks, $first_el);

        return $chunks;
    }

    // returns value
    public function arrayShiftAssoc(&$arr)
    {
        $val = reset($arr);
        $key = key($arr);
        $ret = array($key => $val);
        unset($arr[$key]);
        return $ret;
    }

    /**
     * Delete property
     *
     * @param $id
     */
    public function delete(int $id)
    {
        try {
            MobilepropertiesModel::model()->deleteByPk($id);
        } catch (\Exception $e) {
            // Handle exception
        }
    }

    public function getImageSlider($images, $available)
    {
        $images = array_values($images);
        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false, 'imgwidth' => 900, 'imgheight' => 720, 'imgcrop' => 'yes'));

        $height = round($this->screen_width / 1.25, 0);

        $image_styles['imgwidth'] = '900';
        $image_styles['imgheight'] = '720';
        $image_styles['width'] = $this->screen_width;
        $image_styles['height'] = $height;
        $image_styles['imgcrop'] = 'yes';
        $image_styles['not_to_assetlist'] = true;
        $image_styles['priority'] = '9';
        $image_styles['image_fallback'] = $fallback_image_path;

        $image_styles['tap_to_open'] = 1;

        $navi_styles['margin'] = '-45 0 0 0';
        $navi_styles['align'] = 'center';

        $totalcount = count($images);
        $items = array();

        foreach ($images as $i => $image) {
            $scroll = array();
            $image_styles['tap_image'] = $this->getImageFileName($image);
            $scroll[] = $this->getImage($image, $image_styles);
            $scroll[] = $this->getSwipeNavi($totalcount, ($i + 1), $navi_styles);
            $items[] = $this->getColumn($scroll, array('margin' => '0 0 0 0', 'background-color' => '#000000'));
        }
        if ($available) {
            return $this->getSwipearea($items, array('background-color' => '#ffffff', 'width' => $this->screen_width));
        } else {
            return $this->getSwipearea($items, array('background-color' => '#ffffff', 'width' => $this->screen_width, 'opacity' => '0.5'));
        }

    }

    public function getPropertyHeader( $property_name ) {
        $this->rewriteActionField('subject', strtoupper($property_name));
        $this->rewriteActionConfigField('hide_subject', 1);
        $this->rewriteActionConfigField('hide_menubar', 1);

        $text = strtoupper($property_name);

        $onclick = new stdClass();
        $onclick->action = 'go-home';

        $this->data->header[] = $this->getRow(array(
            $this->getColumn(array(
	            $this->getText('❮', array(
		            'color' => ( isset($this->colors['top_bar_text_color']) ? $this->colors['top_bar_text_color'] : '#FFFFFF' ),
		            'font-size' => '25',
		            'vertical-align' => 'middle',
	            )),
            ), array(
                'width' => '11%',
                'margin' => '0 0 0 15',
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'onclick' => $onclick,
            )),
            $this->getColumn(array(
                $this->getText($text, array(
                    'padding' => '0 10 0 10',
                    'text-align' => 'center',
                    'color' => ( isset($this->colors['top_bar_text_color']) ? $this->colors['top_bar_text_color'] : '#FFFFFF' ),
                )),
            ), array(
                'text-align' => 'center',
                'width' => '78%',
            )),
            $this->getColumn(array(
                $this->getImage('icon-menu-placeholder.png', array(
                    'width' => '20',
                )),
            ), array(
                'width' => '11%',
                'vertical-align' => 'middle',
                'text-align' => 'center',
            )),
        ), array(
            'padding' => '12 0 12 0',
            'vertical-align' => 'middle',
            'background-color' => $this->color_topbar,
        ));
    }

	private function getPropertySize( $area, $areaText, $mode = 'image-bar' ) {

    	if ( empty($area) ) {
    		return array();
	    }

	    if ( $mode == 'image-bar' ) {
			return array(
				$this->getImage('icon-house-green.png', array('width' => 17, 'vertical-align' => 'middle')),
				$this->getText(' ' . $area . $areaText, array('style' => 'property-line-text')),
			);
	    } else {

		    return array(
			    $this->getRow(array(
				    $this->getImage('icon-house.png', array('style' => 'property-meta-icon')),
				    $this->getText($area . $areaText, array('style' => 'property-meta-text-gray')),
			    ), array('style' => 'property-meta-box')),
			    $this->getVerticalSpacer(10)
		    );

	    }

	}

}