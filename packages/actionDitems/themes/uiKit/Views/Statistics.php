<?php

namespace packages\actionDitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Models\Model as ArticleModel;
use packages\actionDitems\themes\uiKit\Components\Components as Components;

class Statistics extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \StdClass;

        $this->getTabs(1);

        $export_generated = $this->getData('export_generated', 'mixed');

        if ($export_generated) {
            $this->layout->onload[] = $this->getOnclickShowDiv(
                'export_generated',
                $this->getDivParams(15, 15)
            );
        }

        $this->layout->header[] = $this->getComponentRow([
            $this->getComponentText('{#latest_visits#}', [], [
                'font-size' => 17,
                'color' => '#333333',
            ]),
            $this->getComponentRow([
                $this->getComponentText('{#filter_results#}', [], [
                    'font-size' => 12,
                    'padding' => '0 5 0 0',
                    'color' => '#333333',
                ]),
                $this->getComponentImage('uikit-icon-filter.png', [], [
                    'width' => 20,
                ]),
            ], [
                'onclick' => $this->getOnclickShowDiv('filter_results', $this->getDivParams())
            ], [
                'vertical-align' => 'middle',
                'floating' => 1,
                'float' => 'right',
            ])
        ], [], [
            'width' => 'auto',
            'vertical-align' => 'middle',
            'padding' => '15 15 15 15',
        ]);

        $this->layout->header[] = $this->getComponentDivider();

        $this->layout->header[] = $this->getTableHeaders([
            '{#country#}',
            '{#user#}',
            '{#team#}',
            '{#date#}'
        ]);

        $this->getVisits();

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'icon-export.png',
                'onclick' => $this->getOnclickSubmit('Statistics/export')
            ],
        ], true);

        return $this->layout;
    }

    protected function getTabs($tab)
    {
        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#latest_visits#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $tab == 1
            ),
            array(
                'text' => strtoupper('{#top_5#}'),
                'onclick' => $this->getOnclickOpenAction('top5', false, [
                    'transition' => 'none'
                ]),
                'active' => $tab == 2
            )
        ));
    }

    private function getVisits()
    {
        $visits = $this->getData('visits', 'array');

        if (empty($visits)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_visits_yet#}', [], [
                'text-align' => 'center',
                'font-size' => 17,
                'color' => '#333333',
                'padding' => '15 15 15 15',
            ]);
        }

        $this->layout->scroll[] = $this->getVisitsList($visits);
    }

    protected function getTableHeaders($items)
    {
        $tabs = [];

        $width = round(100 / count($items), 2);

        foreach ($items as $item) {
            $tabs[] = $this->getComponentColumn([
                $this->getComponentText($item, [], [
                    'color' => '#333333',
                    'padding' => '8 10 8 10'
                ])
            ], [], [
                'width' => $width . '%',
                'text-align' => 'center',
            ]);
        }

        return $this->getComponentColumn([
            $this->getComponentRow($tabs, [], [
                'background-color' => '#d8d8d8'
            ]),
            $this->getComponentDivider()
        ]);
    }

    private function getVisitsList(array $content)
    {
        $rows = array();
        $is_scrollable = false;
        $next_page_id = 1;

        if (count($content) > 10) {
            $chunks = array_chunk($content, 10, true);

            if (isset($_REQUEST['next_page_id'])) {
                $next_page_id = $_REQUEST['next_page_id'] + 1;
            }

            $data = (isset($chunks[$next_page_id - 1]) ? $chunks[$next_page_id - 1] : array());
            $is_scrollable = true;
        } else {
            $data = $content;
        }

        foreach ($data as $item) {
            $name = isset($item->extra_data['real_name']) ? $item->extra_data['real_name'] : 'N/A';

            $rows[] = $this->getComponentRow([
                $this->getComponentColumn([
                    $this->getComponentText(($item->country ? $item->country : 'N/A'), [], [
                        'color' => '#333333',
                        'font-size' => '13',
                        'padding' => '8 10 8 10'
                    ])
                ], [], [
                    'width' => '25%',
                    'text-align' => 'center',
                ]),
                $this->getComponentColumn([
                    $this->getComponentText($name, [], [
                        'color' => '#333333',
                        'font-size' => '13',
                        'padding' => '8 10 8 10'
                    ])
                ], [], [
                    'width' => '25%',
                    'text-align' => 'center',
                ]),
                $this->getComponentColumn([
                    $this->getComponentText($item->name, [], [
                        'color' => '#333333',
                        'font-size' => '13',
                        'padding' => '8 10 8 10'
                    ])
                ], [], [
                    'width' => '25%',
                    'text-align' => 'center',
                ]),
                $this->getComponentColumn([
                    $this->getComponentText(date('d.m.Y', $item->date_added), [], [
                        'color' => '#333333',
                        'font-size' => '13',
                        'padding' => '8 10 8 10'
                    ])
                ], [], [
                    'width' => '25%',
                    'text-align' => 'center',
                ]),
            ], [], array(
                'padding' => '6 8 6 8',
            ));

            $rows[] = $this->getComponentDivider();
        }

        if ($is_scrollable) {
            if (empty($rows)) {
                $rows[] = $this->getComponentText('{#no_more_results#}', array(
                    'style' => 'uikit_search_noresults'
                ));
                $next_page_id--;
            }

            return $this->getInfiniteScroll($rows, array('next_page_id' => $next_page_id));
        } else {
            return $this->getComponentColumn($rows, array(), array(
                'margin' => '0 0 15 0'
            ));
        }
    }

    private function getFilterByCountry()
    {
        return [
            $this->getComponentColumn([
                $this->getComponentDivider(),
                $this->getComponentFormFieldText('', array(
                    'hint' => '{#select_country#}',
                    'variable' => 'filter_select_country',
                    'value' => '',
                    'id' => 'filter_select_country',
                    'suggestions_update_method' => 'getcoutries',
                    'suggestions' => [],
                    'suggestions_placeholder' => $this->getComponentText('$value', array(), array(
                        'font-size' => 15,
                        'color' => '#333333',
                        'background-color' => '#ffffff',
                        'padding' => '12 10 12 10',
                    )),
                    'token_placeholder' => $this->getComponentRow([
                        $this->getComponentText('$value', [], [
                            'color' => '#000000',
                            'padding' => '3 5 3 5',
                        ])
                    ], [], [
                        'background-color' => '#f6f6f6',
                        'padding' => '3 3 3 3',
                        'margin' => '3 0 3 0',
                        'border-radius' => '5',
                    ]),
                ), array(
                    'padding' => '0 0 0 0',
                    'margin' => '10 20 10 20',
                )),
                $this->getComponentDivider(),
            ], [], [
                'margin' => '0 0 10 0'
            ])
        ];
    }

    private function getFilterByDate()
    {
        return [
            $this->getComponentRow([
                $this->getComponentColumn(array(
                    $this->getCalendarSubtitle('{#from#}'),
                    $this->getComponentCalendar([
                        'date' => $this->model->getSavedVariable('filter_time_from'),
                        'variable' => 'filter_time_from',
                        'selection_style' => array(
                            'color' => '#ffffff',
                            'background-color' => '#FFCC00'
                        ),
                    ], [
                        'width' => '100%',
                    ])
                ), array(), array(
                    'width' => '50%',
                    'padding' => '0 5 0 0'
                )),
                $this->getComponentColumn(array(
                    $this->getCalendarSubtitle('{#to#}'),
                    $this->getComponentCalendar([
                        'date' => $this->model->getSavedVariable('filter_time_to'),
                        'variable' => 'filter_time_to',
                        'selection_style' => array(
                            'color' => '#ffffff',
                            'background-color' => '#FFCC00'
                        ),
                    ], [
                        'width' => '100%',
                    ])
                ), array(), array(
                    'width' => '50%',
                    'padding' => '0 0 0 5'
                ))
            ], [], [
                'margin' => '0 5 20 5'
            ])
        ];
    }

    protected function getSelectedCountries()
    {

        $countries = $this->getData('selected_countries', 'array');

        if (empty($countries)) {
            return [];
        }

        $items = [];

        foreach ($countries as $i => $country) {
            $items[] = $this->getComponentRow([
                $this->getComponentText($country, [
                    'style' => 'item_tag_text'
                ]),
                $this->getComponentImage('cancel-icon-dev.png', [
                    'style' => 'item_tag_image_delete',
                    'onclick' => $this->getOnclickSubmit('Statistics/removeCountry/' . $country)
                ]),
            ], [
                'style' => 'item_tag_wrapper'
            ]);
        }

        return [
            $this->getComponentWrapRow($items, [], [
                'padding' => '10 15 20 15'
            ])
        ];
    }

    private function getCalendarSubtitle($subtitle)
    {
        return $this->getComponentText($subtitle, array(), array(
            'text-align' => 'center',
            'color' => '#333333',
            'font-size' => '17',
            'font-weight' => 'bold',
            'margin' => '0 0 10 0'
        ));
    }

    public function getDivs()
    {
        $closeDiv = new \stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->keep_user_data = 1;

        $divs['export_generated'] = $this->getComponentColumn([
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('{#report_is_generated#}', [], [
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
                $this->getComponentText('{#your_report_is_being_generated#}.', [], [
                    'font-size' => 18,
                    'text-align' => 'center',
                    'padding' => '0 15 10 15',
                ]),
                $this->getComponentText('{#it_will_be_sent_via_email_shortly#}.', [], [
                    'font-size' => 18,
                    'text-align' => 'center',
                    'padding' => '0 15 10 15',
                ]),
                $this->getComponentRow([
                    $this->uiKitWideButton('{#close#}', [
                        'onclick' => $closeDiv
                    ])
                ], [], [
                    'width' => '100%',
                    'floating' => 1,
                    'vertical-align' => 'bottom',
                    'margin' => '0 0 5 0',
                ])
            ], [], [
                'height' => '300',
                'border-width' => '1',
                'border-color' => '#4a4a4a',
                'background-color' => '#ffffff',
            ])
        ]);

        $divs['filter_results'] = $this->components->getFilter([
            'title' => '{#filter_latest_results#}',
            'button_label' => '{#select#}',
            'filters' => array_merge(
                $this->getFilterByCountry(),
                $this->getSelectedCountries(),
                $this->getFilterByDate()
            ),
        ]);

        $divs['filter_month'] = $this->components->getPicker([
            'title' => '{#select_month#}',
            'button_label' => '{#select#}',
            'variable' => 'filter_month',
            'data' => 'January;January;February;February;March;March;April;April;May;May;June;June;July;July;August;August;September;September;October;October;November;November;December;December',
            'default' => $this->model->getSavedVariable('filter_month', date('F')),
            'close_action' => $this->getOnclickSubmit('Statisticstop/saveFilterMonth')
        ]);

        $divs['filter_year'] = $this->components->getPicker([
            'title' => '{#select_year#}',
            'button_label' => '{#select#}',
            'variable' => 'filter_year',
            'data' => $this->getYears(),
            'default' => $this->model->getSavedVariable('filter_year', date('Y')),
            'close_action' => $this->getOnclickSubmit('Statisticstop/saveFilterYear')
        ]);

        return $divs;
    }

    protected function getDivParams($left = 0, $right = 0)
    {
        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = $left;
        $layout->right = $right;

        return [
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        ];
    }

    private function getYears()
    {
        $start_year = 2017;
        $end_year = date('Y');

        $years = range($start_year, $end_year);

        $output = '';

        foreach ($years as $year) {
            $output .= $year . ';' . $year . ';';
        }

        return substr($output, 0, -1);
    }

}