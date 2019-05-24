<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait InfiniteScroll {

    /**
     * Infinite scrolling for paging. Will refresh the view with $_REQUEST parameter indicating the next page
     *
     * @param $content array of other objects
     * @param array $parameters selected_state, variable, onclick, style
     * <code>
     * $array = array(
     * 'nextpageid' => '20',  // page to load, typically next records
     * 'show_loader' => 1,
     * );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */
    public function getInfiniteScroll(array $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

		$obj = new \StdClass;
        $obj->type = 'infinite-scroll';
        $obj->items = $content;

        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters);
        $obj = $this->configureDefaults($obj);

        return $obj;
	}

}