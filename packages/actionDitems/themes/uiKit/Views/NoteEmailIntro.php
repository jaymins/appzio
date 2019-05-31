<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\Views\Intro as BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class NoteEmailIntro extends BootstrapView
{
    public function tab1()
    {
        $this->layout = new \stdClass();

        if ($this->getData('seen', 'bool')) {
            $this->model->saveVariable('open_div', 'email');
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
        $onclick->action_config = $this->model->getActionidByPermaname('viewnote');
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
            $this->getComponentImage('note_email_intro.png', array(), array(
                'width' => $this->screen_width / 2
            ))
        ), array(), array(
            'text-align' => 'center'
        ));

        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'has_sent_visit_email',
            'visibility' => 'hidden'
        ));

        return $this->layout;
    }
}