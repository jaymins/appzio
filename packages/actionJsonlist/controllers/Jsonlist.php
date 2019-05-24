<?php

/*

    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done

*/

class Jsonlist extends ActivationEngineAction {


    public $feedarray = array();

    public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    public function render_component(){
        return $this->render();
    }

    public function render(){
        $this->init();

        $this->output = '';
        /* main content */



        if(isset($this->configdata->msg)){
          //  $this->output .= $this->configdata->msg .'<br>';
        }

        if(isset($this->configdata->feedurl)){
            $data = file_get_contents($this->configdata->feedurl);
            $this->output .= $this->listHtml($data);
        }

       // $this->output .= '<br><br>';
        $this->output .= $this->donebtn;
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
            $this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
            $this->output .= $this->skipBtn();
		}

        return $this->output;
    }

    public function listHtml($data){
        $feed = $this->getFeed($data);



        $gamecolors = @json_decode($this->gamedata->colors);
        $topbar = $gamecolors->top_bar_color;
        $topbar_txt = $gamecolors->top_bar_text_color;
        $bgcolor = $gamecolors->background_color;

        if($topbar_txt == 'FFFFFF'){
            $icon = '/images/backarrow_white.png';
        } else {
            $icon = '/images/backarrow.png';
        }

        $html = '<div onClick="backToList();" class="back" id="back" style="background:#' .$topbar .'!important;color:#' .$topbar_txt .'!important;"><img width="35" style="vertical-align:middle;" src="' .$icon .'" border="0"> {%back%}</div>';


        while($item = each($feed)){
            $item = $item[1];
            $js = 'onClick="showPost(' ."'" .$item['id'] ."'" .');"';

            if(isset($this->configdata->show_title ) AND $this->configdata->show_title == 1){
                $html .= '<div style="background:#' .$topbar .'!important;" class="titlebar" ' .$js .'><span style="color:#' .$topbar_txt .'!important;">';
                if(isset($this->configdata->show_date ) AND $this->configdata->show_date == 1){
                    $html .= $item['pubDate'] .'<br>';
                }
                $html .= $item['title'];
                $html .= '</span></div>';
                $bordercolor = ' style="border-right:#' .$topbar .' 3px solid!important;border-left:#'.$topbar .' 3px solid!important;border-bottom:#'.$topbar .' 3px solid!important;" ';
                $bottom = '<div class="article_spacer" style="background:#' .$bgcolor .'!important;"></div>';
            } else {
                $bordercolor = false;
                $bottom = false;
            }


            if(isset($this->configdata->show_articles_on_list ) AND $this->configdata->show_articles_on_list == 1) {
                $html .= '<div class="articlecontent_listview">' .$item['description'] .'</div>';
            } else {
                $html .= '<img ' .$js .$bordercolor .' src="' .$item['image'] .'" width="100%" class="article_image">';
                $html .= '<div class="articlecontent" id="article_' .$item['id'] .'">' .$item['description'] .'</div>';

            }

            $html .= $bottom;

        }

        return $html;


    }

    public function getFeed($data){
        $feedXml = @simplexml_load_string($data);
        $output = '';
        $count = 0;

        foreach($feedXml->channel->item as $item){
            $title = (string)$item->title;
            $description = (string)$item->description;
            $description = str_replace('<a href', '<a target="_blank" href',$description);
            $id = md5($title);

            preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $description, $matches);

            if(isset($matches[1][0])){
                $image = $matches[1][0];
            } else {
                $image = false;
            }

            $this->feedarray[$count]['title'] = $title;
            $this->feedarray[$count]['image'] = $image;
            $this->feedarray[$count]['description'] = $description;
            $this->feedarray[$count]['link'] = (string)$item->link;
            $this->feedarray[$count]['pubDate'] = (string)$item->pubDate;
            $this->feedarray[$count]['id'] = $id;
            $count++;
        }

        return $this->feedarray;
    }




}

?>

