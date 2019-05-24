<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMcalendar\Views;

use packages\actionMcalendar\Models\Model;


class Weekly extends Daily
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

        //$this->naviHeader();
        $this->setHeader(2, false);
        $hours = $this->model->getHours();
        $days = $this->model->calendarWeeklyTestData();
        $calendar = $this->getData('calendar', 'array');
        $noswipe = $this->getData('noswipe', 'bool');
        $active_date = $this->getData('active_date', 'string');

        $count = 0;

        if($noswipe){
            foreach ($calendar as $week) {
                $date = strtotime($week[0]['week_num']);
                $start = strtotime('Monday', $date);
                $end = strtotime('Sunday', $date);

                if($active_date == $week[0]['week_num']){
                    $vars = new \stdClass();
                    $vars->header_day_date = strtoupper(date('d.M', $start) . ' - ' . date('d.M', $end));
                    $vars->header_day_name = 'Week ' . date('W', $start);

                    $page[] = $this->getComponentColumn([
                        $this->components->themeCalendarWeekHeader($week),
                        $this->components->themeCalendarWeek($hours, $week)
                    ], [
                        'id' => $week[0]['week_num'],
                        'swipe_id' => $week[0]['week_num'],
                        'set_variables_data' => $vars
                    ]);
                }
            }

            $this->layout->scroll[] = $this->getComponentColumn($page, []);
            return $this->layout;
        }


        foreach ($calendar as $week) {
            $date = strtotime($week[0]['week_num']);
            $start = strtotime('Monday', $date);
            $end = strtotime('Sunday', $date);

            $vars = new \stdClass();
            $vars->header_day_date = strtoupper(date('d.M', $start) . ' - ' . date('d.M', $end));
            $vars->header_day_name = 'Week ' . date('W', $start);

            $page[] = $this->getComponentColumn([
                $this->components->themeCalendarWeekHeader($week),
                $this->components->themeCalendarWeek($hours, $week)
            ], [
                'id' => $week[0]['week_num'],
                'swipe_id' => $week[0]['week_num'],
                'set_variables_data' => $vars
            ]);
        }

        if (isset($page)) {

            $this->layout->scroll[] = $this->getComponentSwipe($page, [
                'id' => 'weekswipe',
                //'item_scale' => 1,
                'cache_merged_data' => 1,
                'remember_position' => 1,
                'preserve_position' => 1,
                'dynamic' => 1,
                'merge_data' => 1,
                'initial_swipe_id' => $active_date,
                'item_width' => '100%',
                'transition' => 'swipe',
                'world_ending' => 'refill_items',
            ]);
        }


        return $this->layout;
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
        $component [] = $this->getComponentImage($content['icon'], ['priority' => '1'], ['width' => '5%']);
        return $this->getComponentRow($component, $action, $style);
    }

}