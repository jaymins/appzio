<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusNextBookingBubble {


    public function getNexudusNextBookingBubble($data=[]){
        /** @var BootstrapComponent $this */

        $date = $this->addParam('date',$data,false);
        $id = $this->addParam('id',$data,false);
        $time = $this->addParam('time',$data,false);
        $location = $this->addParam('resourceName',$data,false);
        $time = $date .' ' .$time;

        $text[] = $this->getComponentText('{#your_next_booking_is_at#} ',['style' => 'nexudus_regular_text']);
        $text[] = $this->getComponentText($time,['style' => 'nexudus_regular_text_bold']);
        $text[] = $this->getComponentText(' at ',['style' => 'nexudus_regular_text']);
        $text[] = $this->getComponentText($location,['style' => 'nexudus_regular_text_bold']);

        $row[] = $this->getComponentRichText($text,[],['height' => '40','vertical-align' => 'top']);
        $row[] = $this->getComponentRow([
                $this->getComponentText('{#manage_booking#}',['style' => 'nexudus_regular_text']),
                $this->getComponentImage('icon-nexudus-forward.png',[],['width' => '20'])
        ],[
            'onclick' => $this->getOnclickOpenAction('viewbooking',false,[
                'id' => $id,'sync_open' => 1
            ])
        ],['vertical-align' => 'middle','text-align' => 'right','margin' => '0 0 0 0']);

        $content = $this->getComponentColumn($row,[],['margin' => '5 0 0 0','width' => 'auto']);
        return $this->getNexudusBlueBubble($content);
    }

}
