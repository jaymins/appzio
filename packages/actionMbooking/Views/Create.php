<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMbooking\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMbooking\Models\Model as ArticleModel;

class Create extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMbooking\Components\Components
     */
    public $components;
    public $theme;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        if ($this->getData('saved', 'bool')) {
            $this->layout->onload[] = $this->getOnclickClosePopup();
            return $this->layout;
        }

        $this->renderHeader();

        $this->renderDatePicker();

        $this->renderTimePicker();

        $this->renderNotesField();

        $this->renderSubmitButton();

        return $this->layout;
    }

    protected function renderHeader()
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#book_slot#}'), array(), array(
                'color' => '#ffffff'
            )),
        ), array(), array(
            'background-color' => '#edac34',
            'padding' => '15 10 15 10'
        ));
    }

    protected function renderDatePicker()
    {
        $this->layout->scroll[] = $this->components->uiKitHintedCalendar('Date', 'date', time(), array(
            'active_icon' => 'calendar-icon.png',
            'inactive_icon' => 'calendar-icon.png'
        ), array());

        $this->renderValidationErrors('date');
    }

    protected function renderTimePicker()
    {
        $this->layout->scroll[] = $this->components->uiKitHintedTime(date('H'));
        $this->renderValidationErrors('time');
    }

    protected function renderNotesField()
    {
        $this->layout->scroll[] = $this->getComponentFormFieldTextArea('', array(
            'variable' => 'notes',
            'hint' => 'Notes...',
            'style' => 'booking_notes_field'
        ));

        $this->renderValidationErrors('notes');
    }

    protected function renderSubmitButton()
    {
        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#book#}'), array(
                'onclick' => $this->getOnclickSubmit('Controller/save'),
                'style' => 'booking_button'
            ))
        ), array(
            'style' => 'booking_submit_button_wrapper'
        ));
    }

    protected function renderValidationErrors($variableName)
    {
        if (isset($this->model->validation_errors[$variableName])) {
            $this->layout->scroll[] = $this->components->validationErrorText($this->model->validation_errors[$variableName]);
        }
    }
}
