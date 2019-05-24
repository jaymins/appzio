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

class AddMobileproperties extends MobilepropertiesController
{

    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;
    public $errors;

    public $action_mode;

    /**
     * Main entry point of the controller
     *
     * @return stdClass
     */


    public function tab1(){
        $this->initModel();
        $this->data = new stdClass();

        switch($this->menuid){

            case 'insert':
                $this->insertNew();
                break;

            default:
                $this->rewriteActionConfigField('backarrow', true);
                $this->rewriteActionField('subject', 'Add Property');
                if($this->getSavedVariable('propertypic')){
                    $this->clearPropertyImages();
                }
                $this->addForm();
                break;
        }

        return $this->data;

    }

    public function insertNew(){

        $this->propertyModel->saveFullPropertyAddress();

        $images = $this->getPropertyImages();

        $this->prepopulateSavedValues();
        $this->validatePropertyInput($images);

        if (!empty($this->errors)) {
            $this->addForm();
            return true;
        }

        $saved_id = $this->propertyModel->saveSubmit('insert', false, $images);

        // Send an email
        if ( $this->getSubmittedVariableByName('temp_reference_check') ) {

            $landlord_name = $this->getSavedVariable('real_name');
            $email_to = 'spmitev@gmail.com';
            $email_body = 'A new property with ID: '. $saved_id .' was added and requires reference check. Landlord name is '. $landlord_name .' and his ID is ' . $this->playid;

            Aenotification::addUserEmail(
                $this->playid,
                'New property for reference check added',
                $email_body,
                $this->actionobj->gameid,
                $email_to
            );

        }

        if ($saved_id) {
            // Clean the temporarily saved variables after a success
            $this->cleanTempVariables();
        }

	    $onclick = new stdClass();
	    $onclick->action = 'open-action';
	    $onclick->action_config = $this->getActionidByPermaname('properties');
	    $onclick->sync_open = 1;
	    $this->data->onload[] = $onclick;

        /*
        if ( isset($this->submitvariables['temp_tenancy_agreement']) ) {

	        $onclick = new stdClass();
	        $onclick->id = 'add|' . $saved_id;
	        $onclick->action = 'open-action';
	        $onclick->action_config = $this->getActionidByPermaname('property-upsells');
	        $onclick->sync_open = 1;
	        $this->data->onload[] = $onclick;

        } else {

	        $onclick = new stdClass();
	        $onclick->action = 'open-action';
	        $onclick->action_config = $this->getActionidByPermaname('properties');
	        $onclick->sync_open = 1;
	        $this->data->onload[] = $onclick;

        }
        */

    }

    /**
     * Show property creation form
     */
    public function addForm()
    {
        $model = new MobilepropertiesModel();

        $this->setGridWidths();
        $this->propertyEditSaves();

        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if ($this->menuid == 'del-images-') {
            $user[] = $this->getText('{#click_on_images_to_delete#}', array('vertical-align' => 'top', 'font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user, array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        }

        $this->data->scroll[] = $this->getSpacer(15);
        $this->data->scroll[] = $this->getImageGrid();
        $this->data->scroll[] = $this->getSpacer(15);
        // $this->getEdinImagesButton();

        $this->getPropertyForm();

        // If there are validation errors print them in the footer
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $this->data->scroll[] = $this->getText($error, array('text-align' => 'center', 'color' => '#FF0000'));
            }
        }

        $this->data->scroll[] = $this->getSpacer(20);
//        $this->data->scroll[] = $this->getTextbutton(strtoupper('{#add#}'), array(
//            'id' => 'insert',
//            'font-size' => '18',
//            'font-weight' => 'bold',
//            'color' => '#ffffff',
//            'background-color' => '#68c763',
//            'text-align' => 'center',
//            'padding' => '19 15 19 15',
//            'width' => $this->screen_width / 2
//        ));
//        $buttonsRow[] = $this->getTextbutton(strtoupper('{#back#}'), array(
//            'id' => 'go-back',
//            'onclick' => $this->getOnclick('go-home'),
//            'font-size' => '18',
//            'font-weight' => 'bold',
//            'color' => '#ffffff',
//            'background-color' => '#1a1b36',
//            'text-align' => 'center',
//            'padding' => '19 15 19 15',
//            'height' => '60',
//            'width' => $this->screen_width / 2
//        ));

        $buttonsRow[] = $this->getTextbutton(strtoupper('{#add_property#}'), array(
            'id' => 'insert',
            'style' => 'submit-button-gray'
        ));

        $this->data->scroll[] = $this->getRow($buttonsRow);
    }


    public function storeProperty()
    {
        $id = str_replace('save-edit-', '', $this->menuid);
        $images = $this->getPropertyImages();
        $this->validatePropertyInput($images);
        if (!empty($this->errors)) {
            $this->showProperty($id);
            return $this->data;
        }
        $this->propertyModel->saveSubmit('update', $id, $images);
    }
    
}