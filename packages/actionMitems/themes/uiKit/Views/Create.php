<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\Views\Create as BootstrapView;

class Create extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    private $menuId;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->setSubject();

        $this->model->setBackgroundColor('#ffffff');

        $this->presetData = $this->getData('presetData', 'array');

        if ($this->getData('saved', 'bool')) {
            return $this->layout;
        }

        $this->setVisitCreateHeader();
        $this->setHeader(1);

        $this->renderVisitImages();
        $this->renderDivider();
        $this->renderTeamNameField();
        $this->renderDivider();
        $this->renderDatePicker();
        $this->renderTags();
        $this->renderDuplicationError();

        $this->renderFloatingButtons();

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $this->setSubject();

        $this->model->setBackgroundColor('#ffffff');
        $this->presetData = $this->getData('presetData', 'array');
        $errors = $this->getData('errors', 'mixed');

        $this->setVisitCreateHeader();
        $this->setHeader(2);

        if (isset($errors['name'])) {
            $this->renderErrorMessage($errors['name']);
        }

        $this->renderCategories();

        $this->renderFloatingButtons();

        return $this->layout;
    }

    protected function setVisitCreateHeader()
    {
        $this->menuId = $this->getController() . '/saveVisit';
        $title = 'ADD VISIT';

        $itemId = $this->getData('itemId', 'mixed');
        if ($itemId) {
            $this->menuId .= '/edit_' . $itemId;
            $title = 'EDIT VISIT';
        }

        $this->layout->header[] = $this->components->uiKitVisitTopbar('arrow-back-white-v2.png', $title, $this->getOnclickGoHome(), array(
            'background-color' => '#fecb2f'
        ));
    }

    protected function renderCategories()
    {
        $categories = $this->model->getItemCategories();
        $this->layout->scroll[] = $this->components->getCategoryAccordion($categories);
    }

    public function formatCategoryDescription(string $description, string $style)
    {
        return array_map(function ($statement) use ($style) {
            return $this->getComponentText('• ' . $statement, array('style' => $style));
        }, explode('|', $description));
    }

    protected function renderDuplicationError()
    {
        $duplicationError = $this->model->validation_errors['duplicate'];

        if ($duplicationError) {
            $this->layout->footer[] = $this->getComponentColumn(array(
                $this->getComponentText('Not saved!', array(), array(
                    'font-weight' => 'bold',
                    'color' => '#FF0000',
                    'margin' => '0 0 10 0'
                )),
                $this->getComponentText($duplicationError, array(), array(
                    'color' => '#FF0000',
                    'text-align' => 'center'
                ))
            ), array(), array(
                'margin' => '0 10 10 10',
                'text-align' => 'center'
            ));
        }
    }

    protected function renderVisitImages()
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getPlaceholderImage('visit_pic_1'),
            $this->getPlaceholderImage('visit_pic_2'),
            $this->getPlaceholderImage('visit_pic_3')
        ), array(), array(
            'text-align' => 'center',
            'padding' => '20 0 20 0'
        ));
    }

    protected function getPlaceholderImage($name)
    {
        $onclick = new \StdClass();
        $onclick->action = 'upload-image';
        $onclick->max_dimensions = '2000';
        $onclick->variable = $this->model->getVariableId($name);
        $onclick->action_config = $this->model->getVariableId($name);
        $onclick->sync_upload = 1;

        $defaultImage = $this->model->getConfigParam('actionimage1');

        if (!$defaultImage) {
            $defaultImage = 'formkit-photo-placeholder.png';
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

    protected function renderImageSwiper()
    {
        $imageIndex = 0;
        $images = array();

        while ($imageIndex <= 10) {

            if ($this->model->getSavedVariable('visit_pic_' . $imageIndex)) {
                $images[] = array(
                    'image' => $this->getVisitImage('visit_pic_' . $imageIndex),
                    'id' => $imageIndex,
                    'variable' => 'visit_pic_' . $imageIndex
                );
                $imageIndex++;
                continue;
            }

            break;
        }

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#attach_photo#}'), array(
                'onclick' => $this->getOnclickImageUpload('visit_pic_' . $imageIndex, array(
                    'sync_upload' => true,
                ))
            ), array(
                'floating' => 1,
                'float' => 'right',
                'color' => '#FFCC00',
                'font-size' => '14',
                'margin' => '10 20 0 0',
                'font-weight' => 'bold'
            ))
        ));

        $this->layout->scroll[] = $this->components->uiKitThreeColumnImageSwiper($images);
    }

    protected function getVisitImage($image)
    {
        if ($this->model->getSavedVariable($image)) {
            $visitImage = $this->model->getSavedVariable($image);
        } else if ($this->model->getSubmittedVariableByName($image)) {
            $visitImage = $this->model->getSubmittedVariableByName($image);
        } else {
            $visitImage = 'icon_camera.png';
        }

        return $visitImage;
    }

    protected function renderDivider()
    {
        $this->layout->scroll[] = $this->getComponentSpacer('1', array(), array(
            'background-color' => '#dadada',
            'opacity' => '0.5'
        ));
    }

    protected function setHeader($tab)
    {
        $params = array('keep_user_data' => 1);

        $itemId = $this->getData('itemId', 'mixed');

        if ($itemId) {
            $params['id'] = 'edit_' . $itemId;
        }

        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#general#}'),
                'onclick' => array(
                    $this->getOnclickSubmit($this->getController() . '/SaveCategories'),
                    $this->getOnclickTab(1, $params)
                ),
                'active' => $tab === 1 ?? false
            ),
            array(
                'text' => strtoupper('{#notes#}'),
                'onclick' => array(
                    $this->getOnclickSubmit($this->getController() . '/SaveData'),
                    $this->getOnclickTab(2, $params)
                ),
                'active' => $tab === 2 ?? false
            ),
            array(
                'text' => strtoupper('{#actions#}'),
                'onclick' => $this->getOnclickTab(3, $params),
                'active' => $tab === 3 ?? false,
                'disabled' => true
            )
        ));
    }

    protected function renderTeamNameField()
    {
        $value = isset($this->presetData['name']) ? $this->presetData['name'] : '';

        $this->layout->scroll[] = $this->getComponentFormFieldText($value, array(
            'variable' => 'name',
            'value' => $value,
            'hint' => '{#team_name#}',
        ), array(
            'padding' => '15 20 15 20'
        ));

        $this->renderValidationErrors('name');
    }

    protected function renderDatePicker()
    {
        $date = $this->model->getSavedVariable('temp_date');

        if (empty($date)) {
            $date = time();
        }

        $this->layout->scroll[] = $this->components->uiKitHintedCalendar('Date', 'date_added', $date, array(
            'active_icon' => 'calendar-dev-icon.png',
            'inactive_icon' => 'calendar-dev-icon.png'
        ), array());

        $this->renderValidationErrors('date_added');
    }

    protected function renderTags()
    {
        $this->layout->scroll[] = $this->uiKitSectionLabel('TAGS');
        
        $value = isset($this->presetData['item_tags']) ? $this->presetData['item_tags'] :
            $this->model->getSubmittedVariableByName('item_tags');

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText($value, array(
                'hint' => '{#type_tag#}',
                'variable' => 'item_tags',
                'id' => 'component_id',
                'suggestions_update_method' => 'gettags',
                'suggestions' => [],
                'suggestions_placeholder' => $this->getComponentText('$value', array(), array(
                    'font-size' => 15,
                    'color' => '#333333',
                    'background-color' => '#ffffff',
                    'padding' => '12 10 12 10',
                )),
            ), array(
                'font-style' => 'italic',
                'color' => '#8C8C8C',
                'padding' => '0 20 0 20',
                'font-size' => '16'
            ))
        ), array(), array(
            'margin' => '5 0 5 0',
        ));

        $this->renderDivider();
        $this->renderItemTagsEdit();
    }

    protected function renderItemTagsEdit()
    {
        $tags = $this->model->sessionGet('tags');

        if (!$tags) {
            return false;
        }

        if (count($tags) > 2) {
            $rows = array_chunk($tags, 2);
        } else {
            $rows[] = $tags;
        }

        $count = 0;
        foreach ($rows as $row_items) {
            $itemTags = array();

            foreach ($row_items as $tag) {
                $itemTags[] = $this->getComponentRow(array(
                    $this->getComponentText($tag, array(
                        'style' => 'item_tag_text'
                    )),
                    $this->getComponentImage('cancel-icon-dev.png', array(
                        'style' => 'item_tag_image_delete',
                        'onclick' => [
                            $this->getOnclickSubmit('Createvisit/deleteTag/' . $tag),
                            $this->getOnclickHideElement('item-tag-' . $count)
                        ],
                    )),
                ), array(
                    'id' => 'item-tag-' . $count,
                    'style' => 'item_tag_wrapper'
                ));

                $count++;
            }

            $this->layout->scroll[] = $this->getComponentRow($itemTags, array(), array(
                'margin' => '10 20 0 20'
            ));
        }

        return true;
    }

    protected function renderActionsText()
    {
        $this->layout->scroll[] = $this->getComponentText('{#your_visit_review_is_saved#}', array(), array(
            'text-align' => 'center',
            'color' => '#787e82',
            'font-size' => '22',
            'font-weight' => 'bold',
            'margin' => '40 0 0 0'
        ));
        $this->layout->scroll[] = $this->getComponentText('{#for_best_result_please_complete_at_least_one_of_the_below_actions#}', array(), array(
            'color' => '#787e82',
            'text-align' => 'center',
            'font-size' => '16',
            'margin' => '20 30 0 30'
        ));
    }

    protected function renderActions()
    {
        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        $emailDivOnclick = $this->getOnclickShowDiv('email', array(
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        ));

        $emailIntro = $this->getOnclickOpenAction('visitemailintro', false, array(
            'id' => 'visit_opened',
            'sync_open' => true,
        ));

        $this->layout->scroll[] = $this->uiKitIconButton(strtoupper('{#email_review#}'), array(
            'onclick' => $this->model->getSavedVariable('visit_email_sent') == 1 ?
                $emailDivOnclick : $emailIntro
        ), array('border-radius' => '0',
            'border-width' => '3',
            'border-color' => '#e6e6e6'));

        $this->layout->scroll[] = $this->uiKitIconButton(strtoupper('{#follow_up_reminder#}'), array(
            'onclick' => $this->getOnclickShowDiv('set_reminder', array(
                'background' => 'blur',
                'tap_to_close' => 1,
                'transition' => 'from-bottom',
                'layout' => $layout
            ))
        ), array('border-radius' => '0',
            'border-width' => '3',
            'border-color' => '#e6e6e6'));

        $this->layout->scroll[] = $this->uiKitIconButton(strtoupper('{#next_visit#}'), array(
            'onclick' => $this->getOnclickShowDiv('next_visit', array(
                'background' => 'blur',
                'tap_to_close' => 1,
                'transition' => 'from-bottom',
                'layout' => $layout
            ))
        ), array('border-radius' => '0',
            'border-width' => '3',
            'border-color' => '#e6e6e6'));

        $this->layout->scroll[] = $this->uiKitIconButton(strtoupper('{#fc_expert_support#}'), array(
            'onclick' => $this->getOnclickOpenAction('fcexperts', false, array(
                'back_button' => 1
            ))
        ), array('border-radius' => '0',
            'border-width' => '3',
            'border-color' => '#e6e6e6'));
    }

    private function renderFloatingButtons()
    {
        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'icon-save-item.png',
                'onclick' => $this->getOnclickSubmit($this->menuId, array(
                    'block_ui' => 1
                ))
            ]
        ], true);
    }

    private function getController()
    {
        return $this->model->getConfigParam('mode');
    }

    private function setSubject()
    {
        $subject = 'ADD VISIT';
        if ($this->getData('subject', 'string')) {
            $subject = $this->getData('subject', 'string');
        }

        $this->model->rewriteActionField('subject', $subject);

        return true;
    }

    private function renderErrorMessage(string $name)
    {
        $this->layout->scroll[] = $this->getComponentText($name . '. {#please_navigate_to_the_general_tab_and_add_it#}.', [], [
            'parent_style' => 'validation_error_message',
            'text-align' => 'center',
            'padding' => '15 15 10 15',
        ]);
    }

}