<?php

namespace packages\actionMitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components as Components;

class Location extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    /**
     * Main view entrypoint
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->getHeader();

        if ($this->getData('complete', 'bool')) {
            $this->layout->onload[] = $this->getOnclickClosePopup(array(
                'set_variables_data' => array(
                    $this->model->getVariableId('address') => $this->model->getSavedVariable('address')
                )
            ));
        }

        $addresses = $this->getData('predictions', 'array');

        if (empty($addresses)) {
            return $this->layout;
        }

        foreach ($addresses as $address) {
            $this->getAddressRow($address);
        }

        return $this->layout;
    }

    protected function getHeader()
    {
        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText('', array(
                'style' => 'search-filter-location',
                'variable' => 'term',
                'hint' => '{#enter_city_or_location#}'
            ), array(
                'color' => '#000000',
                'padding' => '10 10 10 10',
                'font-size' => '18',
                'font-ios' => 'Roboto',
                'font-android' => 'Roboto'
            )),
            $this->getComponentText('Search', array(
                'style' => 'search-filter-button',
                'onclick' => $this->getOnclickSubmit('search')
            ), array(
                'color' => '#ffffff',
                'background-color' => '#EDAC34',
                'padding' => '10 10 10 10',
                'font-weight' => 'bold',
                'font-size' => '18',
                'font-ios' => 'Roboto',
                'font-android' => 'Roboto',
                'floating' => 1,
                "float" => 'right'
            )),
        ), array(), array(
            'background-color' => '#ffffff',
        ));
    }

    protected function getAddressRow($address)
    {
        $this->layout->scroll[] = $this->getComponentText($address['description'], array(
            'onclick' => $this->getOnclickSubmit('save-address-' . $address['description'])
        ), array(
            'padding' => '10 10 10 10',
            'font-weight' => 'bold',
            'font-ios' => 'Roboto',
            'font-android' => 'Roboto'
        ));
    }
}