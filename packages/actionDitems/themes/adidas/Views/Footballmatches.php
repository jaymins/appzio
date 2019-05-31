<?php

namespace packages\actionDitems\themes\adidas\Views;

use Bootstrap\Views\BootstrapView;
// use packages\actionDitems\themes\adidas\Components\Components as Components;
use packages\actionDitems\themes\adidas\Models\Model as ArticleModel;

class Footballmatches extends BootstrapView
{

    /* @var ArticleModel */
    public $model;

    public function tab1() {
        $this->layout = new \stdClass();

        if($this->model->getSavedVariable('role') == 'fan'){
            $this->layout->header[] = $this->uiKitTabNavigation(array(
                array(
                    'text' => strtoupper('{#home#}'),
                    'onclick' => $this->getOnclickOpenAction('fanhome',false,['transition' => 'none']),
                    'active' => 0
                ),
                array(
                    'text' => strtoupper('{#matches#}'),
                    'onclick' => $this->getOnclickOpenAction('matches', false, array(
                        'transition' => 'none'
                    )),
                    'active' => 1
                ),
                array(
                    'text' => strtoupper('{#fan_shop#}'),
                    'onclick' => $this->getOnclickOpenAction('fanshop', false, array(
                        'transition' => 'none'
                    )),
                    'active' => 0
                )
            ));

        } else {
            $this->layout->header[] = $this->uiKitTabNavigation(array(
                array(
                    'text' => strtoupper('{#my_venues#}'),
                    'onclick' => $this->getOnclickOpenAction('venuehome',false,['transition' => 'none']),
                    'active' => 0
                ),
                array(
                    'text' => strtoupper('{#matches#}'),
                    'onclick' => $this->getOnclickOpenAction('venuematches', false, array(
                        'transition' => 'none'
                    )),
                    'active' => 1
                ),
                array(
                    'text' => strtoupper('{#my_events#}'),
                    'onclick' => $this->getOnclickOpenAction('mytodos', false, array(
                        'transition' => 'none',
                    )),
                    'active' => 0
                )
            ));

        }

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#select_the_match#}');

        $this->getButtons($this->getFootballMatches());

        $this->layout->footer[] = $this->uiKitButtonHollow('{#create_event#}', array(
            'onclick' => $this->getOnclickRoute('Addevent/default', false)
        ), array('margin' => '15 80 15 80'));

        return $this->layout;
    }
    
    private function getFootballMatches() {
        
        $matches = $this->getData('football_matches', 'mixed');

        if ( empty($matches) ) {
            return false;
        }

        $output = [];

        foreach ($matches as $match) {
            
            $output[] = [
                'id' => $match['id'],
                'localteam' => $match['localteam_name'],
                'visitorteam' => $match['visitorteam_name'],
                'date' => strtotime($match['formatted_date']),
            ];
        }

        return $output;
    }

    private function getButtons( $items, $type = 'radio' ) {

        if ( empty($items) ) {
            $this->layout->scroll[] = $this->uiKitInfoTileTitle('Missing football matches');
            return true;
        }

        $nameList = 'football_match';
        $prefix = 'var';

        foreach ($items as $item) {
            $checkbox = [];

            $variable = $nameList . '-' . $item['id'];

            $value = 1;
            $active = 0;

            if ( $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                $active = 1;
            }

            if ( $type == 'radio' ) {
                $variable = $nameList;
                $value = $item['id'];

                if ( $value == $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                    $active = 1;
                }
            }

            $icon = 'adidas-selected-state.png';

            $selectstate = array(
                'variable' => $prefix . $variable,
                'style_content' => array(
                    'background-image' => $this->getImageFileName($icon),
                    'background-size' => 'contain',
                    'width' => '30',
                    'height' => '30',
                    'text-align' => 'center',
                    'vertical-align' => 'middle',
                ),
                'allow_unselect' => 1,
                'variable_value' => $value,
                'animation' => 'fade',
                'active' => $active,
            );

            $checkbox[] = $this->getComponentText(' ', array(
                'variable' => $prefix . $variable,
                'allow_unselect' => 1,
                'variable_value' => 0,
                'selected_state' => $selectstate
            ), array(
                'border-color' => '#000000',
                'border-radius' => '15',
                'width' => '30',
                'height' => '30',
                'vertical-align' => 'middle',
            ));

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText($item['localteam'] . ' - ' . $item['visitorteam'], [], [
                    'text-align' => 'left',
                    'font-size' => '14',
                    'color' => '#646a6e',
                    'width' => '60%',
                    'vartical-align' => 'middle',
                ]),
                $this->getComponentText(date('M d', $item['date']), [], [
                    'text-align' => 'left',
                    'font-size' => '14',
                    'font-weight' => 'bold',
                    'color' => '#646a6e',
                    'width' => '20%',
                    'padding' => '0 0 0 10',
                    'vartical-align' => 'middle',
                ]),
                $this->getComponentColumn($checkbox, array(), array(
                    'color' => '#161616',
                    'vertical-align' => 'middle',
                    'text-align' => 'right',
                    'padding' => '10 0 10 0',
                    'width' => 'auto',
                ))
            ], [], [
                'margin' => '5 15 5 15',
            ]);

            $this->layout->scroll[] = $this->getComponentDivider([
                'background' => '#dddddd'
            ]);

        }

        return true;
    }

}