<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMcalendar\Views;

use packages\actionMcalendar\Models\Model;


class Schedule extends Daily
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMcalendar\Components\Components
     */
    public $components;
    public $theme;

    /* @var Model */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->setHeader(3, true);
        $calendar = $this->getData('calendar', 'array');

        foreach ($calendar as $entry) {
            if (empty($entry['items'])) {
                continue;
            }

            $infinite[] = $this->components->themeGetCalendarScheduleDay(
                $entry['day'],
                $entry['name'],
                $entry['items'],
                $entry['notifications'],
                true
            );
            $infinite[] = $this->getComponentDivider();
        }

        if(isset($infinite)){
            $this->layout->scroll[] = $this->getInfiniteScroll($infinite,[
                'next_page_id' => $this->getData('next_page', 'int'),
                'show_loader' => 1
            ]);
        }

        return $this->layout;
    }

}