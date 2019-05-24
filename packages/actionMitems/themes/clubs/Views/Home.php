<?php

namespace packages\actionMitems\themes\clubs\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\clubs\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Home extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();

        if (!$this->model->getSavedVariable('selected_football_club')) {
            $this->layout->onload[] = $this->getOnclickOpenAction('footballteams');
        }

        $this->model->setBackgroundColor('#ffffff');
        $video = $this->getData('video', 'string');

        $height = $this->screen_width / 1.77;

        if(!$video){
            $video = 'https://appziomedia.blob.core.windows.net/asset-5663bc42-12a2-43ad-babe-b862def2a575/appziodemo.mp4';
        }

        $this->layout->scroll[] = $this->getComponentVideo($video,
            ['showplayer' => 1],['width' => '100%', 'height' => $height]);

        $this->layout->scroll[] = $this->getComponentSpacer('20');
        $this->getButtons();

        return $this->layout;
    }

    public function getButtons(){
        $row[] = $this->getItem('dashboard', 'icon-dashboard2.png', '{#dashboard#}');
        $row[] = $this->getItem('downloads', 'icon-download.png', '{#downloads#}');

        $this->layout->scroll[] = $this->getComponentRow($row,[],['margin' => '0 20 0 20']);
        $this->layout->scroll[] = $this->getComponentSpacer('40');

        unset($col);
        unset($row);

        $row[] = $this->getItem('businesscharacters', 'icon-person.png', '{#business_characters#}');
        $row[] = $this->getItem('videocategories', 'icon-video.png', '{#videos#}');
        $this->layout->scroll[] = $this->getComponentRow($row,[],['margin' => '0 20 0 20']);
        $this->layout->scroll[] = $this->getComponentSpacer(10);

    }

    public function getItem($target,$icon,$title){
        $row[] = $this->getComponentImage($icon,[],['width' => '50%']);
        $row[] = $this->getComponentText($title,[],['text-align' => 'center', 'font-size' => '14','margin' => '5 0 0 0']);
        return $this->getComponentColumn($row,['onclick'=>$this->getOnclickOpenAction($target,false,[
            'back_button' => true
        ])],['width' => '50%','text-align' => 'center']);

    }


}