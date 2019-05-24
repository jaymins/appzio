<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getTaskSummary {

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

    public function getTaskSummary($data){
        /** @var BootstrapComponent $this */

        $this->data = $data;

        $taskdata = $this->getData('task', 'mixed');

        if(!isset($taskdata->title)){
            return $this->getComponentText('{#missing#}!');
        }

        $frequency = $this->model->convertSecondsToFrequency($taskdata->repeat_frequency, $taskdata->times_frequency);

        $row[] = $this->getComponentText($taskdata->title,array('style' => 'mtask_summary_header_content','uppercase' => true));
        $row[] = $this->getComponentText($frequency,array('style' => 'mtask_summary_header_content_right','uppercase' => true));

        $out[] = $this->getComponentRow($row);
        $out[] = $this->getComponentText($taskdata->description,array('style' => 'mtask_summary_description'));
        unset($row);

        $length = $taskdata->deadline - $taskdata->start_time;
        $length = round($length/86400,0);

        $row[] = $this->getComponentText('{#length#}',array('style' => 'mtask_summary_header_content','uppercase' => true));
        $row[] = $this->getComponentText($length .' {#days#}',array('style' => 'mtask_summary_header_content_right','uppercase' => true));

        $out[] = $this->getComponentRow($row);
        $out[] = $this->getComponentText('{#youll_need_to_complete_all_tasks#}',array('style' => 'mtask_summary_description'));

        return $this->getComponentColumn($out);


    }

}
