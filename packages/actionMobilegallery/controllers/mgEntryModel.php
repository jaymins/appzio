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


class mgEntryModel extends MobilegalleryController {

    public $mainobj;

    public function getData(){

        $params['actionimage'] = true;
        $params['imgwidth'] = 640;
        $params['imgheight'] = 480;

        $params['width'] = $this->mainobj->screen_width;
        $params['height'] = round($this->mainobj->screen_width*0.75,0);

        $output[] = $this->getImage('image_portrait',$params);

        /* turn bookmark off */
        if($this->menuid == 200){
            $this->moduleBookmarking('remove');
            $this->flushCacheTab(1);
            $this->mainobj->loadVariableContent(true);
        }

        /* turn bookmark on */
        if($this->menuid == 201){
            $this->moduleBookmarking('save',array('updatenotifications' => true,'notification_action' => 'liked'));
            $this->flushCacheTab(1);
            $this->mainobj->loadVariableContent(true);
        }

        $comment = $this->mainobj->getConfigParam('comment');
        $subject = $this->mainobj->getConfigParam('subject');
        $user = $this->mainobj->getConfigParam('user');
        $chatcontent = $this->mainobj->getConfigParam('chat_content');

        $output[] = $this->mainobj->getUserInfoWithBookmark($user,false,true);
        $output[] = $this->getText($subject,array('style' => 'miv_subject'));
        $output[] = $this->getText($comment,array('style' => 'miv_comment'));

        $args = array(
            'context' => 'action',
            'context_key' => $this->mainobj->action_id,
            'hint'=>'Write a comment',
        );

        $chat = $this->moduleChat( $args );

        if ( isset($chat->scroll) ) {
            $output = array_merge($output,$chat->scroll);
        }

        $output[] = $this->getSpacer('15');

        $data = new StdClass();
        $data->scroll = $output;
        $data->footer = $chat->footer;

        return $data;
    }


}