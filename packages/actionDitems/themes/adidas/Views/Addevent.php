<?php

namespace packages\actionDitems\themes\adidas\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Components\Components;
use packages\actionDitems\themes\adidas\Models\Model as ArticleModel;

class Addevent extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1() {
        $this->layout = new \stdClass();
        
        $match_data = $this->getData('match_data', 'mixed');

        $date = date('M d', strtotime($match_data['formatted_date']));
        $match_name = $match_data['localteam_name'] . ' - ' . $match_data['visitorteam_name'];

        $list = $this->getData('venues', 'string');


        if(!$list){

            $this->layout->scroll[] = $this->getComponentText('{#you_first_need_to_add_a_venue#}');

            $this->layout->footer[] = $this->uiKitButtonHollow('{#add_venue#}', array(
                'onclick' => $this->getOnclickOpenAction('addvenue', false)
            ), array('margin' => '15 80 15 80'));

            return $this->layout;
        }

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#choose_the_venue#}');
        $this->layout->scroll[] = $this->getComponentFormFieldSelectorList($list, [
            'variable' => 'place_id'
        ], [
            'width' => '100%',
            'height' => '100',
            'margin' => '0 0 0 0',
            'color' => '#646a6e',
            'font-size' => '17',
        ]);

        $this->layout->scroll[] = $this->uiKitBackgroundHeader($match_name);
        $this->layout->scroll[] = $this->getComponentText($date .' - ' .$match_data['time'], [], [
            'font-size' => '18',
            'padding' => '10 15 10 15',
            'color' => '#646a6e',
        ]);

        $this->layout->scroll[] = $this->getComponentFormFieldTextArea('', array(
            'variable' => 'event_short_description',
            'hint' => '{#short_description#}',
        ), array(
            'padding' => '15 15 15 15'
        ));

        $this->layout->scroll[] = $this->uiKitDivider();

        $this->layout->scroll[] = $this->getComponentFormFieldText('Event - ' . $match_name, array(
            'variable' => 'match_event_name',
            'visibility' => 'hidden',
        ), array(
            'opacity' => '0',
            'height' => '1'
        ));

        $this->renderImageBlock();
        $this->renderErrorMessages();

        $this->layout->footer[] = $this->uiKitButtonHollow('{#create_event#}', array(
            'onclick' => $this->getOnclickRoute('Addevent/create', false),
        ), array('margin' => '15 80 15 80'));

        return $this->layout;
    }

    protected function renderImageBlock() {
        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#upload_promotions#} (optional)');
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getPlaceholderImage('event_pic_1'),
            $this->getPlaceholderImage('event_pic_2'),
            $this->getPlaceholderImage('event_pic_3')
        ), array(), array(
            'text-align' => 'center',
            'padding' => '15 15 15 15'
        ));
    }

    protected function getPlaceholderImage($name) {
        $onclick = new \StdClass();
        $onclick->action = 'upload-image';
        $onclick->max_dimensions = '2000';
        $onclick->variable = $this->model->getVariableId($name);
        $onclick->action_config = $this->model->getVariableId($name);
        $onclick->sync_upload = 1;

        $defaultImage = $this->model->getConfigParam('actionimage1');

        if (!$defaultImage) {
            $defaultImage = 'adidas-upload-placeholder.png';
        }

        return $this->getComponentImage($this->model->getSavedVariable($name), array(
            'imgwidth' => '700',
            'defaultimage' => $defaultImage,
            'onclick' => $onclick,
            'use_variable' => true,
            'variable' => $this->model->getVariableId($name),
            'config' => $this->model->getVariableId($name),
            'debug' => 1,
            'priority' => 9,
            'format' => 'jpg',
            'lossless' => 1,
        ), array(
            'width' => '100',
            'height' => '100',
            'imgcrop' => 'yes',
            'crop' => 'yes',
            'border-radius' => '3',
            'margin' => '0 5 0 5'
        ));
    }

    protected function renderErrorMessages() {

        $errors = $this->getData('errors', 'mixed');

        if ( empty($errors) ) {
            return false;
        }

        foreach ($errors as $error) {
            $this->layout->footer[] = $this->getComponentText($error, [], [
                'text-align' => 'center',
                'color' => '#ff0000',
                'font-size' => '14',
                'padding' => '3 15 3 15',
            ]);
        }

        return true;
    }

}