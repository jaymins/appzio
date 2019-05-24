<?php
namespace packages\actionMMarketplace\Components;

trait uiKitItemCardHalfRow {


    /**
     * Fully customisable item card
     *
     * @param $item object
     * @param array $parameters
     * example array(
     *  "image" => "the name of the image", - the first element that will be displayed
     *  "info" => "text", - small text under the image
     *  "user" => array (
     *      "profilepic" => "image name",
     *      "firstname" => "text",
     *      "lastname" => "text"
     *  ),
     *  "date" => DateTime object,
     *  "action" => "text", - the permaname of the action which will be opened on click
     *  "id" => "text" - if the id that needs to be opened is different from the $item->id
     * )
     * @return mixed
     */
    public function uiKitItemCardHalfRow($item, $parameters = array()) {

        $info = null;
        if (isset($parameters['info'])) {
            $info = $parameters['info'];
        }

        $user = null;
        if (isset($parameters['user'])) {
            $user = $parameters['user'];
        }

        $date = null;
        if (isset($parameters['date'])) {
            //The date is expected to be a DateTime object
            $date = $parameters['date'];
        }

        $id = $item->id;
        if (isset($parameters['id'])) {
            $id = $parameters['id'];
        }

        $action = 'viewitems';
        if (isset($parameters['action'])) {
            $action = $parameters['action'];
        }

        $columnRows = array();

        if (isset($parameters['image'])) {

            $columnRows[] = $this->getComponentRow(array(
                $this->getComponentImage($parameters['image'], array(
                    'priority' => 9,
                    'style' => 'uikit_item_card_half_row_image'
                ))
            ), array(
                'style' => 'uikit_item_card_half_row_image_row'
            ));
        }

        if ($info) {
            $columnRows[] = $this->getComponentText($info, array(
                'style' => 'uikit_item_card_half_row_info'
            ));
        }

        if ($user) {
            $columnRows[] = $this->getComponentRow(array(
                $this->getComponentImage($user['profilepic'], array(
                    'priority' => 9,
                    'style' => 'uikit_item_card_half_row_user_pic'
                )),
                $this->getComponentText($user['firstname'] . ' ' . $user['lastname'], array(
                    'style' => 'uikit_item_card_half_row_user_name'
                )),
            ), array(), array(
                'width' => '100%',
                'text-align' => 'center',
            ));
        }

        $title = '';
        if (isset($item->title)) {
            $title = $item->title;
        } else if (isset($item->name)) {
            $title = $item->name;
        }

        $columnRows[] = $this->getComponentText($title, array(
            'style' => 'uikit_item_card_half_row_title'
        ));

        if ($date) {
            $columnRows[] = $this->getComponentText($date->format('d-m-Y'), array(
                'style' => 'uikit_item_card_half_row_date'
            ));

            if (!isset($parameters['dateOnly'])) {
                $columnRows[] = $this->getComponentText($date->format('H:i T'), array(
                    'style' => 'uikit_item_card_half_row_time'
                ));
            }
        }

        $openAction = $this->getOnclickOpenAction($action, false, array(
            'id' => $id,
            'back_button' => 1,
            'sync_open' => 1
        ));

        return $this->getComponentColumn($columnRows,
            array(
                'onclick' => $openAction,
                'style' => 'uikit_item_card_half_row_container'
            ));
    }
}