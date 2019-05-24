<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MobileintroController extends ArticleController {

    public $data;
    public $theme;

    public function tab1(){
        $this->data = new stdClass();
        $this->getIntro();
        //$this->data->footer[] = $this->getTextbutton('{#close#}',array('id' => 'close', 'onclick' => $this->getOnclick('complete-action')));
        return $this->data;
    }

    public function getIntro(){
        $width = $this->screen_width;
        $height = $this->screen_width;

        $count = 1;
        $totalcount = 0;

        while($count < 12){
            $name = 'actionimage' .$count;
            $img = $this->getConfigParam( $name );
            if($img){
                $totalcount++;
            }
            $count++;
        }

        $count = 1;

        while($count < 12){
            $name = 'actionimage' .$count;
            $img = $this->getConfigParam( $name );
            if($img){
                $items[] = $this->getColumn($this->getPage($count,$img,$totalcount),array(
                    'background-size' => 'cover','vertical-align' => 'top'));

            }
            $count++;
        }

        if(isset($items)){
            $this->data->scroll[] = $this->getRow(array(
                $this->getColumn(array(
                    $this->getSwipearea($items),
                ), array( 'margin' => '0 0 0 0' )),
            ), array( 'margin' => '0 0 0 0' ));
        }

    }

    public function getPage($count,$img,$totalcount){
        $col[] = $this->getImage($img);
        $col[] = $this->getSwipeNavi($totalcount,$count,array());
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText($this->getConfigParam('title'.$count),array('text-align' => 'center', 'margin' => '0 40 0 40','font-size' => '28'));
        $col[] = $this->getSpacer(20);
        $size = $this->getConfigParam('font_size') ? $this->getConfigParam('font_size') : 18;
        $col[] = $this->getText($this->getConfigParam('page'.$count),array('text-align' => 'center', 'margin' => '0 40 0 40','font-size' => $size, 'height' => '200'));
        return $col;
    }


}