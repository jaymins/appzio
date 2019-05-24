<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getSearchBox {

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

    public function getSearchBox($id, $back=false,array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        if($back){
            $close = $this->getOnclickOpenAction($back,false,array('sync_open' => 1,'transition' => 'none'));
        } else {
            $close = $this->getOnclickGoHome();
        }

        $col[] = $this->getComponentImage('div-back-icon.png',array('onclick' => $close,'style' => 'fauxheader_back'));
        $row[] = $this->getComponentImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getComponentFormFieldText($this->model->getSubmittedVariableByName('searchterm'),array(
            'style' => 'example_searchbox_text',
            'hint' => '{#search_for_product#}','variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => $id,
            'submit_menu_id' => 'search_'.$id,
            'suggestions_style_row' => 'example_list_row','suggestions_text_style' => 'example_list_text',
            //'submit_on_entry' => '1','activation' => 'initially'
        ));

        $close = $this->getOnclickOpenAction('categories',false,array('sync_open' => 1,'transition' => 'none'));

        $col[] = $this->getComponentVerticalSpacer('10');
        $col[] = $this->getComponentRow($row,array('style' => 'example_searchbox','width' => '70%'));
        $col[] = $this->getComponentImage('close-alert-box.png',array('onclick' => $close),array('margin' => '0 20 0 -35','width'=>'20'));
        //$col[] = $this->getComponentText('{#search#}',array('style' => 'example_searchbtn','onclick' => $this->getOnclickSubmit('search_'.$id)));
        return $this->getComponentRow($col,array(),array('background-color' => "#ffffff",'vertical-align' => 'middle','padding'=> '6 0 6 0'));



    }

}
