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

class MobileversionupdateView extends MobileversionupdateController {

    public $data;
    public $theme;
    public $availability;

    public function tab1(){
        $this->data = new StdClass();
        $versioncheck = $this->versionCheck();

        if($versioncheck){
            $this->data->scroll[] = $this->getFullPageLoader();
            $this->data->onload[] = $this->getOnclick('complete-action',true);
            return $this->data;
        } else {
            $this->upgradeScreen();
        }

        return $this->data;
    }

    public function upgradeScreen(){
        $url = $this->dataobj->getLinks();

        if($this->client_device == 'client_iphone') {
            $click = $this->getOnclick('url', false, $url['appstore']);
        } else {
            $click = $this->getOnclick('url', false, $url['playstore']);
        }

        $image[] = $this->getImage($this->getActionImage(),array('width' => '125'));

        $this->data->scroll[] = $this->getRow($image,array('text-align' => 'center'));
        $this->data->scroll[] = $this->getText($this->getConfigParam('msg'),array('margin' => '10 20 10 20','text-align' => 'center'));

        $icon[] = $this->getImage($this->getAppIcon(),array('width' => '70', 'border' => '1' ,
            'border-color' => '#999999','border-radius' => '15','margin' => '20 0 10 0','onclick' => $click));
        $this->data->scroll[] = $this->getRow($icon,array('text-align' => 'center'));

        if($this->client_device == 'client_iphone'){
            $this->data->scroll[] = $this->getText('{#download_from_appstore#}',array('text-align' => 'center','margin' => '5 10 10 10','onclick' => $click));
        } else {
            $this->data->scroll[] = $this->getText('{#download_from_playstore#}',array('text-align' => 'center','margin' => '10 10 10 10','onclick' => $click));
        }

        $this->data->footer[] = $this->getTextbutton('{#check_installed_version#}',array('id' => 'check'));
    }

}