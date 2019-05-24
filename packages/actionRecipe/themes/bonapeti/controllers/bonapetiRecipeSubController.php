<?php

class bonapetiRecipeSubController extends RecipeController
{

    public $fields = array(
        'access_token', 'token_scope', 'refresh_token',
    );

    public $tabsmenu_images = array();
    public $mode;
    public $token;

    public $galleryobj;
    public $servings = 0;
    public $tabmode = 'top';
    public $debug = false;

    public $recipe;
    public $async_upload = false;

    public function init()
    {

        $this->mode = $this->getConfigParam('action_mode');
        $this->token = $this->getVariable('access_token');

        if ($this->mode == 'recipe') {
//            $this->tabsmenu_images = array(
//                '1' => array('recipe-tab-bon-1.png','25%'),
//                '2' => array('recipe-tab-bon-2.png','25%'),
//                '3' => array('recipe-tab-bon-3.png','25%'),
//                '4' => array('recipe-tab-bon-4.png','25%')
//            );
        }

    }

    public function tab1()
    {
        $this->data = new StdClass();

        if ($this->mode == 'preparation') {
            $this->showPreparationSteps();
            return $this->data;
        }

        if ($this->mode == 'shopping_list') {

            if (strstr($this->menuid, 'remove-from-list-')) {
                $ingredientKey = str_replace('remove-from-list-', '', $this->menuid);
                $this->removeFromShoppingList($ingredientKey, true);
                $this->flushCacheTab(1);
            }

            $this->showShoppingList();
            return $this->data;
        }

        if (empty($this->token)) {
            $this->data->scroll[] = $this->getText('Missing access token', array('style' => 'recipe_chat_nocomments'));
            return $this->data;
        }

        if ($this->mode == 'listing') {
            $this->showRecipes();
        } else if ($this->mode == 'categories') {
            $this->showCategories();
        } else {
            $this->showSingleRecipe();
        }

        return $this->data;
    }

    public function showCategories()
    {
        $url = 'http://api.bonapeti.bg/services/recipe/categories?access_token=' . $this->token;

        $response = $this->getAPIResponse($url, 'bonapeti-category-');

        if ($response) {

            if (isset($response->error) AND $response->error == 'expired_token') {
                $this->refreshToken();
                $this->token = $this->getVariable('access_token');
                $this->showCategories();
                return true;
            } else {
                $this->renderCategories($response, $url);
                return true;
            }

        }
    }

    public function showRecipes()
    {
        $query_param = false;

        if (preg_match('~view-category~', $this->menuid)) {
            $query_param = $this->menuid;
            $this->saveVariable('category_id_temp', $this->menuid);
        } else {
            $query_param = $this->getVariable('category_id_temp');
        }

        $pieces = explode('|', $query_param);
        $id = str_replace('view-category-', '', $pieces[0]);

        // $url = 'http://api.bonapeti.bg/services/recipe?access_token=' . $this->token . '&page=550';
        $url = 'http://api.bonapeti.bg/services/recipe?access_token=' . $this->token . '&category=' . $id . '&page=50';

        $response = $this->getAPIResponse($url);

        if ($response) {
            if (isset($response->error) AND $response->error == 'expired_token') {
                $this->refreshToken();
                $this->token = $this->getVariable('access_token');
                $this->showRecipes();
                return true;
            } else {
                $this->renderResponse($response, $url);
                return true;
            }
        } else {
            $this->data->scroll[] = $this->getText('За съжаление няма рецепти в тази категория', array('style' => 'recipe_chat_nocomments'));
        }

    }

    public function showSingleRecipe()
    {
        $query_param = false;

        if (preg_match('~view-recipe~', $this->menuid)) {
            $query_param = $this->menuid;
            $this->saveVariable('recipe_id_temp', $this->menuid);
        } else {
            $query_param = $this->getVariable('recipe_id_temp');
        }

        $this->setRecipe($query_param);

        if (strstr($this->menuid, 'add-to-list-')) {
            $ingredientKey = str_replace('add-to-list-', '', $this->menuid);
            $this->addToShoppingList($ingredientKey);
            $this->flushCacheTab(1);
        }

        if (strstr($this->menuid, 'remove-from-list-')) {
            $ingredientKey = str_replace('remove-from-list-', '', $this->menuid);
            $this->removeFromShoppingList($ingredientKey);
            $this->flushCacheTab(1);
        }

        if (empty($this->recipe)) {
            $this->data->scroll[] = $this->getError('Invalid recipe');
            return false;
        }

        /* turn bookmark off */
        if ($this->menuid == 200) {
            $this->removeBookmark();
//            $this->moduleBookmarking('remove');
            $this->flushCacheTab(1);
        }

        /* turn bookmark on */
        if ($this->menuid == 201) {
            $this->saveBookmark();
//            $this->moduleBookmarking('save',array('updatenotifications' => true));
            $this->flushCacheTab(1);
        }

        $this->rewriteActionField('subject', $this->recipe['title']);

        switch ($this->menuid) {
            case 'cooked_this':
                $this->submitImage();
                break;

            case 'general_submit':
                $this->imageConfirmation();
                break;

            default:
                $this->getMainScroll(array());
                break;
        }

        $onclick = new stdClass();
        $onclick->id = 'recipe-' . $this->recipe['recipe_ID'];
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $this->getConfigParam('preparation_info');
        $onclick->config = $this->getConfigParam('preparation_info');
        $onclick->sync_open = 1;

        $this->data->footer[] = $this->getTextbutton('Начин на приготвяне', array(
            'id' => 'id',
            'style' => 'recipe_cta_button',
            'onclick' => $onclick
        ));

    }

    public function removeBookmark()
    {
        $id = $this->recipe['recipe_ID'];
        $bookmarks = json_decode($this->varcontent['bookmarks']);

        if (isset($bookmarks->$id)) {
            unset($bookmarks->$id);
        }

        $this->updateBookmarks($bookmarks);
    }

    public function getBookmarkStatus()
    {
        $id = $this->recipe['recipe_ID'];
        $bookmarks = json_decode($this->varcontent['bookmarks']);

        if ( isset($bookmarks->$id) ) {
            return true;
        }

        return false;
    }

    public function saveBookmark()
    {
        $id = $this->recipe['recipe_ID'];
        $bookmarks = json_decode($this->varcontent['bookmarks']);

        if (is_object($bookmarks)) {
            if (isset($bookmarks->$id)) {
                return true;
            } else {
                $bookmarks->$id = true;
            }
        } else {
            $bookmarks = new StdClass;
            $bookmarks->$id = true;
        }

        $this->updateBookmarks($bookmarks);
    }

    private function updateBookmarks($bookmarks)
    {
        $bookmarks = json_encode($bookmarks);
        AeplayVariable::updateWithName($this->playid, 'bookmarks', $bookmarks, $this->gid);
    }

    public function showPreparationSteps()
    {
        $this->rewriteActionField('subject', 'Начин на приготвяне');

        $query_param = $this->getVariable('recipe_id_temp');
        $this->setRecipe($query_param);

        if (!$this->recipe) {
            return;
        }

        $index = 1;
        foreach ($this->recipe['steps'] as $step) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getColumn(array(
                    $this->getText($index, array('style' => 'recipe_preparation_index'))
                ), array(
                    'background-image' => $step['picture'],
                    'background-size' => 'contain',
                    'width' => '40%',
                    'height' => '100'
                )),
                $this->getText($step['description'], array(
                    'style' => 'preparation_step_description'
                ))
            ), array(
                'padding' => '10 10 10 10'
            ));

            $this->data->scroll[] = $this->getRow(array(
                $this->getHairline('#DADADA')
            ), array(
                'margin' => '0 25 0 25'
            ));

            $index++;
        }
    }

    public function addToShoppingList($key)
    {
        $ingredient = $this->recipe['ingredients'][$key];
        $itemName = $ingredient['prev_text'] . ' ' . $ingredient['product'];
        $recipeName = $this->recipe['title'];

        if (isset($this->varcontent['shopping_list'])) {
            $shoppinglist = json_decode($this->varcontent['shopping_list']);
            if (!is_array($shoppinglist)) {
                $shoppinglist = array();
            }
        } else {
            $shoppinglist = array();
        }

        if (!in_array($itemName, $shoppinglist)) {
            $shoppinglist[] = $itemName;
        }

        AeplayVariable::updateWithName($this->playid, 'shopping_list', json_encode($shoppinglist), $this->gid);
    }

    public function removeFromShoppingList($key, $fromListAction = false)
    {
        $ingredient = $this->recipe['ingredients'][$key];
        $ingredientName = $ingredient['prev_text'] . ' ' . $ingredient['product'];

        $shoppinglist = json_decode($this->varcontent['shopping_list']);

        if ($fromListAction) {
            $index = $key;
        } else {
            $index = array_search($ingredientName, $shoppinglist);
        }

        unset($shoppinglist[$index]);

        $shoppinglist = array_values($shoppinglist);

        AeplayVariable::updateWithName($this->playid, 'shopping_list', json_encode($shoppinglist), $this->gid);
    }

    public function tab2()
    {
        $this->data = new StdClass();

        $this->servings = $this->getPlayState('servings');

        if ($this->menuid > 800 AND $this->menuid < 810) {
            $this->servings = $this->menuid - 800;
            $this->savePlayState('servings', $this->servings);
        }

        if ($this->menuid == 'add-to-list') {
            $this->servings = Yii::app()->cache->get($this->actionid . 'servings', $this->servings);
            $this->addToList($this->servings);
        } else {
            $this->getIngedients($this->servings);
        }

        $this->data->footer[] = $this->getTextbutton('Добави в списъка', array('id' => 'add-to-list', 'style' => 'add-button'));

        return $this->data;
    }

    public function tab3()
    {

        $recipe_id = $this->recipe['recipe_ID'];
        $chat_room_id = 'recipe-chat-' . $recipe_id;

        $this->data = new StdClass();

        $this->data = $this->moduleChat(array(
            'context' => 'action',
            'context_key' => $chat_room_id,
            'disable_header' => true,
        ));

        return $this->data;
    }

    public function tab4()
    {
        $this->data = new StdClass();

        $recipe_id = $this->recipe['recipe_ID'];
        $entries = $this->playdatastorage->findAllByAttributes(array(
            'key' => 'bonapeti-recipe-image-' . $recipe_id
        ));

        if (empty($entries)) {
            $this->data->scroll[] = $this->getText('Be the first one to cook this recipe and receive double points! Go to first tab and click "I cooked this".', array('style' => 'recipe_chat_nocomments'));
        }

        $images = array();

        foreach ($entries as $image) {

            if (empty($image->value)) {
                continue;
            }

            $values = json_decode($image->value);
            $images[] = array(
                'name' => $values->name,
                'date' => $values->date,
                'pic' => $values->pic,
                'comment' => $values->comment,
                'user' => $values->user,
            );
        }

        $gallery_items = $this->moduleGallery(array(
            'images' => json_encode($images),
            'viewer_id' => $this->getConfigParam('gallery_item_id'),
            'dir' => 'images',
        ));

        foreach ($gallery_items as $item) {
            $this->data->scroll[] = $item;
        }

        return $this->data;
    }

    private function givePoints()
    {

        $point1_name = 'points_' . $this->configobj->points_1;
        $point2_name = 'points_' . $this->configobj->points_2;

        if (!isset($this->vars[$point1_name])) {
            Aevariable::addGameVariable($this->gid, $point1_name);
        }

        if (!isset($this->vars[$point2_name])) {
            Aevariable::addGameVariable($this->gid, $point2_name);
        }

        if (isset($this->varcontent[$point1_name])) {
            $newpoints1 = $this->varcontent[$point1_name] + 1;
            AeplayVariable::updateWithName($this->playid, $point1_name, $newpoints1, $this->gid);
        } else {
            $newpoints1 = 1;
            AeplayVariable::updateWithName($this->playid, $point1_name, 1, $this->gid);
        }

        if (isset($this->varcontent[$point2_name])) {
            $newpoints2 = $this->varcontent[$point2_name] + 1;
            AeplayVariable::updateWithName($this->playid, $point2_name, $newpoints2, $this->gid);
        } else {
            $newpoints2 = 1;
            AeplayVariable::updateWithName($this->playid, $point2_name, 1, $this->gid);
        }

        $arr['points1'] = $newpoints1;
        $arr['points2'] = $newpoints2;

        $this->flushCacheTab(3);
        return $arr;
    }

    private function submitImage()
    {

        $this->loadVariables();

        $name = 'upload_temp';

        if (isset($this->varcontent[$name]) AND $this->varcontent[$name]) {
            $pic = $this->varcontent[$name];
        } else {
            $pic = 'photo-placeholder.jpg';
        }

        // $params['imgwidth'] = '600';
        // $params['imgheight'] = '600';
        // $params['margin'] = '10 90 10 90';
        // $params['imgcrop'] = 'yes';
        // $params['crop'] = 'round';

        $params['defaultimage'] = 'photo-placeholder.jpg';
        $params['style'] = 'recipe_top_images';

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'upload-image';
        $params['onclick']->max_dimensions = '600';
        $params['onclick']->variable = $this->getVariableId($name);
        $params['onclick']->action_config = $this->getVariableId($name);
        $params['onclick']->sync_upload = true;

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'photo-placeholder.jpg';
        $params['priority'] = 9;

        $this->data->scroll[] = $this->getImage($pic, $params);
        $this->data->scroll[] = $this->getText('Please upload a photo of your cooking creation to collect the points', array('style' => 'recipe_text_narrow'));
        $this->data->scroll[] = $this->getText('Comment (optional):', array('style' => 'recipe_text_narrow'));
        $this->data->scroll[] = $this->getFieldtextarea('', array('hint' => 'Comment (optional):', 'style' => 'recipe_comment_field_upload', 'variable' => 'comment_temp'));

        $this->data->footer[] = $this->getTextbutton('Запиши снимката', array('style' => 'add-button', 'id' => 'general_submit'));
    }

    private function imageConfirmation()
    {

        $this->loadVariables();
        $this->loadVariableContent(true);

        $this->saveVariables();

        if ($this->async_upload) {
            // $call = 'apps/recipeAsync?actionid=' . $this->actionid .'&playid=' . $this->playid;
            // Controller::asyncAppzioApiCall( $call, $this->gid );
        } else {
            $this->addImageToUser();
            // Yii::import('application.modules.aelogic.packages.actionRecipe.models.SaveVarsModel');
            // SaveVarsModel::saveVars( $this->actionid, $this->playid );
        }

        $points = $this->givePoints();

        $point1 = ucfirst($this->configobj->points_1) . ' points';
        $point2 = ucfirst($this->configobj->points_2) . ' points';

        $this->data->scroll[] = $this->getImage('thumbs-up2.jpg', array('style' => 'recipe_top_images'));
        $this->data->scroll[] = $this->getText('Your photo has been uploaded. It might take a little while for it to show up in the recipe\'s gallery. Here is your updated point tally:', array('style' => 'recipe_text_narrow'));
        $this->data->scroll[] = $this->getText($point1 . ' : ' . $points['points1'] . 'pt', array('style' => 'recipe_points_title'));
        $this->data->scroll[] = $this->getText($point2 . ' : ' . $points['points2'] . 'pt', array('style' => 'recipe_points_title'));
    }

    private function pointsText()
    {

        $time = '60 min';
        $points1 = ($this->getConfigParam('points1') ? $this->getConfigParam('points1') : '1');
        $points2 = ($this->getConfigParam('points2') ? $this->getConfigParam('points2') : '1');

        if (isset($this->configobj->bookmarks)) {
            $likes = $this->configobj->bookmarks;
        } else {
            $likes = 3;
        }

        $output[] = $this->getText($time, array('style' => 'recipe_points_text'));
        $output[] = $this->getText($points1, array('style' => 'recipe_points_text'));
        $output[] = $this->getText($points2, array('style' => 'recipe_points_text'));
        $output[] = $this->getText($likes, array('style' => 'recipe_points_text'));

        return $output;
    }

    private function getMainScroll()
    {
        $assetname = $this->recipe['head_recipe_image'];

        $time = empty($this->recipe['cook_time']) ? 'N/A' : $this->recipe['cook_time'];

        $info[] = $this->getRow(array(
            $this->getImage('icon-clock.png', array(
                'width' => 13,
                'margin' => '2 5 0 0'
            )),
            $this->getText($time . ' минути', array('style' => 'recipe_info_label'))
        ), array(
            'width' => '110'
        ));

        $info[] = $this->getRow(array(
            $this->getImage('icon-eye.png', array(
                'width' => 15,
                'margin' => '3 5 0 0'
            )),
            $this->getText($this->recipe['views'], array('style' => 'recipe_info_label'))
        ), array(
            'width' => '110'
        ));

        $info[] = $this->getRow(array(
            $this->getImage('icon-hearth.png', array(
                'width' => 13,
                'margin' => '2 5 0 0'
            )),
            $this->getText($this->configobj->bookmarks, array('style' => 'recipe_info_label'))
        ), array(
            'width' => '110'
        ));

        $recipeInfo[] = $this->getText(mb_strtoupper($this->recipe['title']), array(
            'color' => '#FFFFFF',
            'font-size' => 17,
            'margin' => '0 0 10 0'
        ));
        $recipeInfo[] = $this->getRow($info);

        // Get like status and set like button parameters
        $bookmarked = $this->getBookmarkStatus();

        $bookmarkOnclick = new stdClass();
        $bookmarkOnclick->action = 'submit-form-content';

        if ($bookmarked) {
            $bookmarkOnclick->id = 200;
            $bookmarkImage = 'liked_recipe.png';
        } else {
            $bookmarkOnclick->id = 201;
            $bookmarkImage = 'like_recipe.png';
        }

        $background[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getImage('play_icon.png', array(
                    'width' => '50'
                ))
            ), array(
                'text-align' => 'center',
                'margin' => '0 0 70 0'
            )),
            $this->getRow(array(
                $this->getColumn($recipeInfo, array(
                    'width' => '80%'
                )),
                $this->getImage($bookmarkImage, array(
                    'width' => '60',
                    'margin' => '0 0 0 0',
                    'onclick' => $bookmarkOnclick
                ))
            ), array(
                'width' => '100%'
            ))
        ), array(
            'background-image' => $this->getImageFileName('shadow-image-wide.png'),
            'background-size' => 'cover',
            'padding' => '100 20 20 20',
            'width' => '100%',
        ));

        $this->data->scroll[] = $this->getColumn($background, array(
            'background-image' => $assetname,
            'background-size' => 'contain'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($this->recipe['author_pic_url'], array(
                'imgcrop' => 'yes',
                'crop' => 'round',
                'defaultimage' => 'anonymous2.png',
                'width' => 40,
                'margin' => '0 10 0 0'
            )),
            $this->getColumn(array(
                $this->getText($this->recipe['author'], array('style' => 'recipe_author_name'))
            ))
        ), array(
            'margin' => '10 25 10 25'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getHairline('#DADADA')
        ), array(
            'margin' => '0 25 0 25'
        ));

        $this->data->scroll[] = $this->getSpacer(10);

        $this->renderIngredientsList();

        $this->data->scroll[] = $this->getSpacer(10);

        $this->data->scroll[] = $this->getRow(array(
            $this->getHairline('#DADADA')
        ), array(
            'margin' => '0 25 0 25'
        ));

        $this->data->scroll[] = $this->getSpacer(10);

        $this->data->scroll[] = $this->getText('Коментари:', array('style' => 'recipe_section_title'));
    }

    public function renderIngredientsList()
    {
        $ingredients[] = $this->getText('Продукти:', array('style' => 'recipe_section_title'));

        if (isset($this->varcontent['shopping_list'])) {
            $shoppinglist = json_decode($this->varcontent['shopping_list']);
            if (!is_array($shoppinglist)) {
                $shoppinglist = array();
            }
        } else {
            $shoppinglist = array();
        }

        foreach ($this->recipe['ingredients'] as $key => $ingredient) {

            $ingredientName = $ingredient['prev_text'] . ' ' . $ingredient['product'];

            if (!in_array($ingredientName, $shoppinglist)) {
                $icon = 'plus_icon.png';
                $id = 'add-to-list-' . $key;
            } else {
                $icon = 'minus_icon.png';
                $id = 'remove-from-list-' . $key;
            }

            $onclick = new stdClass();
            $onclick->id = $id;
            $onclick->action = 'submit-form-content';

            $ingredients[] = $this->getRow(array(
                $this->getImage($icon, array(
                    'width' => '15',
                    'onclick' => $onclick
                )),
                $this->getText($ingredient['prev_text'] . ' ' . $ingredient['product'], array(
                    'font-size' => 14,
                    'font-weight' => 'bold',
                    'margin' => '0 0 0 10'
                ))
            ), array(
                'margin' => '5 0 0 0'
            ));
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn($ingredients),
            $this->getColumn(array(
                $this->getText(12, array(
                    'background-image' => $this->getImageFileName('portions.png'),
                    'background-size' => 'contain',
                    'height' => '40',
                    'width' => '40',
                    'margin' => '0 0 10 0',
                    'padding' => '12 0 0 0',
                    'text-align' => 'center',
                    'font-size' => '13',
                    'color' => 'AFC82C',
                    'font-ios' => 'Roboto',
                )),
                $this->getImage('shopping_list.png', array(
                    'height' => '40',
                    'width' => '40',
                    'margin' => '0 0 0 0'
                ))
            ))
        ), array(
            'margin' => '5 25 0 25'
        ));
    }

    public function renderSteps()
    {

        if (!isset($this->recipe['steps']) OR empty($this->recipe['steps'])) {
            return false;
        }

        $steps = $this->recipe['steps'];

        foreach ($steps as $i => $step) {

            if (!isset($step['description']) OR empty($step['description'])) {
                continue;
            }

            $description = $step['description'];
            $picture = $step['picture'];
            // http://www.bonapeti.bg/

            $this->data->scroll[] = $this->getRow(array(
                $this->getImage('step-indicator.png', array('width' => '100%')),
                $this->getText($i + 1, array('width' => '100%', 'floating' => '1', 'text-align' => 'center', 'vertical-align' => 'middle', 'color' => '#ffffff', 'font-size' => 20, 'font-weight' => 'bold', "font-ios" => "Lato-Regular", "font-android" => "Lato-Regular",))
            ));

            $description = html_entity_decode($description);
            $this->data->scroll[] = $this->getText(strip_tags($description), array('style' => 'recipe_step_text'));
        }

    }

    private function getIngredientsMenu($servings)
    {

        $params['width'] = '33%';

        if ($servings == 1) {
            $columns[] = $this->getImage('btn-minus-off.png', $params);
        } else {
            $columns[] = $this->getImagebutton('btn-minus.png', 800 + $servings - 1, false, $params);
        }

        $columns[] = $this->getImage('servings-' . $servings . '.png', $params);

        if ($servings == 8) {
            $columns[] = $this->getImage('btn-plus-off.png', $params);
        } else {
            $columns[] = $this->getImagebutton('btn-plus.png', $servings + 801, false, $params);
        }

        $this->data->scroll[] = $this->getSpacer('15');
        $this->data->scroll[] = $this->getRow($columns, array('width' => '100%'));
        $this->data->scroll[] = $this->getSpacer('15');
    }

    public function getIngedients($servings = false, $msg = false)
    {

        $ingredients = $this->recipe['ingredients'];

        if (empty($ingredients)) {
            return false;
        }

        if (!$servings) {
            $serv = $this->getPlayState('servings');

            if ($serv) {
                $servings = $serv;
            } elseif ($this->configobj->servings > 0 AND $this->configobj->servings < 9) {
                $servings = $this->configobj->servings;
            } else {
                $servings = 4;
            }
        }

        $this->getIngredientsMenu($servings);

        if ($msg) {
            $this->data->scroll[] = $this->getText($msg, array('style' => 'recipe_text_narrow'));
        }

        $this->data->scroll[] = $this->getText('Съставки', array('style' => 'ingredients-title'));

        foreach ($ingredients as $i => $ingredient) {
            $count = $i + 1;

            $stepname = 'ingredient' . $count;
            $countname = 'ingredient' . $count . '_count';

            // if(isset($this->configobj->$countname) AND $this->configobj->$countname){
            //     $one_portion = $this->configobj->$countname / $this->configobj->servings;
            //     $itemcount =  round($one_portion * $servings,1);
            // } else {
            //     $itemcount = '';
            // }

            // $content = $itemcount . ' ' . $ingredient['product'];
            $content = $ingredient['prev_text'] . ' ' . $ingredient['product'];
            $this->data->scroll[] = $this->getText($content, array('style' => 'recipe_ingredients_text'));
        }

        $this->data->scroll[] = $this->getSpacer('15');
    }

    public function addToList($servings)
    {

        $count = 1;

        if (!$servings) {
            $serv = $this->getPlayState('servings');

            if ($serv) {
                $servings = $serv;
            } elseif ($this->configobj->servings > 0 AND $this->configobj->servings < 9) {
                $servings = $this->configobj->servings;
            } else {
                $servings = 4;
            }
        }

        if (isset($this->varcontent['shopping_list'])) {
            $shoppinglist = json_decode($this->varcontent['shopping_list']);
            if (!is_object($shoppinglist)) {
                $shoppinglist = new StdClass();
            }
        } else {
            $shoppinglist = new StdClass();
        }

        while ($count < 30) {
            $stepname = 'ingredient' . $count;
            $countname = 'ingredient' . $count . '_count';

            if (isset($this->configobj->$stepname) AND $this->configobj->$stepname) {
                $ingredient = $this->configobj->$stepname;

                if ($this->configobj->$stepname) {

                    if (isset($this->configobj->$countname) AND $this->configobj->$countname) {
                        $one_portion = $this->configobj->$countname / $this->configobj->servings;
                        $itemcount = round($one_portion * $servings, 1);

                        if (isset($shoppinglist->$ingredient)) {
                            $shoppinglist->$ingredient = $this->configobj->$stepname + $itemcount;
                        } else {
                            $shoppinglist->$ingredient = $itemcount;
                        }
                    } else {
                        if (!isset($shoppinglist->$ingredient)) {
                            $shoppinglist->$ingredient = 'some ';
                        }
                    }
                }
            }

            $count++;
        }

        AeplayVariable::updateWithName($this->playid, 'shopping_list', json_encode($shoppinglist), $this->gid);

        $msg = 'Recipe added to your shopping list';
        return $this->getIngedients($servings, $msg);
    }

    public function setRecipe($query_param)
    {

        if (empty($query_param)) {
            return false;
        }

        $pieces = explode('|', $query_param);
        $id = str_replace('view-recipe-', '', $pieces[0]);

        $cache_name = 'bonapeti-api-page-' . $pieces[1];
        $data = Appcaching::getGlobalCache($cache_name);

        if (empty($data)) {
            // do stuff .. like get the data from the API
            return false;
        }

        $info = json_decode($data, true);

        $key = array_search($id, array_column($info, 'recipe_ID'));
        $this->recipe = $info[$key];
    }

    public function getAPIResponse($url, $cache_prefix = 'bonapeti-api-page-')
    {

        $url_str = $this->getShortURL($url);

        $cache_name = $cache_prefix . $url_str;

        // Appcaching::removeGlobalCache( $cache_name );
        $cache_results = Appcaching::getGlobalCache($cache_name);

        if ($cache_results) {
            return json_decode($cache_results);
        }

        $ch = curl_init();

        $header = array();
        $header[] = 'Accept: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        $result = curl_exec($ch);
        curl_close($ch);

        if (empty($result)) {
            return false;
        }

        Appcaching::setGlobalCache($cache_name, $result);

        return json_decode($result, false, 512, JSON_UNESCAPED_UNICODE);
    }

    public function renderCategories($response, $url)
    {
        $url_str = $this->getShortURL($url);

        $this->getSearchBar();

        $open_action_id = $this->getConfigParam('recipe_listing_id');

        $cats_map = array(
            9 => array(
                'image' => 'cat-entrees',
                'title' => 'ПРЕДЯСТИЯ',
                'color' => '#666666',
            ),
            8 => array(
                'image' => 'cat-salads',
                'title' => 'САЛАТИ',
                'color' => '#666666',
            ),
            10 => array(
                'image' => 'cat-soups',
                'title' => 'СУПИ',
                'color' => '#666666',
            ),
            11 => array(
                'image' => 'cat-veggies',
                'title' => 'БЕЗМЕСНИ',
                'color' => '#666666',
            ),
            74 => array(
                'image' => 'cat-meat',
                'title' => 'МЕСО',
                'color' => '#f58220',
            ),
            14 => array(
                'image' => 'cat-pizza',
                'title' => 'ПИЦА | ПАСТА',
                'color' => '#666666',
            ),
            15 => array(
                'image' => 'cat-bread',
                'title' => 'ТЕСТЕНИ',
                'color' => '#666666',
            ),
            16 => array(
                'image' => 'cat-deserts',
                'title' => 'ДЕСЕРТИ',
                'color' => '#666666',
            ),
            17 => array(
                'image' => 'cat-others',
                'title' => 'ДРУГИ',
                'color' => '#666666',
            ),
        );

        $cat_rows = array_chunk($response, 3);

        foreach ($cat_rows as $row_items) {
            $current_output = array();

            foreach ($row_items as $item) {

                $item_id = $item->id;
                $mapped_item = $cats_map[$item_id];

                $onclick = new StdClass();
                $onclick->action = 'open-action';
                $onclick->action_config = $open_action_id;
                $onclick->sync_open = 1;
                $onclick->sync_close = 1;
                $onclick->id = 'view-category-' . $item_id . '|' . $url_str;

                $current_output[] = $this->getColumn(array(
                    $this->getImage($mapped_item['image'] . '.png', array(
                        'height' => 50
                    )),
                    $this->getText($mapped_item['title'], array(
                        'padding' => '10 0 0 0',
                        'color' => $mapped_item['color'],
                        'text-align' => 'center',
                        'width' => '100%',
                    )),
                ), array(
                    'width' => '33%',
                    'text-align' => 'center',
                    'vertical-align' => 'middle',
                    'onclick' => $onclick,
                ));
            };

            $this->data->scroll[] = $this->getRow($current_output, array(
                'padding' => '22 0 22 0'
            ));

            unset($current_output);
        }

        /*
        $bg = $this->getImageFileName('shadow.png');

        foreach ($response as $i => $entry) {

            $image = $this->getImageFileName( 'cat-' . ($i+1) . '.jpg' );
            $path = 'C:\xampp\htdocs\ae\xampphtdocsappzion\app\documents\games\505\images';

            list($width, $height) = @getimagesize( $path . DIRECTORY_SEPARATOR . $image );
            $dim = array(
                'height' => $height,
                'width' => $width
            );

            $dim_height = ( $dim['height'] ? $dim['height'] : '200' );
            $dim_width = ( $dim['width'] ? $dim['width'] : '500' );

            $image_height_dp = $this->screen_width * $dim_height / $dim_width;
            $image_height_dp = round( $image_height_dp );

            $row = $this->getRow(array(
                $this->getImage( $image, array( 'width' => '100%', 'lazy' => 1, 'padding' => '0 0 0 0', 'height' => $image_height_dp ) ),
                $this->getColumn(array(
                    $this->getRow(array(
                        $this->getText( $entry->name, array( 'style' => 'recipe-title', ) ),
                    ), array( 'text-align' => 'center', 'padding' => '8 10 8 10', 'background-color' => '#48000000', ) ),
                ), array( 'width' => '100%', 'padding' => '10 0 10 0', 'vertical-align' => 'middle', 'text-align' => 'center', 'floating' => 1, 'background-image' => $bg, 'background-size'=>'cover' )),
            ), array( 'width' => '100%' ));

            $background = $this->getRow(array(
                $this->getImage( $image, array( 'width' => '100%', 'padding' => '0 0 0 0' ) ),
                $this->getImage( 'shadow.png', array( 'width' => '100%', 'padding' => '0 0 0 0', 'floating' => 1 ) ),
            ), array( 'width' => '100%', 'floating' => 1 ));

            $this->data->scroll[] = $row;
        }

        */

    }

    public function renderResponse($response, $url)
    {
        $url_str = $this->getShortURL($url);

        $bg = $this->getImageFileName('shadow-bg.png');

        foreach ($response as $i => $entry) {

            $image = str_replace(' ', '%20', $entry->head_recipe_image);

            if ($image == 'http://www.bonapeti.bg/') {
                continue;
            }

            list($width, $height) = @getimagesize($image);
            $dim = array(
                'height' => $height,
                'width' => $width
            );

            $dim_height = ($dim['height'] ? $dim['height'] : '200');
            $dim_width = ($dim['width'] ? $dim['width'] : '500');

            $image_height_dp = $this->screen_width * $dim_height / $dim_width;
            $image_height_dp = round($image_height_dp);

            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->action_config = $this->getConfigParam('recipe_item_id');
            $onclick->sync_open = 1;
            $onclick->sync_close = 1;
            $onclick->id = 'view-recipe-' . $entry->recipe_ID . '|' . $url_str;
            // $onclick->context = 'recipe-' . $entry->recipe_ID;

            $cooking_time = ($entry->cooking_time ? $entry->cooking_time : 'N/A');

            $row = $this->getRow(array(
                $this->getImage($image, array('width' => '100%', 'lazy' => 1, 'padding' => '0 0 0 0', 'height' => $image_height_dp)),

                $this->getRow(array(
                    $this->getColumn(array(
                        $this->getRow(array(
                            $this->getText(strtoupper($entry->title), array('style' => 'recipe-title',)),
                        ), array(
                            'text-align' => 'left',
                            'padding' => '3 10 3 10',
                            // 'border-width' => 1,
                            // 'border-color' => '#333333'
                        )),
                        $this->getRow(array(
                            $this->getImage('icon-clock.png', array(
                                'width' => 15,
                                'margin' => '0 5 0 0',
                                'vertical-align' => 'middle',
                            )),
                            $this->getText($cooking_time, array('style' => 'recipe-author',)),
                            $this->getVerticalSpacer(20),
                            $this->getImage('icon-hearth.png', array(
                                'width' => 15,
                                'margin' => '0 5 0 0',
                                'vertical-align' => 'middle',
                            )),
                            $this->getText($entry->recipe_ID, array('style' => 'recipe-author',)),
                        ), array(
                            'text-align' => 'left',
                            'padding' => '3 0 3 10',
                            'vertical-align' => 'middle',
                            // 'border-width' => 1,
                            // 'border-color' => '#ff0000'
                        )),
                    ), array(
                        'width' => '80%',
                        'height' => '80',
                        'vertical-align' => 'bottom',
                        'padding' => '0 0 5 0',
                        // 'border-width' => 1,
                        // 'border-color' => '#333333'
                    )),
                    $this->getColumn(array(
                        $this->getImage('icon-big-play.png', array(
                            'width' => 45,
                            'text-align' => 'center',
                        )),
                        $this->getRow(array(
                            $this->getImage('icon-eye.png', array(
                                'width' => 15,
                                'margin' => '0 5 0 0',
                                'vertical-align' => 'middle',
                            )),
                            $this->getText($entry->views, array('style' => 'recipe-author',))
                        ), array(
                            'width' => '100%',
                            // 'border-width' => 1,
                            // 'border-color' => '#cc00ff',
                            'padding' => '3 0 3 0',
                            'text-align' => 'center',
                            'vertical-align' => 'middle',
                        )),
                    ), array(
                        'width' => '20%',
                        'height' => '80',
                        'text-align' => 'center',
                        'vertical-align' => 'bottom',
                        'padding' => '0 0 5 0',
                        // 'border-width' => 1,
                        // 'border-color' => '#333333'
                    )),
                ), array(
                    'vertical-align' => 'bottom',
                    'floating' => 1,
                    'width' => '100%',
                    'height' => $image_height_dp,
                    'background-image' => $bg,
                    'background-size' => 'cover',
                )),
            ), array(
                'width' => '100%',
                'onclick' => $onclick,
            ));

            $this->data->scroll[] = $row;
            $this->data->scroll[] = $this->getSpacer(2, array(
                'background-color' => '#b3c135'
            ));
        }

    }

    public function getShortURL($url)
    {
        $encoded = bin2hex($url);
        // $encoded = chunk_split($encoded, 2, '%');
        $encoded = substr($encoded, 0, strlen($encoded) - 1);
        return $encoded;
        // return base_convert($url, 10, 36);
    }

    public function refreshToken()
    {

        $refresh_token = $this->getVariable('refresh_token');
        $auth_code = $this->getVariable('initial_auth_code');

        if (empty($refresh_token)) {
            return false;
        }

        $data = array(
            'grant_type' => 'refresh_token',
            'client_id' => 'appzio',
            'client_secret' => 'appziopass',
            // 'redirect_uri' => 'https://appzio.com/gettoken.php',
            'code' => $auth_code,
            'refresh_token' => $refresh_token,
            'authorize' => '1',
        );

        $url = 'http://api.bonapeti.bg/token';

        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $url);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);
        # curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        # curl_setopt($process, CURLOPT_USERPWD, "$login:$password");
        $result = curl_exec($process);

        curl_close($process);

        $this->saveAPIResponse($result);
    }

    public function saveAPIResponse($result)
    {

        if (empty($result)) {
            return false;
        }

        $result = json_decode($result, true);

        if (isset($result['error'])) {

            // echo '<pre>';
            // print_r( 'Expired Refresh Token' );
            // echo '</pre>';
            // exit;

            return false;
        }

        foreach ($this->fields as $field) {
            if (isset($result[$field]) AND !empty($result[$field])) {
                $this->saveVariable($field, $result[$field]);
            }
        }

        return true;
    }

    public function addImageToUser()
    {
        $comment = $this->getVariable('comment_temp');
        $picture = $this->getVariable('upload_temp');

        if (empty($picture)) {
            $picture = 'photo-placeholder.jpg';
        }

        $recipe_id = $this->recipe['recipe_ID'];

        $recipe_data = array(
            'name' => $this->playid,
            'date' => date('r'),
            'pic' => $picture,
            'comment' => $comment,
            'user' => $this->playid,
        );

        $this->playdatastorage->key = 'bonapeti-recipe-image-' . $recipe_id;
        $this->playdatastorage->value = json_encode($recipe_data);
        $this->playdatastorage->filename = 'bonapeti-image-collection';
        $this->playdatastorage->label = 'bonapeti-image-collection';
        $this->playdatastorage->insert();

        $this->deleteVariable('comment_temp');
        $this->deleteVariable('upload_temp');
    }

    public function getSearchBar()
    {
        // 'background-color' => $this->color_topbar_hilite,

        $value = false;

        $row[] = $this->getImage('icon-person.png', array(
            'width' => '25',
            'margin' => '0 10 0 10',
            'vertical-align' => 'middle',
            // 'height' => '50'
        ));

        $row[] = $this->getRow(array(
            $this->getFieldtext($value, array(
                'width' => '85%',
                'font-size' => '14',
                'padding' => '0 10 0 10',
                'hint' => 'Търси ...',
                'submit_menu_id' => 'dosearch',
                'variable' => 'searchterm',
                'id' => 'something',
                'vertical-align' => 'middle',
                // 'background-color' => $this->color_topbar,
                // 'style' => 'olive-searchbox-text',
                // 'suggestions_style_row' => 'example_list_row',
                // 'suggestions_text_style' => 'example_list_text',
                //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
                //'submit_on_entry' => '1',
            )),
            $this->getImage('icon-search.png', array(
                'width' => '25',
                'vertical-align' => 'middle',
            )),
        ), array(
            'background-color' => '#ffffff',
            'vertical-align' => 'middle',
            'border-radius' => '5',
            'margin' => '0 10 0 0',
            'height' => '34',
        ));

        $this->data->header[] = $this->getRow($row, array(
            'background-color' => $this->color_topbar,
            'vertical-align' => 'middle',
            'height' => '50',
        ));
    }

    public function showShoppingList()
    {
        $this->rewriteActionField('subject', 'Shopping List');

        if (isset($this->varcontent['shopping_list'])) {
            $shoppinglist = json_decode($this->varcontent['shopping_list']);
            if (!is_array($shoppinglist)) {
                $shoppinglist = array();
            }
        } else {
            $shoppinglist = array();
        }

        $index = 0;
        foreach ($shoppinglist as $item) {

            $onclick = new stdClass();
            $onclick->id = 'remove-from-list-' . $index;
            $onclick->action = 'submit-form-content';

            $this->data->scroll[] = $this->getRow(array(
                $this->getText($item),
                $this->getImage('minus_icon.png', array('onclick' => $onclick))
            ), array(
                'margin' => '10 25 10 25'
            ));

            $this->data->scroll[] = $this->getRow(array(
                $this->getHairline('#dadada')
            ), array('margin' => '3 25 3 25'));

            $index++;
        }
    }

}