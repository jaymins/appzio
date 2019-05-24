<?php

namespace packages\actionMregister\themes\cityapp\Views;

use packages\actionMregister\themes\adidas\Components\Components;
use packages\actionMregister\Views\View as BootstrapView;

class View extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;


    /* view will always need to have a function called tab1 */
    /**
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('{#registration#}', [], [
            'font-size' => '30',
            'color' => '#171819',
            'padding' => '25 25 15 25',
            'text-align' => 'center'
        ]);

        $this->layout->scroll[] = $this->getComponentText('{#please_register#}', [], [
            'text-align' => 'center',
            'color' => '#171819',
            'padding' => '10 15 15 15',
            'font-size' => '18',
        ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('cityapp-menu-line.png', [
                'width' => 1000,
                'priority' => 1
            ], [
                'width' => '60%'
            ])
        ], [], [
            'text-align' => 'center',
            'margin' => '20 0 20 0',
        ]);

        $this->getRegisterForm();

        $this->layout->scroll[] = $this->getComponentSpacer('20');

        $this->getMesages();

        $this->getActionButtons();

        return $this->layout;
    }

    private function getMesages()
    {
        if (!isset($this->model->validation_errors) OR empty($this->model->validation_errors)) {
            return false;
        }

        foreach ($this->model->validation_errors as $error) {
            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText($error, [], [
                    'font-size' => '13',
                    'color' => '#FF0200',
                ])
            ], [], [
                'text-align' => 'center',
                'margin' => '5 15 5 15'
            ]);
        }

        return true;
    }

    private function getFieldData($field)
    {

        if ($this->model->getSubmittedVariableByName($field)) {
            $value = $this->model->getSubmittedVariableByName($field);
        } elseif ($this->model->getSavedVariable($field)) {
            $value = $this->model->getSavedVariable($field);
        } else {
            $value = '';
        }

        return $value;
    }

    private function getRegisterForm()
    {

        $this->layout->scroll[] = $this->getComponentColumn([
            $this->getComponentFormFieldText($this->getFieldData('username'), [
                'hint' => '{#your_name#}',
                'variable' => 'username',
                'style' => 'cityapp-form-field',
            ])
        ], [
            'style' => 'cityapp-form-field-container'
        ]);

        $this->layout->scroll[] = $this->getComponentColumn([
            $this->getComponentFormFieldText($this->getFieldData('email'), [
                'hint' => '{#email#}',
                'variable' => 'email',
                'input_type' => 'email',
                'style' => 'cityapp-form-field',
            ])
        ], [
            'style' => 'cityapp-form-field-container'
        ]);

        $this->layout->scroll[] = $this->getComponentColumn([
            $this->getComponentFormFieldPassword('', [
                'hint' => '{#password#}',
                'variable' => 'password',
                'style' => 'cityapp-form-field',
            ])
        ], [
            'style' => 'cityapp-form-field-container'
        ]);

        $this->layout->scroll[] = $this->getComponentColumn([
            $this->getComponentFormFieldPassword('', [
                'hint' => '{#confirm_password#}',
                'variable' => 'password_again',
                'style' => 'cityapp-form-field',
            ])
        ], [
            'style' => 'cityapp-form-field-container'
        ]);

        return true;
    }

    private function getActionButtons()
    {
        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->uiKitButtonFilled('{#sign_up#}', [
                    'onclick' => $this->getOnclickSubmit('Controller/PageOne'),
                ], [
                    'border-radius' => '5',
                    'background-color' => '#ef4345',
                    'width' => '100%',
                    'color' => '#fbfbfb',
                    'padding' => '15 0 15 0',
                    'height' => 'auto',
                    'font-size' => '18',
                ])
            ], [], [
                'width' => '100%',
                'text-align' => 'center',
            ])
        ], [], [
            'width' => '100%',
            'margin' => '30 0 15 0',
        ]);

        $this->layout->footer[] = $this->getComponentImage('cityapp-registration-footer.png', [], [
            'width' => '100%'
        ]);

    }

}