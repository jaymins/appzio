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
Yii::import('application.modules.aelogic.packages.actionMobileversionupdate.views.*');

class MobileversionupdateController extends ArticleController {

    /* @var MobileversionupdateModel */
    public $dataobj;

    public function init(){
        $this->dataobj = new MobileversionupdateModel();
        $this->dataobj->factoryInit($this);
    }


    public function versionCheck(){
        $target = $this->getConfigParam('minimum_client_version');
        
        if($target > $this->app_version){
            return false;
        }

        return true;
    }

    public function getActionImage(){
        $image_file = $this->getConfigParam('actionimage1');

        if(!$image_file){
            $image_file = 'upgrade.png';
        }

        return $image_file;

    }

    public function getAppIcon(){
        return Controller::getDomain($this->gid) .'/documents/games/' .$this->gid .'/mobile/client-assets/app_icon_1024x1024.png';
    }

}