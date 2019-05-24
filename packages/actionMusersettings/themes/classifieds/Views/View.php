<?php

namespace packages\actionMusersettings\themes\classifieds\Views;
use packages\actionMusersettings\Views\View as BootstrapView;
use packages\actionMusersettings\themes\classifieds\Components\Components;

class View extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
//        $this->setTopShadow();

        $this->model->rewriteActionConfigField('hide_menubar', 1);

        $btn_onclick[] = $this->components->getOnclickSubmit('controller/save/');
        $btn_onclick[] = $this->getOnclickListBranches();

        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'gohome-simple','btn_title'=>'{#save#}','title' => '{#user_settings#}','btn_onclick' => $btn_onclick));

        /* get the data defined by the controller */
        $fieldList = $this->getData('fieldlist','array');

        $collection = [];
        foreach($fieldList as $field){

            $groupName = explode('_', $field);

            if ($groupName[0] == 'mreg' && $groupName[1] != 'collect') {
                if (!isset($collection[$groupName[1]])) {
                    $collection[$groupName[1]] = [];
                }
                $collection[$groupName[1]] = array_merge($collection[$groupName[1]], $this->addField_page1($field));
            }
        }

        foreach ($collection as $title => $items) {
            $this->layout->scroll[] = $this->components->getSummaryBox($title, $items);
        }

        $this->layout->scroll[] = $this->components->getAddressFields(false);

        return $this->layout;
    }


}
