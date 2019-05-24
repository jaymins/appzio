<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getLargeProgress {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getLargeProgress(array $tasks){
        /** @var BootstrapComponent $this */

        $params['track_color'] = '#B2B4B3';
        $params['progress_color'] = '#3EB439';
        $params['style'] = 'task_progress_big';
        $params['text_content'] = $tasks['proofcount'] .' {#of#} '. $tasks['proofs_required'] .' {#required_accepted#}';
        $fill = $tasks['proofcount'] / $tasks['proofs_required'];
        return $this->getComponentProgress($fill,$params);

    }

}
