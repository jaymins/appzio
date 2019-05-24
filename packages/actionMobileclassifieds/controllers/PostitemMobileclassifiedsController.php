<?php

/*

    This is the main controller of the action.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileclassifieds.models.*');

class PostitemMobileclassifiedsController extends MobileclassifiedsController {

    public $data;
    public $theme;
    public $error = false;
    public $error_messages = array();


    /* this will create the model and feed the contents of the controller to the model
       This way its easy to access things like getSavedVariable and other data helpers
    */

    public function validate()
    {
        $this->validateCommonVars();

        if ($this->error) {
            $this->displayErrors();
            $this->loadVariableContent(true);
        }

        return !$this->error;

    }

    public function validateCommonVars()
    {
        $variables_to_validate = array(
            'category' => 'Category is missing',
            'title' => 'Title is missing',
            'description' => 'Description is missing',
            'price' => 'Price is missing',
        );

        foreach($variables_to_validate as $var => $message) {
            $key = $this->vars[$var];
            if (empty($this->itemsModel->factory->submitvariables[$key]) || preg_match('~choose~', strtolower($this->itemsModel->factory->submitvariables[$key]))) {
                $this->error = true;
                $this->error_messages[] = $message;
            }
        }
    }

    public function displayErrors()
    {
        if ( empty($this->error_messages) OR !is_array($this->error_messages) ) {
            return false;
        }

        foreach ($this->error_messages as $message) {
            $this->data->footer[] = $this->getText($message, array(
                'text-align' => 'center',
                'padding' => '5 5 5 5',
                'color' => '#FF0000'
            ));
        }

        return true;
    }
}