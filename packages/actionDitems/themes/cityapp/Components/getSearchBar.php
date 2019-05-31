<?php

namespace packages\actionDitems\themes\cityapp\Components;

use Bootstrap\Components\BootstrapComponent;
use Bootstrap\Models\BootstrapModel;

trait getSearchBar
{

    public function getSearchBar(array $params)
    {
        /** @var BootstrapComponent $this */
        /** @var BootstrapModel $this ->model */

        if (isset($params['onclick_close'])) {
            $close = $params['onclick_close'];
        } else {
            $close = $this->getOnclickSubmit('publiclisting/default/cancelsearch');
        }

        if (isset($params['onclick_submit'])) {
            $submit = $params['onclick_submit'];
        } else {
            $submit = 'Publiclisting/default/search';
        }

        $row[] = $this->getComponentImage('search-icon-for-field.png', array(), array('height' => '20'));

        if ($this->model->getMenuId() == 'cancelsearch' OR isset($params['filter'])) {
            $val = '';
        } else {
            $val = $this->model->getSubmittedVariableByName('searchterm');
        }

        $fieldparams = array(
            'value' => $val,
            'hint' => (isset($params['hint']) ? $params['hint'] : '{#search#}'),
            'variable' => 'searchterm',
//            'id' => 1,
            'submit_menu_id' => $submit,
            'suggestions_style_row' => 'akit_list_row',
            'suggestions_text_style' => 'akit_list_text',
            'submit_on_entry' => '1',
            //'activation' => 'initially'
        );

        if (isset($params['filter'])) {
            $fieldparams['filter_on_entry'] = 1;
            unset($fieldparams['submit_menu_id']);
            unset($fieldparams['submit_on_entry']);
        }

        $row[] = $this->getComponentFormFieldText($val, $fieldparams, [
            'parent_style' => 'akit_searchbox_text',
            'font-size' => 13
        ]);

        if (!isset($params['filter'])) {
            $right[] = $this->getComponentLoader(array('style' => 'akit_loader', 'visibility' => 'onloading'));
            // $right[] = $this->getComponentImage('uikit-delete-icongrey.png', array('onclick' => $close), array('width' => '20'));
            $row[] = $this->getComponentRow($right, array(), array('margin' => '0 0 0 0', 'floating' => 1, 'float' => 'right'));
        }

        return $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentRow($row, [], [
                    'vertical-align' => 'middle',
                ])
            ], [], [
                'width' => '65%',
                'border-radius' => '25',
                'background-color' => '#f5f5f5',
                'padding' => '5 15 5 15',
            ]),
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('Всички категории', [], [
                        'font-size' => 12,
                        'padding' => '0 10 0 10',
                        'color' => '#797979'
                    ]),
                    $this->getComponentImage('cityapp-arrow-down-gray.png', [], [
                        'width' => 6
                    ])
                ], [], [
                    'text-align' => 'right',
                    'vertical-align' => 'middle',
                ]),
            ], [], [
                'text-align' => 'right',
                'vertical-align' => 'middle',
            ]),
        ], [], [
            'vertical-align' => 'middle',
            'margin' => '0 15 0 15',
        ]);
    }

}