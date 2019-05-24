<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;

class Statistics extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfitness\Components\Components
     */
    public $components;
    public $theme;
    private $top_margin;
    public $top_part_height;
    public $bottom_buttons_height;
    public $bottom_buttons_position;

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->bottom_buttons_height = round($this->screen_width / 2.406, 0);

        if($this->notch AND $this->transparent_statusbar){
            $this->top_part_height = $this->screen_height - 60 - $this->bottom_buttons_height;
        } else {
            $this->top_part_height = $this->screen_height - 60 - $this->bottom_buttons_height;
        }

        $bottom_buttons_height = round(($this->screen_width - 80 - 80) / 3, 0);
        $this->bottom_buttons_position = $this->top_part_height - $bottom_buttons_height/2;
    }


    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();
        $col[] = $this->getNavi();
        $col[] = $this->getProgress();

        $this->layout->scroll[] = $this->getComponentColumn($col, [
            'overlay' => [
                $this->getBottomButtons(),
                $this->components->themeStatisticsTabButtons(1,$this->bottom_buttons_position)
            ]
        ], [
            'height' => $this->screen_height - 60
        ]);

        return $this->layout;
    }

    /* PRs */
    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();

        $col[] = $this->getNavi();
        $col[] = $this->getProgress();
        $col[] = $this->getPrs();
        $col[] = $this->getBottomButtons(2);

        $this->layout->scroll[] = $this->getComponentColumn($col, [
            'overlay' => [
                $this->components->themeStatisticsTabButtons(2,$this->bottom_buttons_position),
            ]
        ], [
        ]);

        return $this->layout;
    }

    /* stats */
    public function tab3()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();

        $col[] = $this->getNavi();
        $col[] = $this->getProgress();
        $col[] = $this->getProgressStats();
        $col[] = $this->getBottomButtons(3);

        $this->layout->scroll[] = $this->getComponentColumn($col, [
            'overlay' => [
                $this->components->themeStatisticsTabButtons(3,$this->bottom_buttons_position),
            ]
        ], [
        ]);

        return $this->layout;
    }

    /* leaderboard */
    public function tab4()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();

        $col[] = $this->getNavi();
        $col[] = $this->getProgress();
        $col[] = $this->getLeaderBoard();
        $col[] = $this->getBottomButtons(4);

        $this->layout->scroll[] = $this->getComponentColumn($col, [
            'overlay' => [
                $this->components->themeStatisticsTabButtons(4,$this->bottom_buttons_position),
            ]
        ], [
        ]);

        return $this->layout;
    }

    public function getProgress()
    {

        $points = $this->getData('my_points', 'mixed');
        $month_points = $this->getData('month_points', 'mixed');
        $points_text = $this->getData('my_points_text', 'mixed');

        $col[] = $this->getComponentText($points,[],[
            'text-align' => 'center',
            'font-size' => '75',
            'font-ios' => 'OpenSans-Extrabold',
            'font-android' => 'OpenSans-Extrabold',
            'color' => '#ffffff'
        ]);

        $col[] = $this->getComponentText('points',[],[
            'text-align' => 'center',
            'font-size' => '25',
            'font-ios' => 'OpenSans',
            'font-android' => 'OpenSans',
            'color' => '#ffffff'
        ]);

        $layout = new \stdClass();
        $layout->middle = '0';
        $layout->center = '0';

        $overlay[] = $this->getComponentColumn($col,['layout' => $layout]);
        unset($col);

        $value = @round($points / $month_points,2);

        if(!isset($value) OR $value > 1){
            $value = 0;
        }

        $col[] = $this->components->getComponentProgressRing($value,[
            'animation_duration' => '0.8',
            'progress_color' => 'BE0F2E',
            'progress_color2' => 'BE0F2E',
            'track_color' => '80000000',
            'track_height' => '10'
        ],[
            'width' => $this->top_part_height-280,
            'height' => $this->top_part_height-280,
            'margin' => '0 40 0 40']);

        $page[] = $this->getComponentRow($col, [
            'overlay' => $overlay
        ], ['text-align' => 'center']);

        $page[] = $this->getComponentText($points_text, [], [
            'color' => '#ffffff',
            'height' => '76',
            'text-align' => 'center',
            'margin' => '20 40 46 40'
        ]);

        return $this->getComponentColumn($page,[],[
            'height' => $this->top_part_height - 130
        ]);
    }

    public function getPrs()
    {

        if($this->model->getSavedVariable('units') == 'imperial'){
            $unit = 'Lbs';
        } else {
            $unit = 'KGs';
        }

        $page[] = $this->components->themeStatisticsHeader('PRs',$unit,'theme-icon-small-settings.png',[
            'onclick' => $this->getOnclickSubmit('statistics/switchunit/')
        ]);

        $arr = $this->getData('pr', 'array');

        foreach ($arr as $val) {
            $rows[] = $this->components->themeStatisticsPrRow($val);
        }

        $page[] = $this->getComponentColumn($rows);
        return $this->getComponentColumn($page);

    }

    public function getProgressStats()
    {


        $onclick = $this->getOnclickShowDiv('statistics',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout());

        $value = $this->model->sessionGet('statistics_time');

        if($value == 'monthly'){
            $string = '{#monthly#}';
        } else {
            $string = '{#weekly#}';
        }

        $page[] = $this->components->themeStatisticsHeader('{#progress#}', $string,'theme-icon-small-filter.png',
            ['onclick' => $onclick]);

        $data = $this->getData('chart', 'array');

        if(isset($data['names'])){
            $names = $data['names'];
            $sets = $data['sets'];


            $col[] = $this->getComponentBarchart($sets,[
                'names' => $names,
                //'x_names' => $names,
                'no_values' => 1,
                'hide_grid' => 1,
                'no_click' => 1,
                'no_zoom' => 1,
                'stack_sets' => 1
            ],[
                "height"=> "300",
                "margin" => '10 20 10 20',
                "color"=> "AAAAAA"
            ]);
        } else {
            $col[] = $this->getComponentText('{#no_data_yet#}',[],[
                'text-align' => 'center',
                'color' => '#ffffff',
                'margin' => '10 40 10 40']);
        }

        //$col[] = $this->getComponentImage('theme-progress-graph-placeholder.png');
        $page[] = $this->getComponentColumn($col, [], ['background-color' => '#111111']);
        return $this->getComponentColumn($page);

    }

    public function getLeaderBoard()
    {

        $onclick = $this->getOnclickShowDiv('leaderboard_filter',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout());

        $page[] = $this->components->themeStatisticsHeader(
            '{#leaderboard#}',
            '{#group#}',
            'theme-icon-small-filter.png',[
                'onclick' => $onclick
        ]);
        $arr = $this->getData('leaderboard', 'array');

        $play_id = $this->model->playid;

        foreach ($arr as $key=>$val) {
            if(isset($val['real_name']) AND $val['real_name'] AND isset($val['points']) AND $val['points']){
                $ranking = $key+1;
                $current_user = ($play_id == $val['play_id'])?true:false;
                $rows[] = $this->components->themeStatisticsLeaderboardRow($val['real_name'], $val['points'].'pt', $ranking, $current_user);
            }
        }

        if(!isset($rows)) {
            $rows[] = $this->components->themeStatisticsLeaderboardRow('{#no_entries#}', '' . '', 'N/A');
        }

        $page[] = $this->getComponentColumn($rows);
        return $this->getComponentColumn($page);
        
    }


    public function getNavi()
    {

        $month = $this->getData('month', 'string');

        $width = $this->screen_width - 60;

        $left[] = $this->getComponentImage('theme-symbol-arrow-back.png', [
            'priority' => '1'
        ], ['width' => '10']);

        $col[] = $this->getComponentColumn($left, [
            'onclick' => $this->getOnclickSubmit('statistics/changemonth/previous')
        ], ['padding' => '30 0 30 20']);

        $col[] = $this->getComponentText($month, [], [
            'width' => $width,
            'parent_style' => 'theme_progress_month_text'
        ]);

        $right[] = $this->getComponentImage('theme-symbol-arrow-forward.png', [
            'priority' => '1'
        ], ['width' => '10']);

        $col[] = $this->getComponentColumn($right, [
            'onclick' => $this->getOnclickSubmit('statistics/changemonth/next')
        ], ['padding' => '30 20 30 0']);

        return $this->getComponentRow($col, [], ['margin' => '0 0 0 0', 'vertical-align' => 'middle']);
    }

    /*        $this->layout->scroll_config = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();
        $this->layout->scrollcontent_config->overlay[] = $this->getComponentColumn($container,[
            "on_rearrange" => $this->getOnclickSubmit('rearrange'),
            'draggable_content' => $calendar_rows,
            'row_count' => count($container),
            'draggable_offset' => '0 0 0 80'],[
            'width' => $this->screen_width - 80,'margin' => '0 0 0 80'
        ]);*/


    public function getBottomButtons($tab = 1)
    {
        $top = round($this->screen_width * 1.38768, 0);
        $month = $this->getData('month_value', 'num');

        $col[] = $this->getComponentImage('theme-progress-survey-background2.png', [
            'priority' => '1',
            'onclick' => $this->getOnclickOpenAction('quizz', false, ['back_button' => 1])
        ], ['width' => $this->screen_width / 2]);
        $col[] = $this->getComponentImage('theme-progress-photos-background.png', [
            'priority' => '1',
            'onclick' => $this->getOnclickOpenAction('gallery', false, ['id' => $month, 'sync_open' => 1, 'back_button' => 1])
        ], ['width' => $this->screen_width / 2]);

        $width = $this->screen_width / 2;
        $btn[] = $this->getComponentText('{#complete_a_survey#}', [
            'onclick' => $this->getOnclickOpenAction('quizz', false, ['back_button' => 1])

        ], ['width' => $width, 'padding' => '0 40 0 40', 'text-align' => 'center', 'color' => '#ffffff']);
        $btn[] = $this->getComponentText('{#progress_photos#}', [
            'onclick' => $this->getOnclickOpenAction('gallery', false, ['id' => $month, 'sync_open' => 1, 'back_button' => 1])

        ], ['width' => $width, 'padding' => '0 40 0 40', 'text-align' => 'center', 'color' => '#ffffff']);

        $layout = new \stdClass();
        $layout->top = round($width / 3.2, 0);
        $layout->width = $this->screen_width;
        $overlay[] = $this->getComponentRow($btn, ['layout' => $layout]);

        $layout = new \stdClass();
        $layout->bottom = 0;
        $layout->width = $this->screen_width;

        if($tab == 1){
            $layout = new \stdClass();
            $layout->bottom = 0;
            $layout->width = $this->screen_width;

            return $this->getComponentRow($col, [
                'overlay' => $overlay,'layout' => $layout
            ], []);
        } else {
            return $this->getComponentRow($col, [
                'overlay' => $overlay
            ], []);
        }

    }


    public function getDivs()
    {

        $filters = $this->getData('leaderboard_filters','array');
        $values = $this->model->sessionGet('leaderboard_filtering');

        $divs['leaderboard_filter'] = $this->components->themeFullScreenDiv([
            'title' => '{#filter#}',
            'div_name' => 'leaderboard_filter',
            'content' => $this->components->themeMultiselectList([
                'variable' => 'leaderboard_filter',
                'values' => $values,
                'controller' => 'statistics/leaderboardfiltering/',
                'list' => $filters,
                'use_keys' => true,
                'controller_clear_filters' => 'statistics/leaderboardfilterclear/',
                'controller_refresh' => true,
            ], [
                'background-color' => '#2e3237',
            ])
        ]);

        $values = [
           [
               'text' => 'Weekly',
               'value' => 'weekly',
               ''
           ],
           [
               'text' => 'Monthly',
               'value' => 'monthly'
           ]
        ];
        
        $value = $this->model->sessionGet('statistics_time');

        $divs['statistics'] = $this->components->themeFullScreenDiv([
            'title' => '{#chart_values#}',
            'div_name' => 'statistics',
            'content' => [$this->components->themeRadioButtonsList($values,'statistics_time',$value)
        ,
            $this->components->themeButton('{#save#}',
                [
                    $this->getOnclickHideDiv('statistics'),
                    $this->getOnclickSubmit('statistics/setchart/')
                ],
                'download.png')]]);

        return $divs;
    }

}