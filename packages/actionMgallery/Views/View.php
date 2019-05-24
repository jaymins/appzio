<?php

namespace packages\actionMgallery\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMgallery\Components\Components;
use packages\actionMgallery\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
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
        $this->layout->header[] = $this->components->themeSmallHeader('{#progress_photos#}');
        $this->setImages();
        $this->addButton();
        return $this->layout;
    }

    public function setImages(){
        $data = $this->getData('images', 'array');

        $imagewidth = $this->screen_width / 2 - 30;

        if(!$data){
            return false;
        }

        if($data){
            $count = 0;
            $layout = new \stdClass();
            $layout->left = 0;
            $layout->top = 0;

            foreach($data as $image){

                $date = strtotime($image->created);
                $day = date('d',$date);
                $month = date('M',$date);
                $onclick = $this->getOnclickOpenAction('galleryswipe',false,[
                    'sync_open' => 1,'id' => $image->id,'back_button' => 1]);

                $col[] = $this->getComponentText($day,[],['color' => '#ffffff','font-size' => '20']);
                $col[] = $this->getComponentText($month,['uppercase' => true],['color' => '#ffffff','font-size' => '11']);

                $overlay[] = $this->getComponentColumn($col,[
                    'layout' => $layout
                ],[
                    'text-align' => 'center',
                    'background-color' => '#0D0D0B',
                    'padding' => '8 8 8 8']);

                if($count == 0){
                    $col1[] = $this->getComponentImage($image->image,[
                        'overlay' => $overlay,
                        'onclick' => $onclick
                    ],['width' => $imagewidth,'margin' => '0 0 20 0']);
                    $count++;
                } else {
                    $col2[] = $this->getComponentImage($image->image,[
                        'overlay' => $overlay,
                        'onclick' => $onclick
                    ],['width' => $imagewidth,'margin' => '0 0 20 0']);
                    $count = 0;
                }

                unset($col);
                unset($overlay);
            }
        }

        if(isset($col1)){
            $output[] = $this->getComponentColumn($col1,[],['width' => '50%','padding' => '0 10 0 0']);
        }

        if(isset($col2)){
            $output[] = $this->getComponentColumn($col2,[],['width' => '50%','padding' => '0 0 0 10']);
        }

        if(isset($output)){
            $this->layout->scroll[] = $this->getComponentRow($output,[],['padding' => '20 20 20 20']);
        }

    }

    public function addButton(){
        $col[] = $this->getComponentImage('theme-icon-button-plus.png',[],['width' => '70']);

        $layout = new \stdClass();
        $layout->bottom = 90;
        $layout->center = 0;

        $onclick[] = $this->getOnclickImageUpload('progress_photo',['sync_open' => 1]);
        $onclick[] = $this->getOnclickSubmit('refresh');
        $onclick[] = $this->getOnclickSubmit('refresh');

        $this->layout->overlay[] = $this->getComponentColumn($col, [
            'layout' => $layout,
            'onclick' => $onclick
        ],[

        ]);

    }

}
