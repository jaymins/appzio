<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Makebooking extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;
    public $booking_data;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->booking_data = $this->getData('booking_data', 'array');
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#new_booking#}');

        $general_error = '{#please_complete_all_the_fields_before_continuing#}';

        $this->layout->header[] = $this->getComponentSpacer(10);
        $this->layout->header[] = $this->getComponentText($general_error,['style' => 'nexudus_uikit_loginheader']);
        $this->layout->header[] = $this->getComponentSpacer(10);
        $this->layout->header[] = $this->uiKitDivider();

        $col[] = $this->getPlace();
        $col[] = $this->getCalendar();
        $col[] = $this->getTime();
        $col[] = $this->getLength();

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 30 15']);

        if($this->model->validation_errors){
            $this->layout->header[] = $this->getComponentDivider();
            $this->layout->header[] = $this->getComponentText($general_error,['style' => 'nexudus_error_header']);

            foreach($this->model->validation_errors as $error){
                $this->layout->header[] = $this->getComponentDivider();
                $this->layout->header[] = $this->getComponentText($error,['style' => 'nexudus_error_header']);
            }
        }

        $this->layout->footer[] = $this->uiKitButtonFilled('{#continue#}',[
            'onclick' => $this->getOnclickSubmit('Makebooking/step2/do',['viewport' => 'top'])
        ],[
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
        ]);


/*        if($this->model->getConfigParam('debug')){

            $this->layout->scroll[] = $this->getComponentText($this->model->getSavedVariable('stripe_customer_id'));

            $this->layout->footer[] = $this->uiKitButtonFilled('{#choose_card#}',[
                'onclick' => $this->getOnclickStripeChooseCard()
            ],[
                'background-color' => $this->color_top_bar_color,
                'color' => $this->color_top_bar_text_color,
            ]);

            $this->layout->footer[] = $this->uiKitButtonFilled('{#pay#}',[
                'onclick' => $this->getOnclickStripePurchase()
            ],[
                'background-color' => $this->color_top_bar_color,
                'color' => $this->color_top_bar_text_color,
            ]);

        }*/

        $this->layout->footer[] = $this->getComponentSpacer(15);
        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    public function getCalendar(){

        $date = $this->model->getSubmittedVariableByName('date') ? $this->model->getSubmittedVariableByName('date') : time();

        return $this->components->uiKitExpandingField([
            'divider' => true,
            'icon' => 'icon-nexudus-calendar.png',
            'variable' => 'date',
            'value' => $date,
            'value_color' => '#ffffff',
            'title' => '{#date#}',
            'expanding_content' => $this->getCalendarSelector()
        ]);

    }

    public function getCalendarSelector(){

        if(isset($this->booking_data['date'])){
            $date = $this->booking_data['date'];
        } else {
            $date = $this->model->getSubmittedVariableByName('date');
        }

        return $this->getComponentCalendar([
            'variable' => 'date',
            'date' => $date,
            'min_date' => time(),
            'header_style' => ['color' => '#FFFFFF','background-color' => '#383535'],
            'weekdays_style' => ['color' => '#FFFFFF'],
            'weekend_style' => ['color' => '#BFBFBD'],
        ],['background-color' => '#3A373A','color' => '#ffffff']);

    }

    public function getPlaceSelector(){
        $locations = $this->getData('locations', 'string');

        if(isset($this->booking_data['location'])){
            $place_value = $this->booking_data['location'];
        } else {
            $place_value = $this->model->getSubmittedVariableByName('location');
        }

        return $this->getComponentFormFieldSelectorList($locations,[
            'value' => $place_value,
            'variable' => 'location',
            'update_on_entry' => 1
        ],[
            'color' => '#ffffff',
            'background-color' => '#3A373A'
        ]);

    }

    private function getPlace()
    {

        return $this->components->uiKitExpandingField([
            'divider' => true,
            'icon' => 'icon-nexudus-location.png',
            'variable' => 'location',
            'value_color' => '#ffffff',
            'value' => isset($this->booking_data['location']) ? $this->booking_data['location'] : $this->model->getSubmittedVariableByName('location'),
            'title' => '{#location#}',
            'expanding_content' => $this->getPlaceSelector()
        ]);


    }

    private function getValue($name,$default){

        if(isset($this->booking_data[$name]) AND $this->booking_data[$name]){
            return $this->booking_data[$name];
        } elseif($this->model->getSubmittedVariableByName($name)){
            return $this->model->getSubmittedVariableByName($name);
        }

        return $default;


    }


    private function getTime()
    {

        return $this->components->uiKitExpandingField([
            'divider' => true,
            'icon' => 'icon-nexudus-time.png',
            'variable' => 'hour',
            'variable2' => 'minute',
            'value' => $this->getValue('hour', 9),
            'value2' => $this->getValue('minute', '00'),
            'value_color' => '#ffffff',
            'var_separator' => ':',
            'title' => '{#time#}',
            'expanding_content' => $this->getTimeSelectors()
        ]);
    }

    public function getActiveHour(){
        if(isset($this->booking_data['hour'])){
            $hour = $this->booking_data['hour'];
        } elseif($this->model->getSubmittedVariableByName('hour')) {
            $hour = $this->model->getSubmittedVariableByName('hour');
        } else {
            $hour = 9;
        }

        $hour = str_replace('am', '', $hour);
        $hour = str_replace('pm', '', $hour);

        return $hour;
    }

    public function getActiveMinute(){
        if(isset($this->booking_data['minute'])){
            $minute = $this->booking_data['minute'];
        } elseif($this->model->getSubmittedVariableByName('minute')) {
            $minute = $this->model->getSubmittedVariableByName('minute');
        } else {
            $minute = 0;
        }

        return $minute;
    }

    public function getTimeSelectors(){
        $hours = $this->getData('hours', 'string');
        $minutes = $this->getData('minutes', 'string');

        $col[] = $this->getComponentFormFieldSelectorList($hours,[
            'variable' => 'hour',
            'value' => $this->getActiveHour(),
            'update_on_entry' => 1
        ],[
            'color' => '#ffffff',
            'text-align' => 'center',
            'width' => '50%',
            'background-color' => '#3A373A'
        ]);

        $col[] = $this->getComponentFormFieldSelectorList($minutes,[
            'variable' => 'minute',
            'value' => $this->getActiveMinute(),
            'update_on_entry' => 1
        ],[
            'color' => '#ffffff',
            'text-align' => 'center',
            'width' => '50%',
            'background-color' => '#3A373A'
        ]);

        return $this->getComponentRow($col,[],['text-align' => 'center']);

    }

    public function getLengthSelector(){

        if(isset($this->booking_data['edit_id'])){
            return $this->getComponentText('{#sorry_but_you_cant_edit_the_length_of_your_booking#}',[
                'style' => 'nexudus_uikit_formheader'
            ]);
        }

        $lengths = $this->getData('lengths', 'string');

        if(isset($this->booking_data['length'])){
            $length = $this->booking_data['length'];
        } else {
            $length = $this->model->getSubmittedVariableByName('length');
        }

        return $this->getComponentFormFieldSelectorList($lengths,[
            'variable' => 'length',
            'value' => $length,
            'update_on_entry' => 1
        ],[
            'color' => '#ffffff',
            'background-color' => '#3A373A'
        ]);
    }

    private function getLength()
    {
        return $this->components->uiKitExpandingField([
            'divider' => true,
            'icon' => 'icon-nexudus-hourglass.png',
            'variable' => 'length',
            'value_color' => '#ffffff',
            'title' => '{#length#}',
            'expanding_content' => $this->getLengthSelector()
        ]);
    }


}
