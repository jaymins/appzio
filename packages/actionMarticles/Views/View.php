<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMarticles\Views;

use Bootstrap\Views\BootstrapView;


class View extends BootstrapView {

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMarticles\Components\Components
     */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /**
     * View will always need to have a function called tab1. View can include up to five tabs, named simply tab2 etc.
     * Advantage with tabs are, that all tabs are loaded when action is updated, so you can navigate between tabs
     * without doing any refreshes. To navigate to another tab, define OnClick in the following way:
     * <code>
     *  $this->getOnclickTab(2);
     * </code>
     *
     * View should always return a class, with at least one of these defined:
     *
     * $this->layout->header[]
     *
     * $this->layout->scroll[]
     *
     * $this->layout->footer[]
     *
     * $this->layout->onload[]
     *
     * $this->layout->control[]
     *
     * Each of these sections must be an array and the array can only include objects. Be careful with types,
     * returning any other types will throw an error in the client.
     *
     * Data from controller is accessed using $this->getData('fieldname','array');
     *
     * Data from controller must have type defined. This is to avoid data type errors which can happen rather
     * easily without type casting.
     *
     * @link http://docs.appzio.com/php-toolkit/viewsbootstrapview-php/
     *
     * @return \stdClass
     */

    public function tab1(){
        $this->layout = new \stdClass();

        return $this->layout;
    }

    /**
     * Divs are containers that are loaded when action is refreshed and can be activated and hidden
     * without actually refreshing the view. This makes it possible to build very complex interactions
     * that don't require turnaround to the server, thus providing much more responsive interface for
     * the user.
     *
     * Div's are always named and referred by their names, so the getDivs must return an object with named
     * divs inside of it. To show a div, you you use OnClickShowDiv.
     *
     * In our example the showing of the div is handled by component getPhoneNumberField like this:
     * <code>
     * $this->getOnclickShowDiv('countries',$clickparams)
     * </code>
     * @return \stdClass
     */

    public function getDivs(){
        $divs = new \stdClass();

        /* look for traits under the components */
        // $divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

}