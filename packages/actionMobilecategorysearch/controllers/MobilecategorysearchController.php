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
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MobilecategorysearchController extends ArticleController {

    public $data;
    public $theme;

    public function tab1(){
        $this->data = new StdClass();

        if($this->menuid == 'search'){
            $this->saveVariables();
            $this->no_output = true;
            return $this->data;
        }

        $this->data->scroll[] = $this->getText('{#search_parameters#}',array('style' => 'form-field-section-title'));
        $this->data->scroll[] = $this->formkitSlider('{#distance#} ({#km#})','temp_distance','10000',50,10000,10);

        $listparams['variable'] = 'temp_gendersearch';
        $listparams['value'] = array('men' => '1','women' => 1);
        $interests = array('men'=>'{#men#}','women'=>'{#women#}');
        $this->data->scroll[] = $this->formkitTags('{#genders#}:',$interests,$listparams);

        $listparams['variable'] = 'temp_interests';
        $interests = array('japanese_food'=>'{#japanese#}','chinese_food'=>'{#chinese#}','deserts' => '點心', 'buffet_food' => '{#buffet#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#food_and_tasting#}',$interests,$listparams);
        $interests = array('tennis'=>'{#tennis#}','volleyball'=>'{#volleyball#}','basketball' => '{#basketball#}','sport_chinese' => '羽毛球');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#sport#}',$interests,$listparams);
        $interests = array('offroad'=>'{#offroad#}','sports_cars'=>'{#sports_car#}','motorbikes' => '{#motorbike#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#automobile#}',$interests,$listparams);
        $interests = array('gardening'=>'{#gardening#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#others#}',$interests,$listparams);

        $buttonparams = array('background-color' => $this->color_topbar,'height' => '45','vertical-align' => 'middle','text-align' => 'center');
        $textparams = array('color' => '#ffffff');

        $onclick1 = new stdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'search';

        $onclick2 = new stdClass();
        $onclick2->action = 'open-branch';
        $onclick2->sync_open = 1;
        $onclick2->action_config = $this->getConfigParam('main_branch');

        $this->data->footer[] = $this->getButtonWithIcon('seach-icon-ios.png','search','{#search#}',$buttonparams,$textparams,array($onclick1,$onclick2));
        return $this->data;
    }

}