<?php

namespace packages\actionMbooking\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMbooking\Models\Model as ArticleModel;

class Location extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMbooking\Components\Components
     */
    public $components;
    public $theme;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        $lat = $this->getData('lat', 'float');
        $lon = $this->getData('lon', 'float');

        $position = $lat . ',' . $lon;

        $this->layout->scroll[] = $this->getComponentMap(array(
            'position' => $position,
            'zoom' => 15,
            'map_type' => 'terrain',
            'markers' => array(
                array(
                    'position' => $position,
                    'icon' => 'marker.png'
                )
            )
        ), array(
            'height' => $this->screen_height
        ));

        return $this->layout;
    }
}
