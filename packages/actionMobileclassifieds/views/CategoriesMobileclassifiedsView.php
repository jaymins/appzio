<?php

/*

    Layout code codes here. It should have structure of
    $this->data->scroll[] = $this->getElement();

    supported sections are header,footer,scroll,onload & control
    and they should always be arrays

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class CategoriesMobileclassifiedsView extends MobileclassifiedsView {

    public $data;
    public $theme;

    public function tab1(){
        $this->data = new StdClass();

        if ( preg_match( '~selected-category-~', $this->menuid ) ) {
            $selected_category_id = str_replace( 'selected-category-', '', $this->menuid );
            $this->data->scroll[] = $this->getText($selected_category_id);
        }

        $categories = $this->categoriesModel->getCategories();

        foreach ($categories as $key => $category) {
            $this->data->scroll[] = $this->getHairline('#CCCCCC');
            $this->data->scroll[] = $this->renderCategory($category);
        }

        $this->data->scroll[] = $this->getHairline('#CCCCCC');

        return $this->data;
    }

    private function renderCategory($category)
    {
        $clicker = new StdClass();
        $clicker->action = 'close-popup';
        $clicker->keep_user_data = 1;

        $update_params = array(
            $this->getVariableId('category') => $category['name']
        );

        $clicker->set_variables_data = (object)$update_params;

        return $this->getText($category['name'], [
                'padding' => '10 10 10 10',
                'color' => '#555555',
                'onclick' => $clicker
            ]);
    }
}