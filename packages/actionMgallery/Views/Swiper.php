<?php

namespace packages\actionMgallery\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMgallery\Components\Components;
use packages\actionMgallery\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Swiper extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->components->themeSmallHeader('{#progress_photos#}',[
            //'right_text' => '1/3'
        ]);
        $this->setImages();
        return $this->layout;
    }

    public function setImages(){
        $data = $this->getData('images', 'array');
        $item = $this->getData('item', 'num');

        if(!$data){
            return false;
        }

        if($item){
            $swipe_params['initial_swipe_id'] = $item;
        }

        $col[] = $this->getComponentText('1/4',['variable' => 'page'],[
            'font-size' => '32',
            'font-ios' => 'OpenSans-ExtraBold',
            'font-android' => 'OpenSans-ExtraBold',
            ]);

        $this->layout->scroll[] = $this->getComponentColumn($col, [], [
            'padding' => '15 20 25 20','background-color' => '#ffffff'
        ]);

        $swipe_params['item_scale'] = 1;
        $swipe_params['transition'] = 'swipe';
        $swipe_params['dynamic'] = 1;
        $swipe_params['id'] = 'imageswiper';
        $swipe_params['swipe_id'] = 'imageswiper';

        if($data){
            $count = 1;

            foreach($data as $image){
                $vars = new \stdClass();
                $vars->date = date('j F Y',strtotime($image->created));
                $vars->page = $count .' / '.count($data);

                $page[] = $this->getComponentImage($image->image,[
                    'swipe_id' => $image->id,
                    'set_variables_data' => $vars
                ],[
                    'margin' => '0 10 0 10',
                    'width' => $this->screen_width-20,
                    'height' => $this->screen_height-240,
                    'crop' => 'yes'
                    ]);

                $count++;
            }
        }

        if(!isset($page)){
            return false;
        }

        $this->layout->scroll[] = $this->getComponentSwipe($page,$swipe_params,[
            'width' => $this->screen_width,
            'height' => $this->screen_height - 240,
            'margin' => '-10 0 0 0',
        ]);

        $left[] = $this->getComponentImage('theme-icon-share-onwhite.png',[
            'onclick' => $this->getOnclickSubmit('edit/share/',['send_ids' => 1])
        ],['width' => '30']);
        $btns[] = $this->getComponentRow($left,[],['width' => '60']);

        $btns[] = $this->getComponentText('4 June 2018',['variable' => 'date','uppercase' => true],['color' => '#9B9B9B','width' => 'auto','text-align' => 'center']);

        $right[] = $this->getComponentImage('theme-icon-trash-onwhite.png',[
            'onclick' => $this->getOnclickSwipeDelete('imageswiper')
        ],['width' => '17']);
        
        $right[] = $this->getComponentImage('theme-icon-edit-onwhite.png',[
            'onclick' => $this->getOnclickSubmit('edit/default/',['send_ids' => 1])
        ],['width' => '17','padding' => '2 0 0 0','margin' => '0 0 0 15']);
        $btns[] = $this->getComponentRow($right,[],['width' => '60','text-align' => 'right']);

        $bottom[] = $this->getComponentRow($btns,[],[
            'vertical-align' => 'middle',
            'background-color' => '#ffffff',
            'height' => '70',
            'text-align' => 'center',
            'width' => $this->screen_width-40,
            'padding' => '20 25 20 25',
            'margin' => '0 20 0 20']);

        $layout = new \stdClass();
        $layout->bottom = 80;
        $layout->center = 0;
        $this->layout->overlay[] = $this->getComponentColumn($bottom,['layout' => $layout]);

        $layout = new \stdClass();
        $layout->bottom = 140;
        $layout->center = 0;
        $navi[] = $this->getComponentSwipeAreaNavigation('#ffffff','#545050');
        $navicol[] = $this->getComponentColumn($navi,[],['background-color' => '#000000','border-radius' => '4','text-align' => 'center','padding' => '0 10 0 10']);
        $this->layout->overlay[] = $this->getComponentColumn($navicol,['layout' => $layout],['height' => '20']);




    }



}
