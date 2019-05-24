<?php

namespace Bootstrap\Components\AppzioUiKit\Listing;

trait uiKitTagList {

    /**
     * View component to render list of tags. Will wrap to multiple lines if needed.
     * @param array $items
     * Array of items to be displayed. Array should 'title' and if onclick is used, also 'id'
     * @param array
     * [background]
     * Background color for the entire block. Default is white.
     *
     * [onclick_edit]
     * Submit onclick command, appends the id at the end. For example, Controller/showtag/{id}
     *
     * [clickparams_edit]
     * Additional parameters for the onclick command for edit / view / whatever
     *
     * [onclick_delete]
     * Show a delete icon and call this when clicked. appends the id at the end. For example, Controller/removetag/{id}
     *
     * [onclick_delete_params]
     * Additional parameters for the onclick command for delete
     *
     * @return mixed
     */

    public function uiKitTagList(array $items, array $params=array()){

        $background = isset($params['background']) ? $params['background'] : '#ffffff';
        $onclick_edit = isset($params['onclick_edit']) ? $params['onclick_edit'] : '';
        $clickparams_edit = isset($params['clickparams_edit']) ? $params['clickparams_edit'] : array();
        $onclick_delete = isset($params['onclick_delete']) ? $params['onclick_delete'] : '';
        $onclick_delete_params = isset($params['onclick_delete_params']) ? $params['onclick_delete_params'] : array();
        unset($params);

        $onclick_delete_params['viewport'] = 'current';

        if(empty($items)){
            return $this->getComponentText('',array(),array('height' => 1,'background-color' => '#ffffff'));
        }

        $width = $this->screen_width - 30;
        $available_width = $width;
        $tags = array();

        foreach ($items as $tag) {
            $font = getcwd() .'/documents/fonts/OpenSans-Regular.ttf';
            if($onclick_edit AND isset($tag['id'])){
                $onclick = $this->getOnclickSubmit('onclick'.$tag['id'],$clickparams_edit);
                $params['onclick'] = $onclick;
            }

            // 17 are the margins and padding
            $size = imagettfbbox(10,0,$font,$tag['title']);
            $size = $size[4] + 17;

            if($onclick_delete){
                $params['style'] = 'item_tag_with_delete';
                $size = $size + 31;
                $onclick_delete = $this->getOnclickSubmit($onclick_delete.'delete_tag_'.$tag['title'],$onclick_delete_params);
            } else {
                $params['style'] = 'item_tag';
            }

            $available_width = $available_width-$size;

            if($available_width < 0){
                $row[] = $this->getComponentRow($tags,array(),array('margin' => '0 0 8 0'));
                unset($tags);
                $available_width = $width;
                $tags[] = $this->getComponentText(($tag['title']), $params);
                if($onclick_delete) {
                    $tags[] = $this->getComponentImage('uikit-delete-icongrey.png', array('style' => 'item_tag_delete','onclick' => $onclick_delete));
                }
            } else {
                $tags[] = $this->getComponentText(($tag['title']), $params);
                if($onclick_delete) {
                    $tags[] = $this->getComponentImage('uikit-delete-icongrey.png', array('style' => 'item_tag_delete','onclick' => $onclick_delete));
                }
            }
        }

        if(isset($tags)){
            $row[] = $this->getComponentRow($tags);
        }

        if(isset($row)){
            $output[] = $this->getComponentColumn($row, array(), array(
                'padding' => '15 15 15 15',
                'background-color' => $background
            ));

            $output[] = $this->uiKitDivider();
        } else {
            $output[] = $this->getComponentText('',array(),array('height' => '1'));
        }

        return $this->getComponentColumn($output);

    }



}