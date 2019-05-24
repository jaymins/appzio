<?php

namespace packages\actionMitems\themes\uiKit\Components;

use Bootstrap\Components\BootstrapComponent;

trait getFilter
{

    public function getFilter(array $params = [])
    {

	    $closeDiv = new \stdClass();
	    $closeDiv->action = 'hide-div';
	    $closeDiv->keep_user_data = 1;

        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';
        $filters = isset($params['filters']) ? $params['filters'] : [];
	    $button_label = isset($params['button_label']) ? $params['button_label'] : '{#select#}';

        return $this->getComponentColumn(array_merge(
            [
                $this->getComponentRow([
                    $this->getComponentText($title, [], [
                        'color' => '#ffffff',
                        'font-size' => '14',
                        'width' => '100%',
                    ]),
                    $this->getComponentImage('cross-sign.png', [
                        'onclick' => $closeDiv
                    ], [
                        'width' => '15',
                        'floating' => '1',
                        'float' => 'right',
                        'margin' => '2 0 0 0'
                    ])
                ], [], [
                    'padding' => '10 20 10 20',
                    'background-color' => '#4a4a4a',
                    'shadow-color' => '#33000000',
                    'shadow-radius' => '1',
                    'shadow-offset' => '0 3',
                    'margin' => '0 0 20 0'
                ]),
                $this->getReminderDivSubtitle($subtitle),
            ],
            $filters,
	        [
                $this->uiKitWideButton($button_label, [
                    'onclick' => [
                        $this->getOnclickSubmit('Statistics/filterData'),
                        $closeDiv
                    ]
                ])
            ]
        ), [], [
            'background-color' => '#ffffff'
        ]);
    }

}