<?php

namespace packages\actionMitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait ItemTag
{
    public $isLiked;

    public function getItemTag($content = '', $params = array())
    {
        $deleteButton = (isset($params['delete']) AND $params['delete']) ?? true;

        /** @var BootstrapComponent $this */

        $output[] = $this->getComponentText($content, array(
            'style' => 'item_tag_text'
        ));

        if ( $deleteButton ) {
            $output[] = $this->getTagDeleteButton($deleteButton, $content);
        }

        return $this->getComponentRow($output, array(
            'style' => 'item_tag_wrapper'
        ));
    }

    protected function getTagDeleteButton($deleteButton, $content)
    {
        return $this->getComponentImage('cancel-icon-dev.png', array(
            'onclick' => $this->getOnclickSubmit('delete_tag_' . $content),
            'style' => 'item_tag_image_delete'
        ));
    }

}