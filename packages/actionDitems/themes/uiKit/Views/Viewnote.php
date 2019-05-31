<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\themes\uiKit\Views\View as BootstrapView;

class Viewnote extends BootstrapView
{

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');
        $item = $this->getData('item', 'object');

        if (empty($item)) {
            return $this->layout;
        }

        if ($this->model->getSavedVariable('open_div')) {
            $divId = $this->model->getSavedVariable('open_div');
            $this->model->deleteVariable('open_div');

            $layout = new \stdClass();
            $layout->top = 80;
            $layout->bottom = 0;
            $layout->left = 0;
            $layout->right = 0;

            $this->layout->onload[] = $this->getOnclickShowDiv($divId, array(
                'background' => 'blur',
                'tap_to_close' => 1,
                'transition' => 'from-bottom',
                'layout' => $layout
            ));
        }

        $this->model->rewriteActionField('subject', $item->name);

        $this->setNoteHeader($item);
        $this->renderItemImages(json_decode($item->images));
        $this->renderDivider();
        $this->renderVisitDate($item->date_added);
        $this->renderDivider();
        $this->renderNameField($item->name);
        $this->renderDivider();
        $this->renderDescription($item->description);
        $this->renderDivider();
        $this->renderItemTags($item->tags);

        $this->renderNoteReminders($item->reminders);

        $this->model->saveVariable('last_stored_visit', $item->id);

        $this->layout->scroll[] = $this->getComponentFormFieldText($item->id, array(
            'variable' => 'note_id',
            'value' => $item->id,
            'visibility' => 'hidden'
        ), array());

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'icon-edit-item.png',
                'onclick' => $this->getOnclickOpenAction('editnote', false, [
                    'id' => 'edit-note-' . $item->id,
                    'disable_cache' => 1,
                    'back_button' => 1,
                    'sync_open' => 1,
                ])
            ],
            [
                'icon' => 'icon-send-item.png',
                'onclick' => $this->getClickSendNote()
            ]
        ], true);

        return $this->layout;
    }

    public function tab2()
    {
        return new \stdClass();
    }

    public function tab3()
    {
        return new \stdClass();
    }

    protected function setNoteHeader($item)
    {
        $this->layout->header[] = $this->components->uiKitVisitTopbar('arrow-back-white-v2.png', $item->name, $this->getOnclickGoHome(), array(
            'background-color' => '#fecb2f'
        ));
    }

    protected function renderNoteReminders($reminders)
    {
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'show_visit_date',
            'visibility' => 'hidden'
        ));
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'show_visit_time',
            'visibility' => 'hidden'
        ));
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'reminder_name',
            'visibility' => 'hidden'
        ));

        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#reminders#}'), array(), array(
                'font-size' => '14',
                'color' => '#2c2b2b',
            )),
            $this->getComponentText(strtoupper('{#add_reminder#}'), array(
                'onclick' => $this->getOnclickShowDiv('set_reminder', array(
                    'background' => 'blur',
                    'tap_to_close' => 1,
                    'transition' => 'from-bottom',
                    'layout' => $layout
                ))
            ), array(
                'font-weight' => 'bold',
                'color' => '#FFCC00',
                'floating' => '1',
                'float' => 'right',
                'font-size' => '14',
                'margin' => '0 20 0 0'
            ))
        ), array(), array(
            'background-color' => '#f9f9f9',
            'padding' => '10 0 10 20',
            'border-color' => '#e3e3e3',
            'border-width' => '1',
            'margin' => '10 0 0 0'
        ));

        foreach ($reminders as $reminder) {
            $this->layout->scroll[] = $this->getComponentRow(array(
                $this->getComponentText($reminder->name, array(), array(
                    'color' => '#787e82',
                    'font-weight' => 'bold',
                    'font-size' => '12',
                    'width' => '200'
                )),
                $this->getComponentText(date('d-M-Y', $reminder->date), array(), array(
                    'color' => '#787e82',
                    'margin' => '0 30 0 0',
                    'font-size' => '14'
                )),
                $this->getComponentImage('icons8-cancel.png', array(
                    'onclick' => array(
                        $this->getOnclickSubmit('Createvisit/deleteReminder/' . $reminder->id),
                        $this->getOnclickHideElement('note_reminder_' . $reminder->id)
                    )
                ), array(
                    'width' => '15',
                ))
            ), array(
                'id' => 'note_reminder_' . $reminder->id,
                'onclick' => array(
                    $this->getOnclickShowDiv('show_reminder', array(
                        'background' => 'blur',
                        'tap_to_close' => 1,
                        'transition' => 'from-bottom',
                        'layout' => $layout
                    )),
                    $this->getOnclickSetVariables(array(
                        'show_visit_date' => date('d M Y', $reminder->date),
                        'show_visit_time' => date('H:i', $reminder->date),
                        'reminder_name' => $reminder->name,
                        'reminder_message' => $reminder->message
                    ))
                )
            ), array(
                'padding' => '10 20 0 20',
                'vertical-align' => 'middle'
            ));
        }
    }

    public function getDivs()
    {
        $visit = $this->model->getItem($this->model->getSavedVariable('last_stored_visit'));

        $divs['email'] = $this->components->uiKitEmailWithInputDiv(array(
            'images' => json_decode($visit->images),
            'action' => 'Controller/noteEmail',
            'subtitle' => 'Select images to attach'
        ));

        $divs['set_reminder'] = $this->components->getReminderDiv(array(
            'title' => 'Add Reminder',
            'subtitle' => ''
        ));

        $divs['next_visit'] = $this->components->getNextVisitDiv(array(
            'title' => 'Schedule your next visit',
            'subtitle' => 'Instructions if needed go here'
        ));

        $divs['show_next_visit'] = $this->components->getShowNextVisitDiv(array(
            'title' => 'Next visit information',
            'subtitle' => 'Instructions if needed go here'
        ));

        $divs['show_reminder'] = $this->components->getShowReminderDiv(array(
            'title' => 'Reminder',
            'subtitle' => ''
        ));

        return $divs;
    }

    protected function getClickSendNote()
    {
        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        $emailNoteButton = $this->getOnclickShowDiv('email', array(
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        ));

        $emailIntro = new \stdClass();
        $emailIntro->action = 'open-action';
        $emailIntro->action_config = $this->model->getActionidByPermaname('noteemailintro');
        $emailIntro->id = 'note_opened';
        $emailIntro->sync_open = true;

        return $this->model->getSavedVariable('note_email_sent') == 1 ?
            $emailNoteButton : $emailIntro;
    }

}