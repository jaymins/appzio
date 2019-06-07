<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionData\Views;

use Bootstrap\Views\BootstrapView;


class View extends BootstrapView {

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionData\Components\Components
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
        $this->setTopShadow();

        $this->layout->scroll[] = $this->getComponentText('Listing', [], [
            'padding' => '10 15 10 15',
            'font-size' => '19',
        ]);

        $this->getExtendedGrid();

// buttons


        $this->layout->scroll[] = $this->getComponentSpacer(30);

        $this->layout->scroll[] = $this->getComponentText('Radio Buttons', [], [
            'padding' => '10 15 10 15',
            'font-size' => '19',
        ]);

        $this->layout->scroll[] = $this->getComponentDivider([
            'background' => '#d10e0e',
        ]);

        $this->getButtons([
            'Radio button 1',
            'Radio button 2',
        ], 'radio');

        /**
         * get the data defined by the controller
         */
       // $fieldlist = $this->getData('fieldlist','array');

       // $this->layout->scroll[] = $this->getComponentImage('logo3.png', array());

        //$this->layout->scroll[]  = $this->getComponentText('Welcom to Basezio');

//        foreach($fieldlist as $field){
//            $this->addField_page1($field);
//        }

        /**
         *  route: Contoller/action. Controller defines the view file.
         *   this is the more complex routing just for an example. See
         *   Pagetwo.php for more straight-forward way of doing it. This
         *   can pass any number of parameters with the click.
         */

        $btn[] = $this->getComponentText('Sign Up',
            array('style' => 'mreg_btn',
                'onclick' => $this->getOnclickRoute(
                'Controller/default/signup',
              true,
                array('mytestid' => 'Flower sent from Default view','exampleid' => 393393),
                true
            )));

        //$this->layout->footer[] = $this->getComponentRow($btn,array('style' => 'mreg_btn_row'));
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
        $divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    /**
     * Model passes the fields that are configured using the webform (simple checkbox whether they are enabled
     * or not) and adds them directly on the $this->layout->scroll[].
     * @param $field
     */
    public function addField_page1($field){
        switch($field){

            case 'mreg_collect_photo':
                $this->layout->scroll[] = $this->components->getPhotoField('mreg_collect_photo');
                break;

            case 'mreg_collect_full_name':
                $content[] = $this->components->getIconField('firstname','{#first_name#}','mreg-icon-person.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('lastname','{#last_name#}');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%'
                )));
                break;

            case 'mreg_collect_phone':
                $content[] = $this->components->getPhoneNumberField($this->getData('current_country','string'),'phone','{#phone#}','mreg-icon-phone.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%'
                )));
                break;


            case 'mreg_collect_email':
                $content[] = $this->components->getIconField('email','{#email#}','mreg-icon-mail.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('password','{#password#}','mreg-icon-key.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('password_again','{#password_again#}');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%'
                )));

                break;
        }
    }

    /**
     * Simple small helper for providing a divider element.
     * @return \stdClass
     */
    public function getDivider(){
        return $this->getComponentText('',array('style' => 'mreg_divider'));
    }

    /**
     * Sets a small shadow on top of the view.
     */
    public function setTopShadow(){
        $txt[] = $this->getComponentText('');
        $this->layout->header[] = $this->getComponentRow($txt, array(), array(
            'background-color' => $this->color_top_bar_color,
            'parent_style' => 'mreg_top_shadow'
        ));
    }

    public function getExtendedGrid() {

        $elements_per_row = 2;
        $chunks = array_chunk($this->getElements(6), $elements_per_row);

        foreach ($chunks as $index => $row_items) {
            $items = [];

            foreach ($row_items as $i => $row_item) {
                $items[] = $this->getComponentColumn([
                    $this->getComponentImage($row_item['image'], [], [
                        'margin' => '2 2 0 2',
                        'crop' => 'round',
                        'width' => '80%',
                    ]),
                    $this->getComponentText($row_item['title'], [], [
                        'font-size' => '17',
                        'padding' => '7 0 7 0',
                    ]),
                    $this->getComponentText($row_item['description'], [], [
                        'font-size' => '13',
                        'color' => '#8b8e89',
                        'padding' => '0 0 7 0',
                        'text-align' => 'center',
                    ]),
                ], [], [
                    'width' => 100 / $elements_per_row . '%',
                    'padding' => '3 5 3 5',
                    'text-align' => 'center',
                ]);
            }

            $this->layout->scroll[] = $this->getComponentRow($items, [], [
                'width' => 'auto',
                'vertical-align' => 'middle',
                'margin' => '0 15 0 15',
            ]);

            if ( ($index+1) < count($chunks) ) {
                $this->layout->scroll[] = $this->getComponentSpacer(1, [], [
                    'background-color' => '#eeeeee',
                    'margin' => '4 0 4 0',
                ]);
            }

        }

    }

    public function getElements( $count = 6 ) {

        $elements = [
            [
                'title' => 'Element 1',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo5.png'
            ],
            [
                'title' => 'Element 2',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo6.jpg'
            ],
            [
                'title' => 'Element 3',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo7.png'
            ],
            [
                'title' => 'Element 4',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo8.jpg'
            ],
            [
                'title' => 'Element 5',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo9.png'
            ],
            [
                'title' => 'Element 6',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo10.png'
            ],
        ];

        return array_slice($elements, 0, $count);
    }

    private function getButtons( array $items, $type ) {

        $nameList = 'testvar';
        $prefix = 'var';

        foreach ($items as $item) {
            $checkbox = [];

            $variable = $nameList . '-' . trim($item, '{#}');

            $value = 1;
            $active = 0;

            if ( $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                $active = 1;
            }

            if ( $type == 'radio' ) {
                $variable = $nameList;
                $value = trim($item, '{#}');

                if ( $value == $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                    $active = 1;
                }
            }

            $icon = $type == 'radio' ? 'layouts-selected-icon-radio.png' : 'layouts-selected-icon.png';

            $selectstate = array(
                'variable' => $prefix . $variable,
                'style_content' => array(
                    'background-image' => $this->getImageFileName($icon),
                    'background-size' => 'contain',
                    'width' => '30',
                    'height' => '30',
                    'text-align' => 'center',
                    'vertical-align' => 'middle',
                ),
                'allow_unselect' => 1,
                'variable_value' => $value,
                'animation' => 'fade',
                'active' => $active,
            );

            $checkbox[] = $this->getComponentText(' ', array(
                'variable' => $prefix . $variable,
                'allow_unselect' => 1,
                'variable_value' => 0,
                'selected_state' => $selectstate
            ), array(
                'border-color' => '#000000',
                'border-radius' => '15',
                'width' => '30',
                'height' => '30',
                'vertical-align' => 'middle',
            ));

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText($item, [], [
                    'text-align' => 'left',
                    'font-size' => '18',
                    'color' => '#004a55',
                    'width' => '80%',
                    'vartical-align' => 'middle',
                ]),
                $this->getComponentColumn($checkbox, array(), array(
                    'color' => '#161616',
                    'vertical-align' => 'middle',
                    'text-align' => 'right',
                    'padding' => '10 0 10 0',
                    'width' => 'auto',
                ))
            ], [], [
                'margin' => '5 15 5 15'
            ]);

            $this->layout->scroll[] = $this->getComponentDivider([
                'background' => '#dddddd'
            ]);
        }

    }


}