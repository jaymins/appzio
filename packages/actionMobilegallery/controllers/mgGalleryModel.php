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


class mgGalleryModel extends MobilegalleryController {

    public $mainobj;


    public function getData(){

        $output[] = $this->topbar();

        $images = $this->getImages();

        if(empty($images)){
            $output[] = $this->getText( 'No pictures at the moment. Check back soon or submit your own!', array( 'style' => 'recipe_chat_nocomments' ) );
        }

        $gallery_items = $this->moduleGallery( array(
            'images'    => json_encode($images),
            'viewer_id' => $this->mainobj->configobj->gallery_item_id,
            'dir'       => 'images',
            'debug'     => false,
            'open_action' => true,
            'grid_spacing' => true,
            'open_in_popup' => false
        ) );

        foreach ($gallery_items as $item) {
            $output[] = $item;
        }

        if(!isset($output)){
            $output[] = $this->getText( 'No pictures at the moment. Check back soon or submit your own!', array( 'style' => 'recipe_chat_nocomments' ) );
        }

        return $output;

    }

    private function getImages(){
        if(isset($this->mainobj->configobj->source_branch) AND
            isset($this->mainobj->configobj->gallery_item_id)
        ) {

            $source = $this->mainobj->configobj->source_branch;
            $gid = $this->mainobj->gid;

            $addpath = '/documents/games/' . $gid . '/original_images/';

            $actions = Aeaction::model()->findAllByAttributes(array('branch_id' => $source));

            foreach ($actions as $action) {
                $config = json_decode($action['config']);

                if(isset($config->comment) AND $config->comment){
                    $item['comment'] = $config->comment;
                } else {
                    $item['comment'] = $config->subject;
                }

                $item['user'] = $config->user;
                $item['pic'] = $addpath . $config->image_portrait;

                if(isset($config->chat_content)){
                    $item['chat_content'] = $config->chat_content;
                } else {
                    $item['chat_content'] = '';
                }

                $item['action_id'] = $action->id;

                if (isset($config->date)) {
                    $item['date'] = $config->date;
                } else {
                    $item['date'] = date('r');
                }

                if (isset($config->likes)) {
                    $item['like_count'] = $config->likes;
                } else {
                    $item['like_count'] = 2;
                }

                if ($item['pic'] AND $item['comment'] AND $item['user']) {
                    $imagesjson[] = $item;
                }

                unset($item);
            }
        }

        if(isset($imagesjson)){
            return array_reverse($imagesjson);
        } else {
            return array();
        }

    }
    

}