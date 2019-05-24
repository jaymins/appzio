<?php

/*
    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.
*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionRecipe.models.*');
Yii::import('application.modules.aelogic.packages.actionRecipe.controllers.*');

class RecipeController extends ArticleController {

    public $tabsmenu_images = array(
        '1' => array('recipe-tab-1.png','25%'),
        '2' => array('recipe-tab-2.png','25%'),
        '3' => array('recipe-tab-3.png','25%'),
        '4' => array('recipe-tab-4.png','25%')
    );

    public $galleryobj;
    public $servings = 0;
    public $tabmode = 'top';
    public $debug = false;

    public function tab1(){

        /* turn bookmark off */
        if($this->menuid == 200){
            $this->moduleBookmarking('remove');
            $this->flushCacheTab(1);
        }

        /* turn bookmark on */
        if($this->menuid == 201){
            $this->moduleBookmarking('save',array('updatenotifications' => true));
            $this->flushCacheTab(1);
        }

        $data = new StdClass();

        if(isset($this->tabs[$this->menuid])) {
            $pointer = $this->tabs[$this->menuid];

            switch($pointer){
                case 'cooked_this':
                    $data->scroll = $this->submitImage();
                    $output[] = $this->getMenu( 'general_submit' );
                    $data->footer = $output;
                    break;

                case 'general_submit':
                    $data->scroll = $this->imageConfirmation();
                    break;
            }
        }

        if(!isset($data->scroll)){
            $data->scroll = $this->getMainScroll(array());
        }

        return $data;
    }

    public function tab2(){
        $data = new stdClass();
        $this->servings = $this->getPlayState('servings');

        if($this->menuid > 800 AND $this->menuid < 810){
            $this->servings = $this->menuid - 800;
            $this->savePlayState('servings',$this->servings);
        }

        if($this->menuid == 100){
            $this->servings = Yii::app()->cache->get( $this->actionid .'servings', $this->servings );
            $data->scroll = $this->addToList($this->servings);
        }

        if(!isset($data->scroll)){
            $data->scroll = $this->getIngredientsScroll(array(),$this->servings);
        }

        $data->footer[] = $this->articlemenuobj->getSingleImageMenuItem('100','','add-to-shopping-list.png');

        return $data;
    }

    public function tab3(){
        $data = new stdClass();

        $args['context_key'] = $this->action_id .'-chat';
        $args['context'] = 'action';

/*      $args['strip_urls'] = $this->getConfigParam('strip_urls');
        $args['pic_permission'] = $this->getConfigParam('pic_permission');
        $args['firstname_only'] = $this->getConfigParam('firstname_only');
        $args['hide_time'] = $this->getConfigParam('hide_time');
        $args['limit_monologue'] = $this->getConfigParam('limit_monologue');
        $args['can_invite_others'] = $this->getConfigParam('can_invite_others');*/

        $args['disable_header'] = 1;

        $chat = $this->moduleChat($args);

        $data->scroll = $chat->scroll;
        $data->footer = $chat->footer;
        return $data;
    }

    public function tab4(){
        $data = new StdClass();

        if(isset($this->configobj->user_images)){
            $gallery_items = $this->moduleGallery( array(
                'images'    => $this->configobj->user_images,
                'viewer_id' => $this->configobj->gallery_item_id,
                'dir'       => 'images'
            ) );

            foreach ($gallery_items as $item) {
                $output[] = $item;
            }
        }

        if(!isset($output)){
            $output[] = $this->getText( 'Be the first one to cook this recipe and receive double points! Go to first tab and click "I cooked this".', array( 'style' => 'recipe_chat_nocomments' ) );
        }

        $data->scroll = $output;
        return $data;
    }

    private function givePoints(){

        $point1_name = 'points_' .$this->configobj->points_1;
        $point2_name = 'points_' .$this->configobj->points_2;

        if(!isset($this->vars[$point1_name])){
            Aevariable::addGameVariable($this->gid,$point1_name);
        }

        if(!isset($this->vars[$point2_name])){
            Aevariable::addGameVariable($this->gid,$point2_name);
        }

        if(isset($this->varcontent[$point1_name])){
            $newpoints1 = $this->varcontent[$point1_name] + 1;
            AeplayVariable::updateWithName($this->playid,$point1_name,$newpoints1,$this->gid);
        } else {
            $newpoints1 = 1;
            AeplayVariable::updateWithName($this->playid,$point1_name,1,$this->gid);
        }

        if(isset($this->varcontent[$point2_name])){
            $newpoints2 = $this->varcontent[$point2_name] + 1;
            AeplayVariable::updateWithName($this->playid,$point2_name,$newpoints2,$this->gid);
        } else {
            $newpoints2 = 1;
            AeplayVariable::updateWithName($this->playid,$point2_name,1,$this->gid);
        }

        $arr['points1'] = $newpoints1;
        $arr['points2'] = $newpoints2;

        $this->flushCacheTab(3);
        return $arr;
    }

    private function imageConfirmation(){

        $this->loadVariables();
        $this->loadVariableContent();
        $this->saveVariables();

        $call = 'apps/recipeAsync?actionid=' .$this->actionid .'&playid=' .$this->playid;
        Controller::asyncAppzioApiCall($call,$this->gid);

        $points = $this->givePoints();

        $point1 = ucfirst($this->configobj->points_1) .' points';
        $point2 = ucfirst($this->configobj->points_2) .' points';

        $output[] = $this->getImage( 'thumbs-up2.jpg', array( 'style' => 'recipe_top_images' ) );
        $output[] = $this->getText( 'Your photo has been uploaded. It might take a little while for it to show up in the recipe\'s gallery. Here is your updated point tally:', array( 'style' => 'recipe_text_narrow' ) );
        $output[] = $this->getText( $point1 .':' .$points['points1'] .'pt', array( 'style' => 'recipe_points_title' ) );
        $output[] = $this->getText( $point2 .':' .$points['points2'] .'pt', array( 'style' => 'recipe_points_title' ) );

        return $output;
    }

    private function submitImage(){

        $this->loadVariables();

        $output[] = $this->getImage( 'photo-placeholder.jpg', array( 'style' => 'recipe_top_images', 'variable' => 'upload_temp' ) );
        $output[] = $this->getText( 'Please upload a photo of your cooking creation to collect the points', array( 'style' => 'recipe_text_narrow' ) );
        $output[] = $this->getFieldupload( 'Add an Image', array( 'style' => 'recipe_upload_image', 'variable' => 'upload_temp', 'type' => 'image' ) );
        $output[] = $this->getText( 'Comment (optional):', array( 'style' => 'recipe_text_narrow' ) );
        $output[] = $this->getFieldtextarea( '', array( 'hint' => 'Comment (optional):', 'style' => 'recipe_comment_field_upload', 'variable' => 'comment_temp' ) );

        return $output;
    }

    private function pointsText(){
        $time = $this->getConfigParam('time') .'min';
        $points1 = $this->getConfigParam('points1');
        $points2 = $this->getConfigParam('points2');
        
        if(isset($this->configobj->bookmarks)){
            $likes = $this->configobj->bookmarks;
        } else {
            $likes = 3;
        }

        $output[] = $this->getText( $time, array( 'style' => 'recipe_points_text' ) );
        $output[] = $this->getText( $points1, array( 'style' => 'recipe_points_text' ) );
        $output[] = $this->getText( $points2, array( 'style' => 'recipe_points_text' ) );
        $output[] = $this->getText( $likes, array( 'style' => 'recipe_points_text' ) );

        return $output;
    }

    private function getMainScroll($output,$color='white'){

        $assetname = $this->getConfigParam('image_portrait');
        $output[] = $this->getImage( $assetname, array( 'style' => 'recipe_top_image','width'=> '100%' ));
        $output[] = $this->getUserInfoWithBookmark($this->getConfigParam('user'));
        $output[] = $this->getImage('points-background.png', array(  ));
        $output[] = $this->getRow( $this->pointsText(), array( 'style' => 'single_row' ));
        $output[] = $this->getText( $this->configobj->intro, array( 'style' => 'recipe_intro' ) );

        $count = 1;

        while($count < 10){
            $stepname = 'step' .$count;
            if(isset($this->configobj->$stepname) AND $this->configobj->$stepname){
                $output[] = $this->getImage( 'step' . $count . '.png', array( 'style' => 'chat-divider' ));
                $output[] = $this->getText( $this->configobj->$stepname, array( 'style' => 'recipe_step_text' ) );
                $count++;
            } else {
                break;
            }
        }

        $output[] = $this->getText( '', array( 'style' => 'recipe_step_text' ) );
        $output[] = $this->getMenu( 'cooked' );

        return $output;
    }

    private function getIngredientsMenu($servings){

        $params['width'] = '33%';

        if($servings == 1){
            $columns[] = $this->getImage('minus-off.png',$params);
        } else {
            $columns[] = $this->getImagebutton('minus.png',800+$servings-1,false,$params);
        }

        $columns[] = $this->getImage('i-' .$servings .'.png',$params);

        if($servings == 8){
            $columns[] = $this->getImage('plus-off.png',$params);
        } else {
            $columns[] = $this->getImagebutton('plus.png',$servings+801,false,$params);
        }

        $output[] = $this->getSpacer('15');
        $output[] = $this->getRow($columns,array('width' => '100%'));
        $output[] = $this->getSpacer('15');

        return $output;
    }

    public function getIngredientsScroll($output,$servings = false,$msg=false){

        if(!$servings){
            $serv = $this->getPlayState('servings');

            if($serv){
                $servings = $serv;
            } elseif($this->configobj->servings > 0 AND $this->configobj->servings < 9){
                $servings = $this->configobj->servings;
            } else {
                $servings = 4;
            }
        }

        $output = $this->getIngredientsMenu($servings);

        if($msg){
            $output[] = $this->getText($msg,array('style' => 'recipe_text_narrow'));
        }

        $output[] = $this->getImage('ingredients-title.png',array('style' => 'chat-divider'));

        $count = 1;

        while($count < 30){
            $stepname = 'ingredient' .$count;
            $countname = 'ingredient' .$count .'_count';

            if(isset($this->configobj->$stepname) AND $this->configobj->$stepname){
                if(isset($this->configobj->$countname) AND $this->configobj->$countname){
                    $one_portion = $this->configobj->$countname / $this->configobj->servings;
                    $itemcount =  round($one_portion * $servings,1);
                } else {
                    $itemcount = '';
                }

                $content = $itemcount .' ' .$this->configobj->$stepname;
                $output[] = $this->getText($content,array('style' => 'recipe_ingredients_text'));
                $count++;
            } else {
                break;
            }
        }

        $output[] = $this->getSpacer('15');
        return $output;
    }

    public function addToList($servings){

        $count = 1;

        if(!$servings){
            $serv = $this->getPlayState('servings');

            if($serv) {
                $servings = $serv;
            } elseif($this->configobj->servings > 0 AND $this->configobj->servings < 9){
                $servings = $this->configobj->servings;
            } else {
                $servings = 4;
            }
        }

        if(isset($this->varcontent['shopping_list'])){
            $shoppinglist = json_decode($this->varcontent['shopping_list']);
            if(!is_object($shoppinglist)){
                $shoppinglist = new StdClass();
            }
        } else {
            $shoppinglist = new StdClass();
        }

        while($count < 30){
            $stepname = 'ingredient' .$count;
            $countname = 'ingredient' .$count .'_count';


            if(isset($this->configobj->$stepname) AND $this->configobj->$stepname){
                $ingredient = $this->configobj->$stepname;

                if($this->configobj->$stepname){
                    $obj = new StdClass;
                    $obj->type = 'msg-plain';

                    if(isset($this->configobj->$countname) AND $this->configobj->$countname){
                        $one_portion = $this->configobj->$countname / $this->configobj->servings;
                        $itemcount =  round($one_portion * $servings,1);

                        if(isset($shoppinglist->$ingredient)){
                            $shoppinglist->$ingredient = $this->configobj->$stepname + $itemcount;
                        } else {
                            $shoppinglist->$ingredient = $itemcount;
                        }
                    } else {
                        if(!isset($shoppinglist->$ingredient)){
                            $shoppinglist->$ingredient = 'some ';
                        }
                    }
                }
            }

            $count++;
        }

        AeplayVariable::updateWithName($this->playid,'shopping_list',json_encode($shoppinglist),$this->gid);

        $msg = 'Recipe added to your shopping list';
        return $this->getIngredientsScroll(array(),$servings,$msg);
    }

}