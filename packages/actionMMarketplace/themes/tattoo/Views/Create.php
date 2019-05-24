<?php

namespace packages\actionMMarketplace\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMMarketplace\Models\Model as ArticleModel;
use packages\actionMMarketplace\Components\Components;

class Create extends BootstrapView
{

    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public $presetData;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->presetData = $this->getData('presetData', 'array');

        $this->model->setBackgroundColor('#ffffff');
        $this->renderHeader();
        $this->openPopUp('category_mm');
        $this->renderImageGrid();
        $this->renderTitleField();
        $this->renderDescriptionField();
        $this->renderDatePicker();
        $this->renderSubmitButton();

        return $this->layout;
    }

    public function renderHeader()
    {
        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('marketplace');

        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentRow(array(
                $this->getComponentImage('back-arrow.png', array(
                    'onclick' => $onclick
                ), array(
                    'vertical-align' => 'middle',
                    'width' => '30',
                    'floating' => true,
                    'float' => 'left'
                ))
            ), array(), array(
                'width' => '10%'
            )),
            $this->getComponentRow(array(
                $this->getComponentText('{#add_bid_request#}', array(), array(
                    'vertical-align' => 'middle',
                    'color' => '#ffffff'
                ))
            ), array(), array(
                'width' => '80%',
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(),array(), array(
                'width' => '10%'
            ))
        ), array(), array(
            'vertical-align' => 'middle',
            'text-align' => 'center',
            'padding' => '10 0 10 0',
            'background-color' => '#29292c',
            'width' => '100%'
        ));
    }

    protected function renderSubmitButton()
    {
        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentText('{#create_bid#}', array(
                'onclick' => $this->getOnclickSubmit('Create/save'),
                'style' => 'tattoo_marketplace_submit_button'
            ))
        ), array(
            'style' => 'tattoo_marketplace_submit_button_wrapper'
        ));
    }

    public function renderBlockHeadLine($name)
    {
        $this->layout->scroll[] = $this->getSeparator();
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper($name), array(
            ), array(
                'color' => '#000000',
                'margin' => '20 0 20 20'
            ))
        ), array(), array(
            'background-color' => '#f9f9f9'
        ));
        $this->layout->scroll[] = $this->getSeparator();
    }

    public function renderDatePicker()
    {
        $this->layout->scroll[] =  $this->uiKitBackgroundHeader('{#valid_until#}');

        $this->layout->scroll[] = $this->components->uiKitCalendarCustom('Date', 'date', strtotime( '+ 7 days' ), array(
            'active_icon' => 'calendar_black.png',
            'inactive_icon' => 'calendar_black.png',
            'disable_hint' => true
        ), array(
            'margin' => '0 15 0 15'
        ));
    }

    public function renderTitleField()
    {
        $this->layout->scroll[] =  $this->uiKitBackgroundHeader('{#title#}');

        $value = isset($this->presetData['title']) ? $this->presetData['title'] : '';

        $this->layout->scroll[] = $this->getComponentFormFieldText($value, array(
            'variable' => 'title',
            'value' => $value,
            'hint' => '{#type_here#}',
        ), array(
            'padding' => '0 15 10 15'
        ));

        $this->renderValidationErrors('title');

    }

    public function renderDescriptionField()
    {
        $this->layout->scroll[] =  $this->uiKitBackgroundHeader('{#description#}');

        $value = isset($this->presetData['description']) ? $this->presetData['description'] : '';

        $this->layout->scroll[] = $this->getComponentFormFieldText($value, array(
            'variable' => 'description',
            'value' => $value,
            'hint' => '{#type_here#}',
        ), array(
            'padding' => '0 15 10 15'
        ));

        $this->renderValidationErrors('description');

    }

    public function renderImageGrid()
    {
        $this->layout->scroll[] =  $this->uiKitBackgroundHeader('{#example_pictures#}');

//        $this->layout->scroll[] = $this->getComponentText(strtoupper('{#upload_photo#}'), array(),
//            array(
//                'text-align' => 'right',
//                'margin' => '10 20 0 10'
//            ));
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getPlaceholderImage('itempic'),
            $this->getPlaceholderImage('itempic2'),
            $this->getPlaceholderImage('itempic3')
        ), array(), array(
            'text-align' => 'center',
            'padding' => '20 0 20 0'
        ));
    }

    public function getSeparator()
    {
        return $this->getComponentText('', array(), array(
            'background-color' => '#4a4a4d',
            'height' => '1',
            'width' => '100%',
        ));
    }

    protected function getPlaceholderImage($name)
    {
        $onclick = new \StdClass();
        $onclick->action = 'upload-image';
        $onclick->max_dimensions = '1200';
        $onclick->variable = $this->model->getVariableId($name);
        $onclick->action_config = $this->model->getVariableId($name);
        $onclick->sync_upload = 1;

        return $this->getComponentImage($this->model->getSavedVariable($name), array(
            'defaultimage' => 'formkit-photo-placeholder.png',
            'onclick' => $onclick,
            'use_variable' => true,
            'variable' => $this->model->getVariableId($name),
            'config' => $this->model->getVariableId($name),
            'debug' => 1,
            'priority' => 9
        ), array(
            'width' => '85',
            'height' => '85',
            'imgcrop' => 'yes',
            'crop' => 'yes',
            'border-radius' => '3',
            'margin' => '0 5 0 5'
        ));
    }



    public function openPopUp($name) {

        $onclick = $this->getOnclickOpenAction(
            'stylespopup',
            false,
            array('sync_open' => 1,'open_popup' => 1, 'sync_close' => 1),
            'Statuses/default/' . $name. '|item',
            false);

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText('{#tattoo_styles#}', array('style'=> 'extradata_hint', 'uppercase' => true)),
            $this->getComponentImage('rounded-add-button.png', array(
                'onclick' => $onclick
            ),
                array(
                    'background-color' => '#f9f9f9',
                    "margin"=> "10 10 10 20",
                    'width' => '20',
                    'floating' => 1,
                    'float' => 'right'
                ))
        ), array(), array(
            'background-color' => '#f9f9f9'
        ));

        $myStyles = json_decode($this->model->getSavedVariable('itemcategory'), true);
        if ($myStyles) {
            $count = 1;
            foreach ($myStyles as $myStyle) {
                $this->layout->scroll[] = $this->getComponentRow(
                    array(
                        $this->getComponentText('{#' . $myStyle . '#}',array(),array(
                            "height" => "50",
                            "padding" => "10 0 10 20",
                        )),
                        $this->getComponentImage('remove.png', array(
                            'onclick' => $this->getOnclickSubmit('remove_style_' . $count, array('sync_open' => 1)
                            )
                        ),
                            array(
                                "margin"=> "10 10 10 20",
                                'width' => '20',
                                'floating' => 1,
                                'float' => 'right'
                            )),
                        $this->getComponentFormFieldText($myStyle, array(
                            'visibility' => 'hidden',
                            'variable' => 'remove_style_' . $count,
                        ))
                    ));

                $count++;
            }
        }

        $this->renderValidationErrors('categories');
    }

    public function renderValidationErrors($field) {
        if (isset($this->model->validation_errors[$field])) {
            $this->layout->scroll[] = $this->getComponentText($this->model->validation_errors[$field], array(
                'style' => 'validation_error_message'
            ));
        }
    }

}