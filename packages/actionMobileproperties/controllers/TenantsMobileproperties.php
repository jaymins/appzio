<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.*.models.*');

class TenantsMobileproperties extends MobilepropertiesController
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
        $this->rewriteActionConfigField('background_color', '#f6f6f6');
        $this->rewriteActionField('subject', 'Matching Tenants');
        $this->getHeader();

        $this->listTenants();

        return $this->data;
    }

    public function listTenants()
    {
        $settings = MobilepropertiesModel::getPropertiesBoundaries($this->playid, $this->gid);
        $matches = MobilepropertiesSettingModel::matchInterestedTenants($settings, $this->playid, $this->gid);

        if (empty($matches)) {
            $this->data->scroll[] = $this->getText('{#no_tenants_match_your_property#}', array('style' => 'rentals-info-message-text'));
            $this->data->scroll[] = $this->getText('{#please_check_later#}', array('style' => 'rentals-info-message-text'));
        }

        foreach ($matches as $match) {
            $vars = Aeplayvariable::getArrayOfPlayvariables($match->play_id);

            if (empty($vars) || $vars['role'] != 'tenant') {
                continue;
            }

            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->action_config = $this->requireConfigParam('detail_view');
            $onclick->id = $match->play_id;
            $onclick->sync_open = 1;
            $onclick->back_button = 1;

            $row = array();

            $picture = 'image-placeholder.png';

            if (isset($vars['profilepic']) && $this->getImageFileName($vars['profilepic'])) {
                $picture = $vars['profilepic'];
            }

            $row[] = $this->getImage($picture, array(
                'crop' => 'round',
                'width' => '50',
                'text-align' => 'center',
            ));

            $name = isset($vars['name']) ? $vars['name'] : '{#anonymous#}';

            $row[] = $this->getColumn(array(
                $this->getText($name, array(
                    'style' => 'tenant_list_name'
                )),
            ), array(
                'width' => '50%',
                'padding' => '0 0 0 10',
                'vertical-align' => 'middle',
            ));

            if ( $match->filter_price_per_week == 'price_per_week' ) {
	            $price_value = ($match->to_price_per_month * 12) / 52 . ' p/w';
            } else {
	            $price_value = $match->to_price_per_month . ' p/m';
            }

            $row[] = $this->getColumn(array(
                $this->getText('< £' . $price_value, array('style' => 'tenant_list_price')),
                $this->getRow(array(
                    $this->getImage('icon-bed.png', array('width' => '12', 'margin' => '4 0 0 0')),
                    $this->getText($match->to_num_bedrooms, array(
                        'font-size' => '13',
                        'font-weight' => 'bold',
                        'floating' => true,
                        'float' => 'right',
                        'color' => '#bdbdbd'
                    )),
                ), array(
                    'background-color' => '#f1f1f1',
                    'margin' => '2 0 0 0',
                    'padding' => '2 10 2 10',
                    'border-radius' => '10',
                    'vertical-align' => 'middle',
                ))
            ), array(
                'width' => '30%',
                'floating' => true,
                'float' => 'right',
                'text-align' => 'right',
            ));

            $this->data->scroll[] = $this->getRow($row, array(
                'onclick' => $onclick,
                'vertical-align' => 'middle',
                'margin' => '10 10 0 10',
                'padding' => '10 10 10 10',
                'background-color' => '#ffffff',
                'border-radius' => '3',
                'shadow-color' => '#33000000',
                'shadow-radius' => 1,
                'shadow-offset' => '0 1',
            ));
        }
    }

    public function getHeader()
    {
        $settings = MobilepropertiesSettingModel::getSettings($this->playid, $this->gid);

//        if(isset($this->menus['filtering']) AND isset($this->menus['clear_filter'])) {
//            if($settings->play_id){
//                $this->rewriteActionConfigField('menu_id',$this->menus['clear_filter']);
//            } else {
//                $this->rewriteActionConfigField('menu_id',$this->menus['filtering']);
//            }
//
//        }
    }
}