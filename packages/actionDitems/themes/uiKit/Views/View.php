<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\themes\uiKit\Views\Create as BootstrapView;

class View extends BootstrapView
{

    private $item;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->item = $this->getData('item', 'object');

        $this->model->rewriteActionField('subject', $this->item->name);

        $this->setVisitHeader();
        $this->setHeader(1);
        $this->renderItemImages(json_decode($this->item->images));
        $this->renderDivider();
        $this->renderVisitDate($this->item->date_added);
        $this->renderDivider();
        $this->renderNameField($this->item->name);
        $this->renderDivider();
        $this->renderDivider();
        $this->renderItemTags($this->item->tags);

        $this->renderEditButton(1);

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $this->model->setBackgroundColor('#ffffff');
        $item_data = $this->getData('item', 'object');

        if (empty($item_data)) {
            return $this->layout;
        }

        $this->item = $item_data;

        $this->setVisitHeader();

        $tab = $this->model->getSavedVariable('go_to_tab');

        if ($tab) {
            return $this->{'tab' . $tab}();
        }

        $categories = $this->model->getItemCategoryInformation($this->item->id);

        $this->model->rewriteActionField('subject', $this->item->name);

        $this->setHeader(2);
        $this->renderVisitCategories($categories);

        $this->renderEditButton(2);

        return $this->layout;
    }

    public function tab3()
    {
        $this->layout = new \stdClass();

        $this->model->setBackgroundColor('#ffffff');

        $landOnThisTab = $this->model->getSavedVariable('go_to_tab');

        if ($landOnThisTab) {
            $this->model->saveVariable('go_to_tab', null);

            $layout = new \stdClass();
            $layout->top = 80;
            $layout->bottom = 0;
            $layout->left = 0;
            $layout->right = 0;

            $this->layout->onload[] = $this->getOnclickShowDiv('email', array(
                'background' => 'blur',
                'tap_to_close' => 1,
                'transition' => 'from-bottom',
                'layout' => $layout
            ));
        }

        $this->item = $this->getData('item', 'object');
        $this->model->saveVariable('last_stored_visit', $this->item->id);

        $this->model->rewriteActionField('subject', $this->item->name);

        $this->setVisitHeader();
        $this->setHeader(3);
        $this->layout->scroll[] = $this->getComponentFormFieldText($this->item->id, array(
            'visibility' => 'hidden',
            'variable' => 'visit_id',
            'value' => $this->item->id
        ));
        $this->layout->scroll[] = $this->getComponentSpacer(40);
        $this->renderActions();
        $this->renderToDos($this->item->id);

        return $this->layout;
    }

    protected function renderVisitDate($date)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage('calendar-dev-icon.png', array(), array(
                'width' => '25'
            )),
            $this->getComponentText(date('d M Y', $date), array(), array(
                'margin' => '0 0 0 15'
            ))
        ), array(), array(
            'margin' => '10 20 10 20'
        ));
    }

    protected function renderNameField($teamName)
    {
        $this->layout->scroll[] = $this->getComponentText($teamName, array(), array(
            'padding' => '10 20 10 20'
        ));
    }

    protected function renderDescription($description)
    {
        if (empty($description)) {
            $description = '{#no_description_available#}';
        }

        $this->layout->scroll[] = $this->getComponentText($description, array(), array(
            'padding' => '10 20 10 20'
        ));
    }

    protected function renderItemTags($tags)
    {
        if (empty($tags)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_tags_available#}', array(), array(
                'padding' => '10 20 0 20'
            ));

            return true;
        }

        $rows = array_chunk($tags, 2);

        foreach ($rows as $row_items) {
            $itemTags = array();

            foreach ($row_items as $tag) {
                $itemTags[] = $this->components->getItemTag($tag->name, array(
                    'delete' => false
                ));
            }

            $this->layout->scroll[] = $this->getComponentRow($itemTags, array(), array(
                'margin' => '10 20 0 20'
            ));
        }

        return false;
    }

    protected function renderItemImages($images)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getPlaceholderImage(isset($images[0]) ? $images[0] : ''),
            $this->getPlaceholderImage(isset($images[1]) ? $images[1] : ''),
            $this->getPlaceholderImage(isset($images[2]) ? $images[2] : '')
        ), array(), array(
            'text-align' => 'center',
            'padding' => '20 0 20 0'
        ));
    }

    protected function getPlaceholderImage($name)
    {

        $defaultImage = $this->model->getConfigParam('actionimage1');

        if (!$defaultImage) {
            $defaultImage = 'formkit-photo-placeholder.png';
        }

        $params = array(
            'imgwidth' => '300',
            'format' => 'jpg',
            'defaultimage' => $defaultImage,
            'use_variable' => true,
            'debug' => 1,
            // 'tap_to_open' => 1,
            'priority' => 9,
        );

        if ($name) {
            $params['onclick'] = $this->getOnclickOpenAction('imagepreview', false, array(
                'id' => basename($name),
                'back_button' => true,
                'sync_open' => true,
            ));
        }

        return $this->getComponentImage($name, $params, array(
            'width' => '100',
            'height' => '100',
            'imgcrop' => 'yes',
            'crop' => 'yes',
            'border-radius' => '3',
            'margin' => '0 5 0 5'
        ));
    }

    protected function renderVisitCategories($categories)
    {
        $this->layout->scroll[] = $this->components->getVisitCategories($categories);
    }

    protected function setVisitHeader()
    {
        $name = ($this->item->name ? $this->item->name : '');

        $this->layout->header[] = $this->components->uiKitVisitTopbar('arrow-back-white-v2.png', $name, $this->getOnclickGoHome(), array(
            'background-color' => '#fecb2f'
        ));
    }

    protected function setHeader($tab)
    {
        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#general#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $tab === 1 ?? false
            ),
            array(
                'text' => strtoupper('{#notes#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $tab === 2 ?? false
            ),
            array(
                'text' => strtoupper('{#actions#}'),
                'onclick' => $this->getOnclickTab(3),
                'active' => $tab === 3 ?? false,
            )
        ));
    }

    protected function renderToDos($itemId)
    {
        $this->layout->scroll[] = $this->uiKitSectionLabel('TO-DOs');
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'reminder_id',
            'visibility' => 'hidden'
        ));
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

        $visit = ItemModel::model()
            ->with('reminders')
            ->findByPk($itemId);

        if (empty($visit)) {
            return;
        }

        $reminders = $visit->reminders;

        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        foreach ($reminders as $reminder) {
            $text = $reminder->type == 'reminder' ?
                $reminder->name : 'Next Visit';

            $icon = 'reminder_action-v2.png';

            $div = $reminder->type == 'reminder' ?
                'show_reminder' : 'show_next_visit';

            $this->layout->scroll[] = $this->getComponentRow(array(
                $this->getComponentImage($icon, array(), array(
                    'vertical-align' => 'middle',
                    'width' => '25'
                )),
                $this->getComponentText($text, array(), array(
                    'vertical-align' => 'middle',
                    'margin' => '0 0 0 10'
                )),
                $this->getComponentRow(array(
                    $this->getComponentText(date('d M Y', $reminder->date), array(), array(
                        'vertical-align' => 'middle',
                        'margin' => '0 10 0 0'
                    )),
                    $this->getComponentImage('icons8-cancel.png', array(
                        'onclick' => array(
                            $this->getOnclickSubmit('Createvisit/deleteReminder/' . $reminder->id),
                            $this->getOnclickHideElement('reminder_' . $reminder->id)
                        )
                    ), array(
                        'width' => '15',

                    ))
                ), array(), array(
                    'floating' => 1,
                    'float' => 'right'
                ))
            ), array(
                'id' => 'reminder_' . $reminder->id,
                'onclick' => array(
                    $this->getOnclickShowDiv($div, array(
                        'background' => 'blur',
                        'tap_to_close' => 1,
                        'transition' => 'from-bottom',
                        'layout' => $layout
                    )),
                    $this->getOnclickSetVariables(array(
                        'reminder_id' => $reminder->id,
                        'show_visit_date' => date('d M Y', $reminder->date),
                        'show_visit_time' => date('H:i', $reminder->date),
                        'reminder_name' => $reminder->name,
                        'reminder_message' => $reminder->message
                    ))
                )
            ), array(
                'margin' => '10 20 0 20',
                'vertical-align' => 'middle'
            ));
        }
    }

    public function getDivs()
    {
        $visit = $this->model->getItem($this->model->getSavedVariable('last_stored_visit'));

        $divs['email'] = $this->components->uiKitEmailWithInputDiv(array(
            'images' => json_decode($visit->images),
            'subtitle' => 'Select images to attach'
        ));

        $divs['set_reminder'] = $this->components->getReminderDiv(array(
            'title' => 'Add Reminder to your visit',
            'subtitle' => ''
        ));

        $divs['next_visit'] = $this->components->getNextVisitDiv(array(
            'title' => 'Schedule your next visit',
            'subtitle' => ''
        ));

        $divs['show_next_visit'] = $this->components->getShowNextVisitDiv(array(
            'title' => 'Next visit information',
            'subtitle' => ''
        ));

        $divs['show_reminder'] = $this->components->getShowReminderDiv(array(
            'title' => 'Follow up Reminder',
            'subtitle' => ''
        ));

        return $divs;
    }

    private function renderEditButton($tab)
    {
        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'icon-edit-item.png',
                'onclick' => $this->getOnclickOpenAction('editvisit', false, [
                    'id' => 'edit-visit-' . $this->item->id,
                    'tab_id' => $tab,
                    'disable_cache' => 1,
                    'sync_open' => 1,
                    'back_button' => 1,
                ])
            ],
        ], true);
    }

}