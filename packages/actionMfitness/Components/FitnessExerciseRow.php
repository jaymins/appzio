<?php
/**
 * Created by PhpStorm.
 * User: trailo
 * Date: 2.10.18
 * Time: 9:24
 */

namespace packages\actionMfitness\Components;


trait FitnessExerciseRow
{

    public function getFitnessExerciseRow($item, $action = [], $type = 'add')
    {

        $content[] = $this->getComponentText($item['name'], [], [
            'color' => '#ffffff',
            'font-size' => '16',
            'padding' => '20 20 20 20',
            'width' => 'auto'
        ]);

        $content[] = $this->getComponentColumn([
            $this->getComponentImage('swiss8-dots.png', [], [
                'width' => '20'
            ])
        ], [], [
            'text-align' => 'center',
            'width' => 30,
            'vertical-align' => 'middle',
            // 'padding' => '5 5 5 5',
            'margin' => '0 10 0 0'
        ]);

        $content[] = $this->getContentMenuButton($item);

        return $this->getComponentColumn([
            $this->getComponentRow($content, $action, [
                'background-color' => '#8C000000',
            ]),
            $this->getContentMenuItem($item, $type)
        ], [
            'onclick' => $this->getOnclickOpenAction('exercisepreview', false, [
                'id' => 'exercise_id-' . $item['id'],
                'sync_open' => 1,
                'back_button' => 1
            ])
        ], []);
    }

    private function getContentMenuButton($item)
    {
        return
            $this->getComponentColumn([
                $this->getComponentImage('add-calendar-symbol-for-events.png', [
                    'id' => 'show-' . $item['id'],
                ], [
                    'width' => '30', 'opacity' => '0.7'
                ]),
                $this->getComponentImage('cross.png', [
                    'id' => 'hide-' . $item['id'],
                    'onclick' => [
                        $this->getOnclickShowElement('show-' . $item['id'], ['transition' => 'none']),
                        $this->getOnclickHideElement('hide-' . $item['id'], ['transition' => 'none']),
                        $this->getOnclickHideElement('menu-' . $item['id'], ['transition' => 'none']),
                    ],
                    'visibility' => 'hidden'
                ], [
                    'width' => '40',
                    'padding' => '7 7 7 7'
                ])
            ], [
                'onclick' => [
                    $this->getOnclickShowElement('hide-' . $item['id'], ['transition' => 'none']),
                    $this->getOnclickHideElement('show-' . $item['id'], ['transition' => 'none']),
                    $this->getOnclickShowElement('menu-' . $item['id'], ['transition' => 'none']),
                ]
            ], [
                'text-align' => 'center',
                //'width' => $width / 2,
                'vertical-align' => 'middle',
                'padding' => '10 10 10 10',
                'width' => '70',
                'background-color' => '#000000'
            ]);
    }

    private function getContentMenuItem($item, $type)
    {
        if ($type == 'add') {
            $menu[] = $this->getComponentText('{#add_to_calendar#}', [
                'uppercase' => true
            ], [
                'color' => '#ffffff',
                'font-size' => 16,
                'padding' => '15 20 15 20'
            ]);

            $menu[] = $this->themeFormField('{#select_date#}', 'training_start_date', 'opendiv',
                [
                    'div_name' => 'training_start_date',
                    'icon_right' => 'swiss8-icon-calendar-input.png',
                    'value' => date('d.m.Y', time()),
                    'shade' => 'light']);

            $menu[] = $this->themeFormField('{#training_time#}', 'training_time-hour', 'opendiv',
                ['div_name' => 'training_time',
                    'icon_right' => 'swiss8-icon-clock.png',
                    'shade' => 'light',
                    'value' => '8',
                    'value2' => '00',
                    'variable2' => 'training_time-minute']);


            $id = isset($item['id']) ? $item['id'] : 0;

            $menu[] = $this->themeButton(
                '{#add_to_calendar#}', [
                    $this->getOnclickSubmit('Listing/add/exercise_' . $id),
                    $this->getOnclickOpenAction('schedule', false, ['sync_open' => 1])
                ],
                'add-calendar-symbol-for-events.png',
                'black',
                'training_time');
        }

        return $this->getComponentRow([
            $this->getComponentColumn(
                $menu, [], [
                'width' => '100%'
            ])
        ], [
            'id' => 'menu-' . $item['id'],
            'visibility' => 'hidden'
        ], [
            'background-color' => '#373C42',
            'width' => '100%'
        ]);
    }

    private function getTimeFields($exercises_per_day)
    {
        if (empty($exercises_per_day) OR $exercises_per_day == 1) {
            return [
                $this->components->themeFormField('{#training_time#}', 'program_training_time-hour', 'opendiv', [
                    'variable2' => 'program_training_time-minute',
                    'shade' => 'light',
                    'div_name' => 'training_time',
                    'icon_right' => 'swiss8-icon-clock.png'

                ])
            ];
        }

        $output = [];

        for ($i = 1; $i < $exercises_per_day + 1; $i++) {
            $output[] = $this->components->themeFormField('{#training_time_' . $i . '#}', 'program_training_time_' . $i . '-hour', 'opendiv', [
                'variable2' => 'program_training_time_' . $i . '-minute',
                'shade' => 'light',
                'div_name' => 'training_time_' . $i,
                'icon_right' => 'swiss8-icon-clock.png'
            ]);
        }

        return $output;
    }

}