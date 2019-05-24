<?php

/*

    This is the main controller of the action.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileclassifieds.models.*');

class MobileclassifiedsController extends ArticleController {

    public $data;
    public $theme;
    public $categoriesModel;

    /* @var  ItemsMobileclassifiedsModel */
    public $itemsModel;
    public $filterModel;
    public $favouriteitemsModel;
    public $dataobj;


    /* this will create the model and feed the contents of the controller to the model
       This way its easy to access things like getSavedVariable and other data helpers
    */

    public function init(){
        $this->dataobj = new MobileclassifiedsModel();
        $this->dataobj->factoryInit($this);
        $this->initModel();
    }


    public function initModel()
    {
        $this->categoriesModel = new CategoriesMobileclassifiedsModel();
        $this->itemsModel = new ItemsMobileclassifiedsModel();
        $this->itemsModel->factoryInit($this);
        $this->filterModel = new FilterMobileclassifiedsModel();
        $this->filterModel->factoryInit($this);
        $this->favouriteitemsModel = new FavouriteitemsMobileclassifiedsModel();
        $this->favouriteitemsModel->factoryInit($this);
    }

    protected function getFilter()
    {
        if ( isset($this->menus['filtering']) ) {
            $this->rewriteActionConfigField('menu_id',$this->menus['filtering']);
        }
    }

    protected function getSearch ()
    {
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';
        $row[] = $this->getImage('search_icon.png', [
            'height' => '40',
            'padding' => '10 5 10 10',
            'background-color' => '#DF4B3F',
        ]);

        $row[] = $this->getFieldtext($value,array(
            'width' => $this->screen_width,
            'vertical-align' => 'middle',
            'color' => '#FFFFFF',
            'background-color' => '#DF4B3F',
            'hint' => '{#Search_Item#}',
            'submit_menu_id' => 'searchbox',
            'variable' => 'searchterm',
            //'id' => 'something',
            'submit_on_entry' => '1',
        ));
        $col[] = $this->getRow($row);

        $this->data->header[] = $this->getRow($col, [
            'background-color' => $this->color_topbar
        ]);

//        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
    }

    protected function clearSavedPictureVariables()
    {
        for ($i = 1; $i <= 8; $i++) {
            $this->saveVariable( 'pic' . $i, '');
        }
    }

    protected function toggleFavouriteItem()
    {
        if (preg_match('~favourite~', $this->menuid)) {
            $id = str_replace('favourite-', '', $this->menuid);
            $this->favouriteitemsModel->toggleFavouriteItem($id);
        }
    }
}