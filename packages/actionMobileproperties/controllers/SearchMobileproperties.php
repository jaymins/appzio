<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class SearchMobileproperties extends MobilepropertiesController
{
    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;

    public function tab1()
    {
        $this->initModel();
        $this->data = new stdClass();

        if($this->getConfigParam('complete_action')){
            $onclick = $this->getOnclick('complete-action');
            $this->saveSearchParameters();
            $this->data->scroll[] = $onclick;
        } else {
            $onclick = $this->getOnclick('open-action',false,$this->getConfigParam('main_action'));
            $this->saveSearchParameters();

        }

        return $this->data;
    }



    public function showSettingsForm()
    {
        $settings = MobilepropertiesSettingModel::getSettings($this->playid, $this->gid);

        $this->data->scroll[] = $this->getText('{#choose_district#}', array('style' => 'mobileproperty_fieldlist_label'));
        $this->data->scroll[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield', 'hint' => 'District', 'variable' => 'district'));

        $this->data->scroll[] = $this->getText('{#min_bedrooms#}', array('style' => 'mobileproperty_fieldlist_label'));
        $this->data->scroll[] = $this->getRangeslider($settings->from_num_bedrooms, array('style' => 'mobileproperty_rangeslider', 'min_value' => 1, 'max_value' => 5, 'step' => 1, 'variable' => 'from_num_bedrooms', 'value' => $settings->from_num_bedrooms));

        $this->data->scroll[] = $this->getText('{#max_bedrooms#}', array('style' => 'mobileproperty_fieldlist_label'));
        $this->data->scroll[] = $this->getRangeslider($settings->to_num_bedrooms, array('style' => 'mobileproperty_rangeslider', 'min_value' => 1, 'max_value' => 5, 'step' => 1, 'variable' => 'to_num_bedrooms', 'value' => $settings->to_num_bedrooms));

        $this->data->scroll[] = $this->getText('{#min_price#}', array('style' => 'mobileproperty_fieldlist_label'));
        $this->data->scroll[] = $this->getRangeslider($settings->from_price_per_month, array('style' => 'mobileproperty_rangeslider', 'min_value' => 100, 'max_value' => 2000, 'step' => 100, 'variable' => 'from_price_per_month', 'value' => $settings->from_price_per_month));

        $this->data->scroll[] = $this->getText('{#max_price#}', array('style' => 'mobileproperty_fieldlist_label'));
        $this->data->scroll[] = $this->getRangeslider($settings->to_price_per_month, array('style' => 'mobileproperty_rangeslider', 'min_value' => 100, 'max_value' => 2000, 'step' => 100, 'variable' => 'to_price_per_month', 'value' => $settings->to_price_per_month));

        $this->data->footer[] = $this->getTextbutton('{#save#}', array('id' => 'save-settings', 'onclick' => $this->getOnclick('id', true, 'save-settings')));
    }

    private function saveSearchParameters(){

    }
}
