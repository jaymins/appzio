<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class MobilepointstatusController extends ArticleController {

    public $data;

    public function tab1(){

        $this->data = new StdClass();

        $this->loadBranchList();

        if(empty($this->available_branches)){
            $this->available_branches[] = array($this->requireConfigParam('first_branch') => 1);
        }

        $points = Aeplay::getUserPlayPoints($this->playid);
        $ending = false;

        foreach($this->available_branches AS $branch=>$val){
            $branch = @Aebranch::model()->findByPk($branch);
            if(isset($branch->name)){
                $name = $branch->name;
                if(strstr($name,'Arvoitus')){
                    $order = substr($name,0,strpos($name,'.'));
                    $branchid = $branch->id;
                } elseif(strstr($name,'Loppu')){
                    $ending = true;
                }
            }
        }

        if(!isset($order)){
            $order = 1;
            $branchid = $this->requireConfigParam('first_branch');
        }

        $order = $order-1;
        $totalpoints = $order*5;

        if($ending == true){
            $params['style'] = 'reset_btn';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'open-branch';
            $params['onclick']->action_config = $this->requireConfigParam('reset_branch');

            $options['track_color'] = '#FFFFFF';
            $options['progress_color'] = '#000000';
            $options['text_content'] = 'eteneminen';
            $options['style'] = 'status_progress';

            $this->data->scroll[] = $this->getText('Onnittelut!
Selvitit kaikki tehtävät hienosti. Voit parantaa tulostasi aloittamalla pelin alusta.',array('style' => 'ending_text'));

            $col1[] = $this->getText('omat pisteet',array('style' => 'points_text_hint'));
            $col1[] = $this->getText($points,array('style' => 'points_text'));
            $col2[] = $this->getText('maksimi',array('style' => 'points_text_hint'));
            $col2[] = $this->getText($totalpoints,array('style' => 'points_text'));

            $col3[] = $this->getText(' ',array('style' => 'points_text_hint'));
            $col3[] = $this->getText('/',array('style' => 'points_text'));

            $cols[] = $this->getColumn($col1,array('style' => 'col1_points'));
            $cols[] =$this->getColumn($col3,array('style' => 'col3_points'));
            $cols[] = $this->getColumn($col2,array('style' => 'col2_points'));

            $this->data->scroll[] = $this->getRow($cols,array('style' => 'pointsrow'));

            $params['style'] = 'pelaa_btn';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'open-branch';
            $params['onclick']->config = $this->requireConfigParam('reset_branch');
            $params['onclick']->action_config = $this->requireConfigParam('reset_branch');

            $this->data->scroll[] = $this->getImage('aloita-alusta2.png',$params);

        } else {
            $params['style'] = 'reset_btn';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'open-branch';
            $params['onclick']->action_config = $this->requireConfigParam('reset_branch');
            $this->data->scroll[] = $this->getImage('aloita-alusta.png',$params);

            $options['track_color'] = '#FFFFFF';
            $options['progress_color'] = '#000000';
            $options['text_content'] = 'eteneminen';
            $options['style'] = 'status_progress';

            $col1[] = $this->getText('omat pisteet',array('style' => 'points_text_hint'));
            $col1[] = $this->getText($points,array('style' => 'points_text'));

            $col2[] = $this->getText('maksimi',array('style' => 'points_text_hint'));
            $col2[] = $this->getText($totalpoints,array('style' => 'points_text'));

            $col3[] = $this->getText(' ',array('style' => 'points_text_hint'));
            $col3[] = $this->getText('/',array('style' => 'points_text'));

            $cols[] = $this->getColumn($col1,array('style' => 'col1_points'));
            $cols[] =$this->getColumn($col3,array('style' => 'col3_points'));
            $cols[] = $this->getColumn($col2,array('style' => 'col2_points'));

            $this->data->scroll[] = $this->getRow($cols,array('style' => 'pointsrow'));
            $this->data->scroll[] = $this->getProgress($order/38,$options);

            $params['style'] = 'pelaa_btn';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'open-branch';
            $params['onclick']->config = $branchid;
            $params['onclick']->action_config = $branchid;

            $this->data->scroll[] = $this->getImage('pelaa.png',$params);

        }


        return $this->data;
    }



}