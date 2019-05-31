<?php

namespace packages\actionDitems\themes\uiKit\Components;

use Bootstrap\Components\BootstrapComponent;

trait NextVisitDiv
{
    public function getNextVisitDiv($params = array())
    {
        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';

        return $this->getComponentColumn(array(
            $this->uiKitDivHeader('Add Next Visit', array(
                'close_icon' => 'cross-sign.png',
                'div_id' => 'next_visit'
            )),
            $this->getReminderDivTitle($title),
            $this->getReminderDivSubtitle($subtitle),
            $this->uiKitPaddedHintedCalendar('Date', 'date', time(), array(
                'active_icon' => 'calendar-dev-icon.png',
                'inactive_icon' => 'calendar-dev-icon.png',
            ), array()),
            $this->getComponentSpacer('1', array(), array(
                'background-color' => '#dadada',
                'opacity' => '0.5',
                'margin' => '0 20 0 20'
            )),
            $this->uiKitHintedTime(),
            $this->getComponentSpacer('1', array(
                'id' => 'spacer_1'
            ), array(
                'background-color' => '#dadada',
                'opacity' => '0.5',
                'margin' => '0 20 0 20'
            )),
            $this->getComponentRow(array(
                $this->getComponentText('{#sync_to_my_calendar#}', array(), array(
                    'color' => '#7b7b7b',
                )),
                $this->getComponentFormFieldOnoff(array(
                    'variable' => 'sync_to_calendar'
                ), array(
                    'floating' => 1,
                    'float' => 'right',
                    'margin' => '0 10 0 0'
                ))
            ), array(
                'id' => 'switch_1'
            ), array(
                'height' => '50',
                'margin' => '5 10 0 20',
                'vertical-align' => 'middle'
            )),
            $this->getComponentSpacer('1', array(
                'id' => 'spacer_2'
            ), array(
                'background-color' => '#dadada',
                'opacity' => '0.5',
                'margin' => '0 20 0 20'
            )),
            $this->getComponentRow(array(
                $this->getComponentText('{#send_invite_to_outlook#}', array(), array(
                    'color' => '#7b7b7b',
                )),
                $this->getComponentFormFieldOnoff(array(
                    'variable' => 'send_outlook_invite'
                ), array(
                    'floating' => 1,
                    'float' => 'right',
                    'margin' => '0 10 0 0'
                ))
            ), array(
                'id' => 'switch_2'
            ), array(
                'height' => '50',
                'margin' => '5 10 0 20',
                'vertical-align' => 'middle'
            )),
            $this->uiKitWideButton('{#add_reminder#}', array(
                'onclick' => $this->createNextVisitReminder()
            ))
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

    protected function createNextVisitReminder()
    {
        $onclick[] = $this->getOnclickSubmit('Createvisit/createNextVisitReminder');
        $onclick[] = $this->getOnclickHideDiv('next_visit');
        $onclick[] = $this->getOnclickSubmit('Controller/show/' .$this->model->sessionGet('item_id_' . $this->model->action_id));
        $onclick[] = $this->getOnclickTab(3);

        return $onclick;
    }

    protected function getNextVisitDivTitle($title)
    {
        return $this->getComponentText($title, array(), array(
            'text-align' => 'center',
            'font-weight' => 'bold',
            'color' => '#787e82',
            'font-size' => '16'
        ));
    }

    protected function getNextVisitDivSubtitle($subtitle)
    {
        return $this->getComponentText($subtitle, array(), array(
            'text-align' => 'center',
            'color' => '#797f82',
            'font-size' => '14'
        ));
    }
}