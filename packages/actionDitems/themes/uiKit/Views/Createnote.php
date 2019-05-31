<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\themes\uiKit\Views\Create as BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class Createnote extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    private $menuId;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');
        $this->model->rewriteActionField('subject', 'ADD NOTE');

        if ($this->getData('created', 'bool')) {
            $this->layout->onload[] = $this->getOnclickGoHome();
            return $this->layout;
        }

        $this->presetData = $this->getData('presetData', 'array');

        $this->setNoteCreateHeader();
        $this->renderVisitImages();
        $this->renderDivider();
        $this->renderNoteDescriptionField();
        $this->renderDivider();
        $this->renderDatePicker();
        $this->renderTags();

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
           [
               'icon' => 'icon-save-item.png',
               'onclick' => $this->getOnclickSubmit($this->menuId, array(
                   'block_ui' => 1
               ))
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

    protected function setNoteCreateHeader()
    {
        $this->menuId = 'Createnote/saveNote';
        $title = 'ADD NOTE';

        $itemId = $this->getData('itemId', 'mixed');
        if ($itemId) {
            $this->menuId .= '/edit_' . $itemId;
            $title = 'EDIT NOTE';
        }

        $this->layout->header[] = $this->components->uiKitVisitTopbar('arrow-back-white-v2.png', $title, $this->getOnclickGoHome(), array(
            'background-color' => '#fecb2f'
        ));
    }

    protected function renderNoteDescriptionField()
    {
        $value = isset($this->presetData['name']) ? $this->presetData['name'] : '';

        $this->layout->scroll[] = $this->getComponentFormFieldText($value, array(
            'variable' => 'name',
            'value' => $value,
            'hint' => '{#note_title#}',
        ), array(
            'padding' => '22 20 22 20'
        ));

        $this->renderValidationErrors('name');
        
        $this->renderDivider();

        $value = isset($this->presetData['description']) ? $this->presetData['description'] : '';

        $this->layout->scroll[] = $this->getComponentFormFieldTextArea($value, array(
            'variable' => 'description',
            'value' => $value,
            'hint' => '{#description#}',
        ), array(
            'padding' => '22 20 22 20'
        ));

        $this->renderValidationErrors('description');
    }

}