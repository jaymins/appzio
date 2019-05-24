<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait Timer {

    /**
     * @param $content - unix time
     * @param array $parameters timer_id, mode, submit_menu_id
     * <code>
     * $array = array(
     *    'timer_id' => 'timer1',  // this is needed if your app uses more than one timer. Timers retain time even when they are not visible.
     *    'mode'   => 'countdown', // other option: countup
     *    'submit_menu_id' => 'submitname', // once timer ends, this menu command is called
     *    'id' => 'mycustomid',
     *    'variable' => 'variablename' // variable name used in submission,
     *    "date_started":"1552650452", // important, this is needed for countup
     *    "date_stopped":"1552650458",
     *    "date_updated":"1552650458",
     *    "format":"%d : %02h : %02m : %02s (seconds|second)",
     *    "duration":"10",
     *    "oncomplete":[{"action":"submit-form-content", "id":"timercompleted"}] // if this is defined,
     *                          the default refesh is not called. Ie. you can do async implementation.
     *    );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */
    public function getTimer(string $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

		$obj = new \StdClass;
        $obj->type = 'timer';
        $obj->content = $content;

        $required = array('timer_id','mode');
        
        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters,array(),$required);
        $obj = $this->configureDefaults($obj);

        return $obj;
	}

}