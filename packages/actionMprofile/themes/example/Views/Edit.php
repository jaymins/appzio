<?php

namespace packages\actionMprofile\themes\example\Views;
use packages\actionMprofile\Views\Edit as BootstrapView;
use packages\actionMprofile\themes\example\Components\Components;

class Edit extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function __construct($obj)
    {
        parent::__construct($obj);

        if ($this->model->getSavedVariable('role') == 'artist') {
            $this->fields['address'] = array(
                'label' => 'Address'
            );

            $this->fields['phone'] = array(
                'label' => 'Phone',
                'type' => 'number'
            );
        }
    }


    protected function getProfileImages()
    {
        return $this->getComponentRow(array(
            $this->components->getUserProfileImage()
        ), array(), array(
            'text-align' => 'center',
            'margin' => '20 0 0 0',
        ));
    }

    /**
     * Render profile save button
     *
     * @return \stdClass
     */
    protected function getSaveButton()
    {
        if ($this->getData('saved', 'bool')) {
            $buttonText = '{#saved#}';
        } else {
            $buttonText = '{#save#}';
        }

        return $this->getComponentText(strtoupper($buttonText), array(
            'style' => 'button_primary',
            'onclick' => array(
                $this->getOnclickSubmit('save-profile'),
                $this->getOnclickListBranches(),
            )
        ));
    }

}
