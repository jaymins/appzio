<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait Map {

    /**
     * @param $content string, no support for line feeds
     * @param array $parameters selected_state, variable, onclick, style
     * <code>
     * array(
     *      'position' => '1.232324,23.234234234234',  // lat, lon
     *      'zoom' => 15,
     *      'map_type' => 'terrain',
     *      'markers' => array(
     *          array(
     *              'position' => '1.232324,23.234234234234',  // lat, lon
     *              'icon' => 'marker.png'
     *              'onclick' => $this->getOnclickOpenAction('search')
     *          )
     *      )
     * )
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */

    public function getComponentMap(array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

		$obj = new \StdClass;
        $obj->type = 'map';

        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters);
        $obj = $this->configureDefaults($obj);

        return $obj;
	}

}