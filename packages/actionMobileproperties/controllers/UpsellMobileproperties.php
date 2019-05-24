<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileproperties.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class UpsellMobileproperties extends MobilepropertiesController
{

    public $data;
    public $theme;

    /** @var MobilepropertiesModel */
    public $propertyModel;

    public $current_property;

    /**
     * Main entry point of the controller
     *
     * @return stdClass
     */
    public function tab1(){
        $this->initModel();
        $this->data = new stdClass();

        $passed_data = $this->sessionGet('temp_stored_property_id');

        if ( $this->menuid AND !isset($_REQUEST['purchase_product_id']) ) {
	        $this->sessionSet('temp_stored_property_id', $this->menuid);
	        $passed_data = $this->menuid;
        }

        if ( empty($passed_data) ) {
            $this->data->scroll[] = $this->getText('{#action_loaded#}', array(
	            'style' => 'rentals-info-message-text'
            ));

            return $this->data;
        }

        $pieces = explode('|', $passed_data);
        
        $mode = $pieces[0];
        $property_id = $pieces[1];

        $this->current_property = $this->propertyModel->findByPk( $property_id );

        if ( empty($this->current_property ) ) {
	        $this->data->scroll[] = $this->getText('General error - missing property', array(
	        	'style' => 'general_example_text',
	        ));
        }

	    $this->setPropertyPayment();

	    $text = '{#thank_you_for_your_interest_in_our_full_property_management#}';

	    $this->data->scroll[] = $this->getColumn(array(
		    $this->getText($text, array(
			    'text-align' => 'center',
			    'padding' => '15 15 15 15',
		    )),
		    $this->getRow(array(
			    $this->getImage('full-management.png', array(
				    'width' => '200',
			    )),
		    ), array(
			    'text-align' => 'center',
			    'margin' => '10 20 15 20',
		    )),
		    $this->getRow(array(
			    $this->getText('Advertise on Zoopla and PrimeLocation', array(
				    'font-size' => '24',
				    'text-align' => 'center',
				    'font-android' => 'Roboto-bold',
				    'font-ios' => 'Roboto-bold',
			    )),
		    ), array(
			    'width' => 'auto',
			    'text-align' => 'center',
			    'margin' => '0 30 15 30',
		    )),
		    $this->getRow(array(
			    $this->getText('We are associated with the best management companies around the UK and will advise you on the most suitable option for your property.', array(
				    'text-align' => 'center',
			    )),
		    ), array(
			    'width' => 'auto',
			    'margin' => '0 30 25 30',
		    )),
		    $this->getRow(array(
			    $this->getText(strtoupper('{#get_it_now#}'), array(
				    'width' => '100%',
				    'font-size' => '18',
				    'font-weight' => 'bold',
				    'background-color' => '#6bc568',
				    'color' => '#ffffff',
				    'padding' => '13 5 13 5',
				    'text-align' => 'center',
			    )),
		    ), array(
			    'width' => '100%',
			    'text-align' => 'center',
			    'onclick' => $this->getOnclick('purchase', false, array(
				    'product_id_ios' => 'advertising.01',
				    'product_id_android' => 'advertising.01',
			    )),
		    )),
	    ), array(
		    'width' => 'auto',
		    'background-color' => '#FFFFFF',
		    'border-radius' => '8',
		    'shadow-color' => '#33000000',
		    'shadow-radius' => 3,
		    'shadow-offset' => '0 1',
		    'margin' => '20 20 20 20',
	    ));

	    $this->getCloseButton( $property_id, $mode );

        return $this->data;
    }

	private function getCloseButton( $property_id, $mode ) {

		$onclick = new stdClass();
		$onclick->action = 'open-action';
		$onclick->action_config = $this->getActionidByPermaname('propertyview');
		$onclick->id= 'property-id-' . $property_id;
		$onclick->sync_open = 1;

		if ( $mode == 'add' ) {
			$onclick = new stdClass();
			$onclick->action = 'open-action';
			$onclick->action_config = $this->getActionidByPermaname('properties');
			$onclick->sync_open = 1;
		}

		$this->data->scroll[] = $this->getText('Thanks, I\'m fine without it', array(
			'font-size' => 13,
			'text-align' => 'center',
			'color' => '#66aaff',
			'onclick' => $onclick,
		));
	}

	private function setPropertyPayment() {

		if ( !isset($_REQUEST['purchase_product_id']) OR $_REQUEST['purchase_product_id'] != 1 ) {
			return false;
		}

		$this->current_property->is_premium = 1;
		$this->current_property->do_advertising = 1;
		$this->current_property->update();

		$onload = new stdClass();
		$onload->action = 'open-action';
		$onload->action_config = $this->getActionidByPermaname('properties');
		$onload->sync_open = 1;
		$this->data->onload[] = $onload;

		return $this->data;
	}

}