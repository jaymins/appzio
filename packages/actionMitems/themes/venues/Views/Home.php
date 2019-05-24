<?php

namespace packages\actionMitems\themes\venues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\venues\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Home extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $venues = $this->getData('venues', 'mixed');

        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#my_venues#}'),
                'onclick' => $this->getOnclickOpenAction('home',false,['transition' => 'none']),
                'active' => 1
            ),
            array(
                'text' => strtoupper('{#matches#}'),
                'onclick' => $this->getOnclickOpenAction('venuematches', false, array(
                    'transition' => 'none'
                )),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#my_events#}'),
                'onclick' => $this->getOnclickOpenAction('mytodos', false, array(
                    'transition' => 'none',
                )),
                'active' => 0
            )
        ));

        if($venues){

            $this->layout->scroll[] = $this->uiKitFormSectionHeader('{#my_venues#}');

            foreach($venues as $venue){

                $image = $venue->headerimage1 ? $venue->headerimage1 : 'mven_icon_home.png';
                
                $this->layout->scroll[] = $this->components->getVenueList($venue->name,[$venue->address],[
                    'date_icon' => $image,
                        'onclick' => $this->getOnclickOpenAction('showvenue',false,
                            ['sync_open' => 1, 'id' => $venue->id])]
                    );
/*                $row[] = $this->getComponentText($venue->name,['style' => 'mivenue_name']);
                $row[] = $this->getComponentText($venue->address,['style' => 'mivenue_address']);
                $this->layout->scroll[] = $this->getComponentColumn($row,['onclick' => $this->getOnclickOpenAction('showvenue',false,
                    ['sync_open' => 1, 'id' => $venue->id])]);
                $this->layout->scroll[] = $this->getComponentDivider();
                unset($row);*/
            }
        }


        $this->layout->footer[] = $this->uiKitButtonHollow('{#add_a_new_venue#}',[
            'onclick' => $this->getOnclickOpenAction('addvenue',['back_button' => 1])
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(20);

        $this->model->setBackgroundColor('#ffffff');

        return $this->layout;
    }

    public function getButtons(){
        $row[] = $this->getItem('dashboard', 'icon-dashboard2.png', '{#dashboard#}');
        $row[] = $this->getItem('downloads', 'icon-download.png', '{#downloads#}');

        $this->layout->scroll[] = $this->getComponentRow($row,[],['margin' => '0 20 0 20']);
        $this->layout->scroll[] = $this->getComponentSpacer('40');

        unset($col);
        unset($row);

        $row[] = $this->getItem('businesscharacters', 'icon-person.png', '{#business_characters#}');
        $row[] = $this->getItem('videocategories', 'icon-video.png', '{#videos#}');
        $this->layout->scroll[] = $this->getComponentRow($row,[],['margin' => '0 20 0 20']);
        $this->layout->scroll[] = $this->getComponentSpacer(10);

    }

    public function getItem($target,$icon,$title){
        $row[] = $this->getComponentImage($icon,[],['width' => '50%']);
        $row[] = $this->getComponentText($title,[],['text-align' => 'center', 'font-size' => '14','margin' => '5 0 0 0']);
        return $this->getComponentColumn($row,['onclick'=>$this->getOnclickOpenAction($target,false,[
            'back_button' => true
        ])],['width' => '50%','text-align' => 'center']);

    }


}