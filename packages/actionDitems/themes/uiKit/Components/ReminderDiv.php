<?php

namespace packages\actionDitems\themes\uiKit\Components;

use Bootstrap\Components\BootstrapComponent;

trait ReminderDiv
{
    public function getReminderDiv($params = array())
    {
        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';

        return $this->getComponentColumn(array(
            $this->uiKitDivHeader('Add Reminder', array(
                'close_icon' => 'cross-sign.png',
                'div_id' => 'set_reminder'
            )),
            $this->getComponentColumn([
                $this->getReminderDivTitle($title),
                $this->getReminderDivSubtitle($subtitle),
                $this->uiKitPaddedHintedCalendar('Date', 'date', time(), array(
                    'active_icon' => 'calendar-dev-icon.png',
                    'inactive_icon' => 'calendar-dev-icon.png',
                ), array()),
                $this->uiKitHintedTime(),
                $this->getComponentSpacer('1', array(), array(
                    'background-color' => '#dadada',
                    'opacity' => '0.5',
                    'margin' => '0 20 0 20'
                )),
                $this->getComponentFormFieldText('', array(
                    'variable' => 'title',
                    'hint' => 'Title',
                    'id' => 'title'
                ), array(
                    'padding' => '0 20 0 20'
                )),
                $this->getComponentSpacer('1', array(), array(
                    'background-color' => '#dadada',
                    'opacity' => '0.5',
                    'margin' => '0 20 0 20'
                )),
                $this->getComponentFormFieldTextArea('', array(
                    'hint' => 'Description',
                    'variable' => 'message',
                    'id' => 'description'
                ), array(
                    'margin' => '8 20 8 20',
                    'height' => '100',
                    'font-style' => 'italic'
                )),
                $this->getComponentSpacer('1', array(
                    'id' => 'switch_1'
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
                    'onclick' => $this->createReminder()
                )),
            ], [
                'scrollable' => 1
            ])
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

    protected function createReminder()
    {
        $onclick[] = $this->getOnclickSubmit('Createvisit/createReminder');
        $onclick[] = $this->getOnclickHideDiv('set_reminder');
        $onclick[] = $this->getOnclickSubmit('Controller/show/' . $this->model->sessionGet('item_id_' . $this->model->action_id));

        if ( $this->model->getSubmittedVariableByName( 'visit_id' ) ) {
            $onclick[] = $this->getOnclickTab(3);
        }

        return $onclick;
    }

    protected function getReminderDivTitle($title)
    {
        return $this->getComponentText($title, array(), array(
            'text-align' => 'center',
            'font-weight' => 'bold',
            'color' => '#787e82',
            'font-size' => '16'
        ));
    }

    protected function getReminderDivSubtitle($subtitle)
    {
        return $this->getComponentText($subtitle, array(), array(
            'text-align' => 'center',
            'color' => '#797f82',
            'font-size' => '14'
        ));
    }
}