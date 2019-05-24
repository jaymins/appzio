<?php

namespace packages\actionMitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait ShowNextVisitDiv
{
    public function getShowNextVisitDiv($params = array())
    {
        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';

        return $this->getComponentColumn(array(
            $this->uiKitDivHeader('Reminder', array(
                'close_icon' => 'cross-sign.png',
                'div_id' => 'show_next_visit'
            )),
            $this->getReminderDivTitle($title),
            $this->getReminderDivSubtitle($subtitle),
            $this->getComponentSpacer(20),
            $this->getComponentRow(array(
                $this->getComponentImage('calendar-dev-icon.png', array(), array(
                    'width' => '30'
                )),
                $this->getComponentText('', array(
                    'variable' => 'show_visit_date'
                ), array(
                    'margin' => '0 0 0 10',
                    'font-size' => '14'
                )),
            ), array(), array(
                'margin' => '0 0 10 20'
            )),
            $this->getComponentRow(array(
                $this->getComponentImage('clock-outline.png', array(), array(
                    'width' => '30'
                )),
                $this->getComponentText('', array(
                    'variable' => 'show_visit_time'
                ), array(
                    'margin' => '0 0 0 10'
                )),
            ), array(), array(
                'margin' => '0 0 0 20'
            )),
            $this->getComponentText('', array(
                'variable' => 'reminder_message'
            ), array(
                'margin' => '0 0 0 10'
            )),
            $this->getComponentSpacer(30),
//            $this->uiKitWideButton('{#remove_reminder#}', array(
//                'onclick' => array(
//                    $this->getOnclickHideDiv('show_next_visit'),
//                    $this->getOnclickSubmit('Create/deleteReminder/' . $this->model->getSubmittedVariableByName('reminder_id')),
//                    $this->getOnclickSubmit($this->model->sessionGet('item_id_' . $this->model->action_id))
//                )
//            ))
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

    protected function getShowNextVisitTitle($title)
    {
        return $this->getComponentText($title, array(), array(
            'text-align' => 'center',
            'font-weight' => 'bold',
            'color' => '#787e82',
            'font-size' => '16'
        ));
    }

    protected function getShowNextVisitSubtitle($subtitle)
    {
        return $this->getComponentText($subtitle, array(), array(
            'text-align' => 'center',
            'color' => '#797f82',
            'font-size' => '14'
        ));
    }
}