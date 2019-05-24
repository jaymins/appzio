<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMcalendar\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMcalendar\Models\Model;


class Daily extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMcalendar\Components\Components
     */
    public $components;
    public $theme;

    /* @var Model */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        if(!$this->model->getSavedVariable('intro_completed')){
            $this->layout->scroll[] = $this->getComponentFullPageLoaderAnimated(['color' => '#ffffff']);
            $this->layout->onload[] = $this->getOnclickOpenAction('appintro');
            return $this->layout;
        }

        //$this->naviHeader();
        $this->setHeader(1);
        $this->setDailyCalendar();


        return $this->layout;
    }

    public function naviHeader(){

        $noswipe = $this->getData('noswipe', 'bool');
        $layout = new \stdClass();
        $layout->top = 5;
        $layout->center = 0;

        if($noswipe){
            $onclick = $this->getOnclickSubmit('yesswipe');
            $txt = 'Enable swipe';

        } else {
            $onclick = $this->getOnclickSubmit('noswipe');
            $txt = 'Disable swipe';

        }

        $this->layout->overlay[] = $this->getComponentText($txt,[
            'layout' => $layout,
            'onclick' => $onclick
        ],[
            'color' => '#ffffff',
            'font-size' => 14
        ]);

    }

    public function setDailyCalendar()
    {

        $calendar = $this->getData('calendar', 'array');
        $active_date = $this->getData('active_date', 'string');
        $noswipe = $this->getData('noswipe', 'bool');

        $hours = $this->model->getHours();

        if($noswipe){
            foreach ($calendar as $key => $day) {
                if($key == $active_date){
                    $page[] = $this->components->themeCalendarDay($day, $hours, [
                        'id' => $key]);
                }
            }

            $this->layout->scroll[] = $this->getComponentColumn($page, [
            ]);

            return true;
        }

        foreach ($calendar as $key => $day) {
            $page[] = $this->components->themeCalendarDay($day, $hours, [
                'id' => $key]);
        }

        if(isset($page)){

            $parameters = [
                'id' => 'dayswipe',
                //'item_scale' => 1,
                'cache_merged_data' => 1,
                'transition' => 'swipe',
                'preserve_position' => 1,
                'dynamic' => 1,
                'merge_data' => 1,
                'item_width' => '100%',
                'remember_position' => 1,
                'world_ending' => 'refill_items',
            ];

            if(!isset($_REQUEST['world_ending'])){
                $parameters['initial_swipe_id'] = $active_date;
            }

            $this->layout->scroll[] = $this->getComponentSwipe($page, $parameters);
        }
    }

    public function getDivs()
    {
        $divs = new \stdClass();

        /* look for traits under the components */
        $divs->add_calendar_item = $this->components->themeCalendarAddDiv([
            'hide_intro' => $this->model->getSavedVariable('intro_addcal_done')
        ]);
        $divs->calendar = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#date#}',
            'div_name' => 'calendar',
            'content' => $this->components->themeCalendarWithLoader([
                'variable' => 'calendar','min_date' => false
            ],['background-color' => '#2e3237','color' => '#ffffff'])
        ]);

        $divs->calendar_weekly = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#date#}',
            'div_name' => 'calendar_weekly',
            'content' => $this->components->themeCalendarWithLoader([
                'variable' => 'calendar_weekly','min_date' => false,
                'controller' => 'weekly/updatecalendarweek/',
            ],['background-color' => '#2e3237','color' => '#ffffff'])
        ]);

        $divs->calendar_schedule = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#date#}',
            'div_name' => 'calendar_schedule',
            'content' => $this->components->themeCalendarWithLoader([
                'variable' => 'calendar_schedule','min_date' => false,
                'controller' => 'schedule/updatecalendarschedule/',
            ],['background-color' => '#2e3237','color' => '#ffffff'])
        ]);

        return $divs;
    }

    public function getDivButtonComponent($content, $action, $style)
    {
        $component [] = $this->getComponentText($content['title'], [], [
            'background' => 'blur',
            'color' => $this->color_top_bar_text_color,
            'width' => '90%',
            'padding' => '15 15 15 15',
            'font-size' => '20'
        ]);
        $component [] = $this->getComponentImage($content['icon'], [], ['width' => '5%']);
        return $this->getComponentRow($component, $action, $style);
    }

    private function getTabs()
    {
        return [
            ['text' => '{#day#}',
                'onclick' => $this->getOnclickOpenAction('daily', false, ['transition' => 'none'])],
            ['text' => '{#week#}',
                'onclick' => $this->getOnclickOpenAction('weekly', false, ['transition' => 'none'])],
            ['text' => '{#schedule#}',
                'onclick' => $this->getOnclickOpenAction('schedule', false, ['transition' => 'none'])]
        ];
    }

    public function setHeader($tab = 1,$static=false)
    {

        $this->setFloater();
        
        $timestamp = $this->getData('active_timestamp', 'num');

        $date_string = strtoupper(date('F j, Y',$timestamp));
        $day_name = date('l',$timestamp);

        $params_date['style'] = 'header_day_date';
        $params_day['style'] = 'theme_general_header_title';

        if(!$static){
            $params_date['variable'] = 'header_day_date';
            $params_day['variable'] = 'header_day_name';
            $col[] = $this->getComponentImage('swiss8-icon-calendar.png', ['priority' => '1'], ['height' => '20', 'opacity' => '0.5']);
            $margin = '0 0 0 10';
        } else {
            $col[] = $this->getComponentImage('swiss8-icon-calendar.png', ['priority' => '1'], ['height' => '20', 'opacity' => '0.5']);
            $margin = '0 0 0 10';
        }

        $col[] = $this->getComponentText($date_string, $params_date, [
            'color' => '#ffffff', 'font-size' => '11', 'opacity' => '0.5', 'margin' => $margin,
            "font-ios" => "OpenSans-Bold",
            "font-android" => "OpenSans-Bold"
        ]);

        if($tab == 1){
            $header[] = $this->getComponentRow($col, [
                'onclick' => $this->getOnclickShowDiv('calendar',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout())
            ], ['margin' => '55 0 0 22', 'vertical-align' => 'middle']);
        } elseif($tab == 2) {
            $header[] = $this->getComponentRow($col, [
                'onclick' => $this->getOnclickShowDiv('calendar_weekly',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout())
            ], ['margin' => '55 0 0 22', 'vertical-align' => 'middle']);
        } else {
            $header[] = $this->getComponentRow($col, [
                'onclick' => $this->getOnclickShowDiv('calendar_schedule',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout())
            ], ['margin' => '55 0 0 22', 'vertical-align' => 'middle']);
        }


        $header[] = $this->getComponentText($day_name, $params_day);
        $header[] = $this->getComponentSpacer(20);

        $this->layout->header[] =
            $this->components->themeHeader([
                'tabs' => $this->getTabs()
            ], [
                'header' => $header,
                'activeTab' => $tab
            ], [
                'background-image' => 'theme-calendar-bg.png',
                'height' => '200'
            ]);
    }

    public function setFloater()
    {
        $layout = new \stdClass();
        $layout->top = 102;
        $layout->right = 20;

        $col[] = $this->getComponentImage('theme-icon-button-plus.png', [
            'priority' => '1',
            'onclick' => $this->getOnclickShowDiv('add_calendar_item',$this->components->themeDivOpenParams('#33000000'),$this->components->themeDivOpenLayout())
        ], [
            'width' => '70'
        ]);

        $this->layout->overlay[] = $this->getComponentColumn($col, ['layout' => $layout]);

    }


}