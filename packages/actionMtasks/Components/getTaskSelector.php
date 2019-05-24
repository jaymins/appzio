<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getTaskSelector {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getTaskSelector($id,$title,$description,$icon){
        /** @var BootstrapComponent $this */

        $width = $this->screen_width / 2 - 30;
        $imgwidth = $width - 40;

        $img[] = $this->getComponentImage($icon,array(),array('width' => $imgwidth,
            'background-color' => '#E4E7E9'));

        $col[] = $this->getComponentRow($img,array(),array('parent_style' => 'taskbox','width'=>$width));

        $col[] = $this->getComponentText($title,array('style' => 'task_item_title'));
        $col[] = $this->getComponentText($description,array('style' => 'task_item_description'));

        //$onclick = $this->getOnclickRoute('Controller/phase3/'.$id,true,array('category' => $id,'phase' => 3));

        $categories = array('chores','school','community','other');

        foreach ($categories as $category) {
            if($category == $id){
                $onclick[] = $this->getOnclickHideElement($category.'_inactive',array('transition' => 'none'));
                $onclick[] = $this->getOnclickShowElement($category.'_active',array('transition' => 'none'));
            } else {
                $onclick[] = $this->getOnclickHideElement($category.'_active',array('transition' => 'none'));
                $onclick[] = $this->getOnclickShowElement($category.'_inactive',array('transition' => 'none'));
            }
        }
        
        $onclick[] = $this->getOnclickSubmit('Controller/savecategory/'.$id);

        if($this->model->sessionGet('category') == $id){
            $output[] = $this->getComponentColumn($col,array('onclick'=>$onclick,'id' => $id.'_inactive','visibility' => 'hidden','loader_off' => true),array('width' => $width,'parent_style' => 'task_item'));
            $output[] = $this->getComponentColumn($col,array('onclick'=>$onclick,'id' => $id.'_active'),array('width' => $width,'parent_style' => 'task_item_active','loader_off' => true));
        } else {
            $output[] = $this->getComponentColumn($col,array('onclick'=>$onclick,'id' => $id.'_inactive'),array('width' => $width,'parent_style' => 'task_item','loader_off' => true));
            $output[] = $this->getComponentColumn($col,array('onclick'=>$onclick,'id' => $id.'_active','visibility' => 'hidden','loader_off' => true),array('width' => $width,'parent_style' => 'task_item_active'));
        }

        return $this->getComponentColumn($output);
    }

}
