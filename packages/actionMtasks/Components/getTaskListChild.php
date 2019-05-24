<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_string;
use function strtolower;

trait getTaskListChild {

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

    public function getTaskListChild($data,$parameters=array()){
        /** @var BootstrapComponent $this */

        $this->data = $data;
        $tasks = $this->getData('tasks', 'array');

        if(!$tasks){
            return $this->getComponentText('{#no_deals_yet#}',array('style' => 'mtasks_savenote'));
        }

        if($tasks['countered']){
            $out[] = $this->getComponentText('{#countered#}',array('style' => 'mtask_summary_header','uppercase' => true));
            $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

            foreach($tasks['countered'] as $task){
                $out[] = $this->getDeal($task);
            }
        }

        $out[] = $this->getComponentText('{#active#}',array('style' => 'mtask_summary_header','uppercase' => true));
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if(!$tasks['active']){
            $out[] = $this->getComponentText('{#no_active_deals_yet#}',array('style' => 'mtasks_savenote'));
        }

        foreach($tasks['active'] as $task){
            $out[] = $this->getDeal($task);
        }

        $out[] = $this->getComponentText('{#pending_for_approval#}',array('style' => 'mtask_summary_header','uppercase' => true));
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if(!$tasks['proposed']){
            $out[] = $this->getComponentText('{#no_pending_deals_yet#}',array('style' => 'mtasks_savenote'));
        }

        foreach($tasks['proposed'] as $task){
            $out[] = $this->getDeal($task);
        }

        $out[] = $this->getComponentText('{#completed#}',array('style' => 'mtask_summary_header','uppercase' => true));
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if(!$tasks['completed']){
            $out[] = $this->getComponentText('{#no_completed_deals_yet#}',array('style' => 'mtasks_savenote'));
        }

        foreach($tasks['completed'] as $task){
            $out[] = $this->getDeal($task);
        }

        $out[] = $this->getComponentText('{#expired#}',array('style' => 'mtask_summary_header','uppercase' => true));
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if(!$tasks['expired']){
            $out[] = $this->getComponentText('{#no_expired_deals_yet#}',array('style' => 'mtasks_savenote'));
        }

        foreach($tasks['expired'] as $task){
            $out[] = $this->getDeal($task);
        }

        return $this->getComponentColumn($out,array());

    }

    public function getDeal($task){
        $top[] = $this->getComponentText(date('M d, Y',$task['start_time']),array('style' => 'mtask_summary_header'));
        $top[] = $this->getComponentText('{#with#}: ' .$task['username'],array(),array('parent_style' => 'mtask_summary_header_right',
            'floating' => '1','float' => 'right'));

        $out[] = $this->getComponentRow($top);
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if(is_string($task['productphoto'])){
            $row[] = $this->getComponentImage($task['productphoto'],array('style' => 'mtasklist_productpic'));
        }

        if(strlen($task['producttitle']) > 30){
            $product_title = preg_replace('/\s+?(\S+)?$/', '', substr($task['producttitle'], 0, 31)) .'...';
        } elseif(is_string($task['producttitle'])) {
            $product_title = $task['producttitle'];
        } else {
            $product_title = '{#no_product#}';
        }

        if(is_string($task['tasktitle'])) {
            $task_title = $task['tasktitle'];
        } else {
            $task_title = '{#no_title#}';
        }

        $col[] = $this->getComponentText($product_title,array('style' => 'mtasklist_header'));
        $col[] = $this->getComponentText($task_title,array('style' => 'mtasklist_title'));
        $col[] = $this->getComponentText('{#due#}: '.date('M d, Y',$task['deadline']),array('style' => 'mtasklist_date'));

        $width = $this->screen_width - 205;
        $row[] = $this->getComponentColumn($col,array(),array('width' => $width));

        if($task['status'] == 'proposed'){
            $row[] = $this->getComponentImage('hourglass.gif',array('style' => 'mtasklist_statuspic'));
        } else {
            $row[] = $this->getTaskProgress($task);
        }

        $out[] = $this->getComponentRow($row,array(),array('margin' => '10 0 10 0'));

        if($task['proofcount'] < $task['proofs_required']){

            if ($task['status'] == 'active') {
                $onclick = $this->getOnclickRoute('Addproof/default/' . $task['taskid'], true);
                $out[] = $this->getComponentText('{#submit_proof#}', array('style' => 'submit_proof_button', 'onclick' => $onclick, 'uppercase' => true));
            }

        } elseif($task['status'] == 'active'){
            $out[] = $this->getComponentText('{#pending_approval#}',array('style' => 'pending_approval','uppercase' => true));
        }

        if($task['status'] == 'countered'){

            /**
             * @todo need to set the category_id at the moment we set
             * category as string because we do not save it properly in the db
             *
             */
            $onclick = $this->getOnclickOpenAction(
                'taskdetailspopup',
                false,
                array('sync_open' => 1,'open_popup' => 1,'sync_close' => 1,'id' => 'popup_details_id_'.$task['taskid']));
            $out[] = $this->getComponentText('{#edit_countered_deal#}',array('style' => 'submit_proof_button','onclick' => $onclick,'uppercase' => true));
        }


        return $this->getComponentColumn($out,array(),array('background-color' => '#ffffff','margin' => '10 15 10 15'));
    }

    public function getTaskProgress($task){

        $params['track_color'] = '#B2B4B3';
        $params['progress_color'] = '#3EB439';
        $params['style'] = 'task_progress_small';
        $fill = $task['proofcount'] / $task['proofs_required'];

        $out[] = $this->getComponentProgress($fill,$params);
        $of = $this->model->localize('{#of#}');
        $out[] = $this->getComponentText($task['proofcount'] .' '.strtolower($of).' '.$task['proofs_required'],array('style' => 'mtasklist_progresstext'));
        return $this->getComponentColumn($out,array(),array('text-align' => 'center','width' => '65'));
    }

}
