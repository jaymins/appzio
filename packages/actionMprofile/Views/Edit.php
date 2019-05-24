<?php

namespace packages\actionMprofile\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMprofile\Components\Components;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Edit extends BootstrapView
{

    public $grid;
    public $margin;

    /**
     * This class provides access to the module's components
     *
     * @var \packages\actionMprofile\Components\Components
     */
    public $components;

    /**
     * Generated component name prefix
     */
    protected const COMPONENT_PREFIX = 'get';

    /**
     * Generated component name suffix
     */
    protected const COMPONENT_SUFFIX = 'ComponentField';

    /**
     * Fields that should be rendered in the view
     *
     * @var array
     */
    protected $fields = array(
        'firstname' => array(
            'label' => 'First name',
        ),
        'lastname' => array(
            'label' => 'Last name'
        ),
        'email' => array(
            'label' => 'Email',
            'type' => 'email'
        )
    );

    /**
     * View constructor.
     *
     * @param $obj
     */
    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entrypoint
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getUserName();

        $this->layout->scroll[] = $this->getProfileImages();

        $this->layout->scroll[] = $this->getProfileFields();

        $this->layout->scroll[] = $this->getAdditionalText();

        $this->layout->footer[] = $this->getSaveButton();

        return $this->layout;
    }

    /**
     * Returns the component holding the username
     *
     * @return \stdClass
     */
    protected function getUserName()
    {
        return $this->getComponentText($this->model->getSavedVariable('firstname') . ' ' . $this->model->getSavedVariable('lastname'), array(), array(
            'text-align' => 'center',
            'margin' => '20 0 0 0',
            'font-size' => '20',
            'font-ios' => 'Roboto',
            'font-android' => 'Roboto',
            'font-weight' => 'bold'
        ));
    }

    /**
     * Returns profile images - by default is using grid component
     *
     * @return array|\stdClass
     */
    protected function getProfileImages()
    {
        return $this->getComponentImageGrid(array(
            'base_variable' => 'profilepic'
        ));
    }

    /**
     * Render user's profile fields.
     * If a custom method is provided it will be called.
     * If not a generic input will be used.
     *
     * @return \stdClass
     */
    protected function getProfileFields()
    {
        $wrapper = array();

        foreach ($this->fields as $field => $data) {

            $component = $this->getComponentName($field);

            // If such a component is declared use it
            if (method_exists($this->components, $component)) {

                $wrapper[] = $this->components->{$component};

            } else {

                // Otherwise use the generic one
                $wrapper[] = $this->components->getComponentField($field, $data['label'], isset($data['type']) ? $data['type'] : 'text');

            }
        }

        return $this->getComponentColumn($wrapper, array(
            'style' => 'propertydetail-shadowbox-indent'
        ), array(

        ));
    }

    /**
     * Render profile save button
     *
     * @return \stdClass
     */
    protected function getSaveButton()
    {
        return $this->getComponentText('{#save#}', array(
            'onclick' => array(
                $this->getOnclickSubmit('save-profile'),
                $this->getOnclickListBranches()
            )
        ));
    }

    /**
     * Get component name for field
     *
     * @param $field
     * @return string
     */
    protected function getComponentName($field)
    {
        return self::COMPONENT_PREFIX . ucfirst($field) . self::COMPONENT_SUFFIX;
    }

    /**
     * Get additional terms or clarification text
     *
     * @return \stdClass
     */
    protected function getAdditionalText()
    {
        return $this->getComponentText('{#we_do_not_share_your_public_information#}', array(), array(
            'font-size' => '12',
            'text-align' => 'center',
            'margin' => '10 0 10 0'
        ));
    }
}
