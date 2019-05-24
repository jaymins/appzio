<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait Webview {

    /**
     * @param $url - initial url
     * @param array $parameters
     * <code>
     * $array = array(
     * 'submit_on_url' => 'https://appzio.com/platform/',
     * 'submit_on_javascript'   => 1,
     * 'submit_on_string_match' => 'stringtomatch', // regex pattern
     * 'full_scroll' => 1, // use entire scroll for the view
     * 'show_submit_page' => 1, // whether the last page should be shown or not
     * );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */

    public function getComponentWebview(string $url, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

		$obj = new \StdClass;
        $obj->type = 'webview';
        $obj->content = $url;

        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters);
        $obj = $this->configureDefaults($obj);

        return $obj;
	}

}