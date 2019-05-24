<?php

namespace packages\actionMfitness\Components;

use packages\actionMfitness\Models\ProgramModel;

trait FitnessProgramRow
{

    public function getFitnessProgramRow(ProgramModel $program, array $parameters = array(), array $styles = array()): \stdClass
    {
        return $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText($program->name, [], [
                        'color' => '#ffffff',
                        'font-size' => '16',
                        'padding' => '25 20 25 20',
                        // 'opacity' => '0.9',
                    ]),
                    $this->getComponentImage('swiss8-dots.png', ['priority' => '1'], [
                        'width' => '25',
                        'margin' => '0 20 0 20',
                        'float' => 'right',
                        'floating' => '1'
                    ])
                ], [], [
                    'width' => $this->screen_width,
                    'vertical-align' => 'middle'
                ]),
                $this->getComponentSpacer(1, [], [
                    'background-color' => '#222222',
                ]),
                $this->getComponentSpacer(1, [], [
                    'background-color' => '#030303',
                ]),
            ], [], []),
        ], [
            'onclick' => $this->getOnclickOpenAction('programview', false, [
                'id' => 'program-' . $program->id,
                'sync_open' => 1,
                'back_button' => 1,
            ]),
        ], [
            'width' => $this->screen_width,
            /*'background-image' => $this->getImageFileName('swiss8-shadow-90.png', array(
                'priority' => 1,
                'imgwidth' => '1440',
            )),
            'background-size' => 'cover',*/
        ]);
    }

}