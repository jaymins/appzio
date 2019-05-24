<?php

namespace Bootstrap\Components\AppzioUiKit\Listing;

use Bootstrap\Views\BootstrapView;

trait uiKitPeopleList
{
    /**
     * @param array $people
     * array(
     *     array(
     *         'name' - the name of the person - required,
     *         'info' - additional information - required,
     *         'contact' - contact information for the person - required,
     *         'onclick' - action to be executed on row click - required
     *     )
     * )
     * @param array $params
     * @param array $styles
     * @return \stdClass
     */
    public function uiKitPeopleList(array $people, array $params = array(), array $styles = array())
    {
        /** @var BootstrapView $this */
        $page = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] : 1;

        $items = array();

        foreach ($people as $item) {
            $items[] = $this->getPersonRow($item);
            $items[] = $this->uiKitDivider();
        }

        if (count($people) < 20) {
            return $this->getComponentColumn($items);
        }

        if (isset($params['infinite'])) {
            return $this->getInfiniteScroll($items, array(
                'next_page_id' => $page + 1
            ));
        }

        // In certain conditions we don't want to use infinite lists.
        // Sometimes they cause UI issues on android after filtering.
        return $this->getComponentColumn($items);
    }

    /**
     * Returns a listing row for a single person
     *
     * @param $person
     * @return \stdClass
     */
    protected function getPersonRow($person)
    {
        /** @var BootstrapView $this */
        return $this->getComponentRow(array(
            $this->getComponentColumn(array(
                $this->getPersonName($person['name']),
                $this->getPersonInfo($person['info']),
                $this->getPersonContact($person['contact']),
                $this->getPersonUnit($person['business_unit'])
            ))
        ), array(
            'style' => 'uikit_people_list_person_wrapper',
            'onclick' => $this->getRowClick($person['contact'])
        ));
    }

    /**
     * Returns person name markup
     *
     * @param $name
     * @return mixed
     */
    protected function getPersonName($name)
    {
        return $this->getComponentText($name, array(
            'style' => 'uikit_people_list_person_name'
        ));
    }

    /**
     * Returns person information markup
     *
     * @param $info
     * @return mixed
     */
    protected function getPersonInfo($info)
    {
        return $this->getComponentText(strtoupper($info), array(
            'style' => 'uikit_people_list_person_info'
        ));
    }

    /**
     * Returns person contact info markup
     *
     * @param $contact
     * @return mixed
     */
    protected function getPersonContact($contact)
    {
        return $this->getComponentText($contact, array(
            'style' => 'uikit_people_list_person_contact'
        ));
    }

    /**
     * Returns person business_unit info markup
     *
     * @param $contact
     * @return mixed
     */
    protected function getPersonUnit($contact)
    {
        return $this->getComponentText(strtoupper($contact), array(
            'style' => 'uikit_people_list_person_contact'
        ));
    }

    /**
     * Returns row onclick open div action
     *
     * @param $contact string
     * @return mixed
     */
    protected function getRowClick($contact)
    {
        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        $onclick[] = $this->getOnclickShowDiv('email', array(
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        ));

        $onclick[] = $this->getOnclickSetVariables(array('recipient_email' => $contact));

        return $onclick;
    }

}