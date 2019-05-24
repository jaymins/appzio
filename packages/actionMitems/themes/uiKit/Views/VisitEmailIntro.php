<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\Views\Intro as BootstrapView;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class VisitEmailIntro extends BootstrapView
{
    public function tab1()
    {
        $this->layout = new \stdClass();

        if ($this->getData('seen', 'bool')) {
            $this->model->saveVariable('go_to_tab', 3);
        }

        $this->layout->scroll[] = $this->getComponentText('Tap on the pictures that you would like to attach to the email', array(), array(
            'color' => '#404040',
            'text-align' => 'center',
            'margin' => '10 10 0 10',
            'font-size' => '14'
        ));
        $this->layout->scroll[] = $this->getComponentText('The top part of the email will be populated with the message that you enter', array(), array(
            'color' => '#404040',
            'text-align' => 'center',
            'margin' => '10 10 0 10',
            'font-size' => '14'
        ));
        $this->layout->scroll[] = $this->getComponentText('The bottom part will include automatically your visit observations', array(), array(
            'color' => '#404040',
            'text-align' => 'center',
            'margin' => '10 10 0 10',
            'font-size' => '14'
        ));

        $onclick = new \stdClass();
        $onclick->id = $this->model->getSavedVariable('last_stored_visit');
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('viewvisit');
        $onclick->sync_open = true;

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText('Continue', array(
                'onclick' => $onclick,
                'style' => 'add_item_button'
            ))
        ), array(), array(
            'text-align' => 'center'
        ));

        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentImage('visit_email_intro.png', array(), array(
                'width' => $this->screen_width / 2
            ))
        ), array(), array(
            'text-align' => 'center'
        ));

        return $this->layout;
    }
}