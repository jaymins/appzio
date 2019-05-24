<?php

namespace packages\actionMitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait ShowReminderDiv
{
    public function getShowReminderDiv($params = array())
    {
        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';

        return $this->getComponentColumn(array(
            $this->uiKitDivHeader('Reminder', array(
                'close_icon' => 'cross-sign.png',
                'div_id' => 'show_reminder'
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
                    'margin' => '0 0 0 10'
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
                'margin' => '0 0 10 20'
            )),
            $this->getComponentText('', array(
                'variable' => 'reminder_message'
            ), array(
                'margin' => '0 0 0 20'
            )),
            $this->getComponentSpacer(30),
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

}