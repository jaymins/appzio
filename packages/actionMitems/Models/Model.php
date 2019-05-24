<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\Models;

use Bootstrap\Models\BootstrapModel;
use packages\actionMbooking\Models\BookingModel;

class Model extends BootstrapModel
{

    use Categories;
    use ItemReminders;

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;

    public $output;

    public $editId;

    /**
     * Retrieve a single item from storage based on it's ID.
     * It's tags, category and owner are preloaded.
     *
     * @param $itemId
     * @return array|mixed|null|static
     */
    public function getItem(int $itemId): ItemModel
    {
        $item = ItemModel::model()->with('tags', 'category')->findByPk($itemId);

        if (empty($item) || is_null($item)) {
            return new ItemModel();
        }

        if (!$item->city AND $item->lat AND $item->lon) {
            $address = $this->coordinatesToAddress($item->lat, $item->lon);

            if (isset($address['city'])) {
                $item->city = $address['city'];
            }

            if (isset($address['country'])) {
                $item->country = $address['country'];
                $item->update();
            }
        }

        if (isset($item->tags)) {
            foreach ($item->tags as $tag) {
                $values['title'] = $tag->name;
                $values['id'] = $tag->id;
                $tags[] = $values;
            }

            if (isset($tags) AND !empty($tags)) {
                $item->pretty_tags = $tags;
            } else {
                $item->pretty_tags = array();
            }
        }

        $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);
        return $item;
    }

    public function getItemsByCategory($category_id = null)
    {
        $relations = [
            'category_relations' => [
                'with' => [
                    'item' => [
                        'alias' => 'item',
                        'with' => [
                            'images_data'
                        ]
                    ],
                    'category' => [
                        'alias' => 'item_category',
                    ]
                ],
            ]
        ];

        if (!$category_id) {
            $category_list = ItemCategoryModel::model()
                ->with($relations)
                ->findAll([
                    'condition' => 'item_category.app_id = :appId',
                    'params' => [
                        ':appId' => $this->appid,
                    ],
                    'order' => 'date_added DESC'
                ]);
        } else {
            $category_list = ItemCategoryModel::model()
                ->with($relations)
                ->findByPk($category_id, [
                    'order' => 'date_added DESC'
                ]);
        }

        if (empty($category_list)) {
            return false;
        }

        return $category_list;
    }

    public function getPrettyTagsFromSession()
    {
        $tags = $this->sessionGet('tags');

        if (is_array($tags)) {
            foreach ($tags as $key => $tag) {
                $out['title'] = $tag;
                $out['id'] = $key;
                $output[] = $out;
            }
            if (isset($output) AND is_array($output)) {
                return $output;
            }
        }

        return array();

    }

    /**
     * Check whether a item is liked by the currently active user
     *
     * @param int $itemId
     * @return bool
     */
    public function isItemLiked(int $itemId): bool
    {
        $isitemLiked = ItemLikeModel::model()->find('play_id = :playId AND item_id = :itemId AND status = "like"', array(
            ':playId' => $this->playid,
            ':itemId' => $itemId
        ));

        return !!$isitemLiked;
    }

    /**
     * Check whether an item is booked by the currently active user
     *
     * @param int $itemId
     * @return bool
     */
    public function isItemBooked(int $itemId): bool
    {
        $isItemBooked = BookingModel::model()->find('play_id = :playId AND item_id = :itemId AND status != :status', array(
            ':playId' => $this->playid,
            ':itemId' => $itemId,
            ':status' => 'declined',
        ));

        return !!$isItemBooked;
    }

    /**
     * Store a single item in storage together with
     * all of it's relations - categories and tags.
     * Item is returned with it's owner preloaded.
     *
     * @param $images
     * @param $tags
     * @param string $type
     * @return \packages\actionMitems\Models\ItemModel
     */
    public function saveItem($images, $tags, $type = '')
    {

        $itemData = array_merge([
            'play_id' => $this->playid,
            'game_id' => $this->appid,
            'images' => $images,
            'status' => 'active',
            'date_added' => time(),
            'lat' => $this->getSavedVariable('lat'),
            'lon' => $this->getSavedVariable('lon'),
            'category_id' => null,
            'id' => $this->editId,
            'type' => $type,
            'slug' => $this->playid . '-' . $this->getSubmittedVariableByName('name') . '-' . date('d-M-Y-H:m')
        ], $this->getAllSubmittedVariablesByName());

        if (!$this->getSubmittedVariableByName('time')) {
            $itemData['time'] = '1';
        }

        if ($this->getSavedVariable('price')
            && empty($itemData['price'])) {
            $itemData['price'] = $this->getSavedVariable('price');
        }

        try {
            $item = ItemModel::store($itemData);
        } catch (\Exception $e) {
            return null;
        }

        $tagIds = $this->saveAndGetTagIds($tags);
        $categoryIds = $this->getSubmittedCategoryIds();

        $this->saveItemAndTagRelation($item->id, $tagIds);
        $this->saveItemAndCategoryRelation($item->id, $categoryIds);

        $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);

        return $item;
    }

    public static function findUnlikedItems($playId, $units = 'miles')
    {
        // TODO: use getUserFilters method when this is not static
        $filters = ItemFilterModel::model()->find('play_id = :playId', array(
            ':playId' => $playId
        ));

        if (isset($filters->tags) && !empty($filters->tags)) {
            $filteredTags = json_decode($filters->tags, true);
            $tags = '';
            $tagsCondition = '';


            if ($filteredTags) {
                $tags = array_map(function ($tag) {
                    return '\'' . $tag . '\'';
                }, $filteredTags);
                $tags = implode(', ', $tags);
            }

            if (!empty($tags)) {
                $tagsCondition = "tags.name IN ($tags)";
            }
        } else {
            $tagsCondition = '';
        }

        if (isset($filters->categories)) {
            $filteredCategories = json_decode($filters->categories);
            $categories = '';
            $categoriesCondition = '';

            if ($filteredCategories) {
                $categories = array_map(function ($category) {
                    return '\'' . $category . '\'';
                }, $filteredCategories);
                $categories = implode(', ', $categories);
            }

            if (!empty($categories)) {
                $categoriesCondition = "categories.name IN ($categories)";
            }
        } else {
            $categoriesCondition = '';
        }

        $vars = \AeplayVariable::getArrayOfPlayvariables($playId);

        $params = array(
            ':playId' => $playId
        );

        if (isset($vars['lat']) && isset($vars['filter_distance'])) {

            $lat = $vars['lat'];
            $lon = $vars['lon'];

            \Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
            \Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
            \Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

            //Only in miles
            $select = array("*",
                "distance" => "( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) )
                    * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
                    * sin( radians(`lat`)))
                ) AS distance");
            $params['unit'] = ($units == 'km' ? 1.609344 : 1);
        } else {
            $select = "*";
        }

        $condition = self::getFilterCondition($filters, $playId);
        $condition .= 't.id NOT IN (SELECT ae_ext_items_likes.item_id FROM ae_ext_items_likes WHERE play_id = :playId)';

        $criteria = new \CDbCriteria();
        $criteria->select = $select;
        $criteria->condition = $condition;
        if (isset($vars['filter_distance']) && isset($vars['lat'])) {
            $criteria->having = 'distance <= ' . $vars['filter_distance'] . ' OR distance IS NULL';
        }
        $criteria->group = 't.id';
        $criteria->params = $params;

        $items = ItemModel::model()
            ->with(array(
                'tags' => array(
                    'condition' => $tagsCondition
                ),
                'categories' => array(
                    'condition' => $categoriesCondition
                )
            ))
            ->findAll(
                $criteria
            );

        return $items;
    }

    protected static function getFilterCondition($filters, $playId)
    {
        $condition = '';

        if (empty($filters)) {
            return $condition;
        }

        if ($filters->price_from) {
            $condition .= "price >= $filters->price_from AND ";
        }

        if ($filters->price_to) {
            $condition .= "price <= $filters->price_to AND ";
        }

        return $condition;
    }

    /**
     * Stores tags in the database depending if they already exist or not.
     * Returns an array of the tag IDs.
     *
     * @param $tags
     * @return array
     */
    public function saveAndGetTagIds(array $tags): array
    {
        $ids = array();

        foreach ($tags as $tag) {
            $model = ItemTagModel::model()->find('name = :name', array(':name' => $tag));
            if (!$model) {
                $model = new ItemTagModel();
                $model->name = $tag;
                $model->save();
            }

            $ids[] = $model->id;
        }

        return $ids;
    }

    public function getSubmittedCategoryIds()
    {
        $ids = array();

        foreach ($this->getAllSubmittedVariablesByName() as $variable => $value) {
            if (stristr($variable, 'category|') && !empty($value)) {
                $categoryName = str_replace('category|', '', $variable);
                $model = ItemCategoryModel::model()->find('name = :name', array(':name' => $categoryName));

                $ids[] = $model->id;
            }
        }

        return $ids;
    }

    /**
     * Store item and tags relation (many to many).
     *
     * @param $itemId
     * @param $tagIds
     * @return void
     */
    public function saveItemAndTagRelation($itemId, $tagIds)
    {
        $data = array_map(function ($id) use ($itemId) {
            return array(
                'tag_id' => $id,
                'item_id' => $itemId
            );
        }, $tagIds);

        if (empty($data)) {
            return;
        }

        $builder = \Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('ae_ext_items_tag_item', $data);
        $command->execute();
    }

    public function saveItemAndCategoryRelation($itemId, $categoryIds)
    {
        $data = array_map(function ($id) use ($itemId) {
            return array(
                'item_id' => $itemId,
                'category_id' => $id
            );
        }, $categoryIds);

        if (empty($data)) {
            return;
        }

        $builder = \Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('ae_ext_items_category_item', $data);
        $command->execute();
    }

    /**
     * Retrieve all items created by given user.
     *
     * @return array|mixed|null|static[]
     */
    public function getUserItems()
    {
        $items = ItemModel::model()->findAll('play_id = :playId ORDER BY id DESC', array(
            ':playId' => $this->playid
        ));

        return $items;
    }

    /**
     * Retrieve all items created by given user.
     *
     * @return array|mixed|null|static[]
     */
    public function getAllItems($page = false, $only_user = false, $only_liked = false, $type = array(), $limit = 10)
    {

        $filters = $this->getUserFilters();

        if ($this->getSubmittedVariableByName('searchterm') AND $this->getMenuId() != 'cancelsearch') {
            $term = $this->getSubmittedVariableByName('searchterm');
            $term = addcslashes($term, '%_');
        }

        if ($only_user) {
            $conditions = "item.game_id = :gameid AND item.play_id = :playid";
        } else {
            $conditions = "item.game_id = :gameid AND item.status = 'active'";
        }

        $params['gameid'] = $this->appid;
        $params['playid'] = $this->playid;

        if (isset($term)) {
            $params['match'] = '%' . $term . '%';
            $conditions .= ' AND (item.name LIKE :match OR item.description LIKE :match OR tags.name LIKE :match)';
        }

        if (isset($filters->category_id) AND $filters->category_id != 0) {
            $conditions .= ' AND category_id = :categoryid';
            $params['categoryid'] = $filters->category_id;
        }

        if (isset($filters->price_from)) {
            $to = $filters->price_to ? $filters->price_to : '5000';
            $conditions .= ' AND item.price > :from AND item.price < :to';
            $params['from'] = $filters->price_from;
            $params['to'] = $to;
        }

        if ($type) {
            $conditions .= ' AND (';

            foreach ($type as $i => $item_type) {
                $conditions .= "item.type = :it{$i}";

                if (($i + 1) < count($type))
                    $conditions .= ' OR ';

                $params['it' . $i] = $item_type;
            }

            $conditions .= ' )';
        }

        if ($only_liked) {
            $conditions .= " AND likes.status = 'like' AND likes.play_id = :playid";
        }

        if ($page) {
            if ($page == 1) {
                $offset = '0';
            } else {
                $offset = ($page-1) * $limit;
            }
        }

        $q = new \CDbCriteria;
        $q->alias = 'item';
        $q->condition = $conditions;
        $q->params = $params;
        $q->limit = $limit;
        $q->group = 'item.id';
        $q->order = 'featured DESC';
        $q->join = 'LEFT OUTER JOIN ae_ext_items_likes AS likes ON (item.id = likes.item_id AND likes.play_id = :playid) ';
        $q->join .= 'LEFT JOIN ae_ext_items_tag_item AS tags_pivot ON item.id = tags_pivot.item_id ';
        $q->join .= 'LEFT JOIN ae_ext_items_tags AS tags ON tags.id = tags_pivot.tag_id';

        if (isset($offset)) {
            $q->offset = $offset;
        }

        $items = ItemModel::model()->findAll($q);

        if (!is_array($items)) {
            return array();
        }

        return $items;
    }

    /**
     * Mark item as liked or skipped.
     *
     * @param $itemId
     * @param $status
     * @return bool
     */

    public function selectItemStatus($itemId, $status)
    {

        $test = ItemLikeModel::model()->findByAttributes(array('play_id' => $this->playid, 'item_id' => $itemId));

        if (isset($test->status) AND $test->status == $status) {
            return false;
        }

        if (is_object($test)) {
            $test->status = $status;
            $test->update();
        } else {
            $itemLike = new ItemLikeModel();
            $itemLike->play_id = $this->playid;
            $itemLike->item_id = $itemId;
            $itemLike->status = $status;
            $itemLike->save();
        }

        return true;

    }

    /**
     * Get all items liked by the currently active user
     *
     * @return array|mixed|null
     */
    public function getLikedItems()
    {
        return ItemLikeModel::model()->with('item')->findAll('t.play_id=:playId AND t.status = "like"', array(
            ':playId' => $this->playid
        ));
    }

    /**
     * Get an array of likes for user
     *
     * @return array|mixed|null
     */
    public function getMyLikedItemsArray()
    {
        $out = ItemLikeModel::model()->with('item')->findAll('t.play_id=:playId AND t.status = "like"', array(
            ':playId' => $this->playid
        ));

        if (isset($out) AND $out) {
            foreach ($out as $key => $value) {

                $id = $value->item_id;
                $output[$id] = true;
            }

            return $output;
        }

        return array();

    }


    /**
     * Remove item from liked list. Entire relationship is deleted.
     *
     * @param $itemId
     * @return void
     */
    public function removeFromLiked($itemId)
    {
        $current = ItemLikeModel::model()->find('play_id = :playId AND item_id = :itemId', array(
            ':playId' => $this->playid,
            ':itemId' => $itemId
        ));

        if (is_object($current)) {
            $current->delete();
        }

    }

    /**
     * Get an array of the currently active item's images.
     * Accessed from variables which are prefilled once a item has been
     * selected or opened.
     *
     * @return array
     */
    public function getItemImages(): array
    {
        $images = array();

        if ($this->getSavedVariable('itempic')) {
            // Save main item image
            $images['itempic'] = $this->getSavedVariable('itempic');
        }

        for ($i = 2; $i < 7; $i++) {
            $picture = 'itempic' . $i;
            if (empty($images['itempic'])) {
                $picIndex = 'itempic';
            } else {
                $picIndex = $picture;
            }

            if ($this->getSavedVariable($picture)) {
                $images[$picIndex] = $this->getSavedVariable($picture);
            }
        }

        return $images;
    }

    /**
     * Validate submitted variables.
     * Errors are filled in validation_errors property which is used by the controller.
     *
     * @return void
     */
    public function validateInput()
    {
        $requiredVariables = array(
            'name' => 'empty|length:20',
            'description' => 'empty'
        );

        $submittedVariables = $this->getAllSubmittedVariablesByName();
        $categories = array();

        foreach ($submittedVariables as $key => $value) {
            if (strstr($key, 'category|') && !empty($value)) {
                $categories[] = $value;
            }

            foreach ($requiredVariables as $var => $validation_type) {

                $validations = explode('|', $validation_type);

                foreach ($validations as $validation) {

                    if ($key != $var) {
                        continue;
                    }

                    if ($validation == 'empty' AND empty($value)) {
                        $this->validation_errors[$key] = 'The ' . $key . ' field is required';
                    } else if ($value AND preg_match('~length~', $validation)) {
                        $rq_chars = str_replace('length:', '', $validation);
                        if (strlen($value) > $rq_chars)
                            $this->validation_errors[$key] = 'The ' . $key . ' field should contain ' . $rq_chars . ' characters max';
                    }
                }

            }

        }

        $tags = $this->sessionGet('tags');

        if (empty($tags)) {
            $this->validation_errors['tags'] = 'Add at least one tag';
        }

        if (empty($categories)) {
            $this->validation_errors['categories'] = 'Please add at least one category';
        }

        $images = $this->getItemImages();

        if (empty($images)) {
            $this->validation_errors['images'] = 'Add at least one image';
        }

        if ($this->getSubmittedVariableByName('price') > 99999) {
            $this->validation_errors['price'] = 'Maximum price allowed is $99999';
        }

    }

    /**
     * Fill data that should be prefilled in the fields when the user opens the creation view
     *
     * @param $variables array
     * @return array
     */
    public function fillPresetData($variables): array
    {
        $data = array();

        if (empty($variables) AND $this->getSavedVariable('temp_preset')) {
            $variables = json_decode($this->getSavedVariable('temp_preset'), true);

            // This is required as otherwise some of the pre-set data would stay the same across the different visits.
            // $this->deleteVariable('temp_preset');
        }

        foreach ($variables as $variable => $value) {
            $data[$variable] = $value;
        }

        return $data;
    }

    /**
     * Returns additional items added by the given artist.
     * The given item id is ignored.
     *
     * @param $artistId
     * @param $itemId
     * @return array|mixed|null|static[]
     */
    public function getOtherArtistItems($artistId, $itemId, $limit = 3)
    {
        return ItemModel::model()->findAll('play_id = :playId AND id != :itemId LIMIT ' . $limit, array(
            ':playId' => $artistId,
            ':itemId' => $itemId
        ));
    }

    public function getRelatedItems($playid, $itemId, $limit = 3)
    {
        $records = ItemModel::model()->findAll('play_id = :playId AND id != :itemId LIMIT ' . $limit, array(
            ':playId' => $playid,
            ':itemId' => $itemId
        ));

        if ($records AND !empty($records)) {
            foreach ($records as $item) {
                $o['id'] = $item->id;
                $o['play_id'] = $item->play_id;
                $o['category_id'] = $item->category_id;
                $o['name'] = $item->name;
                $o['price'] = $item->price;
                $o['status'] = $item->status;
                $o['date_added'] = $item->date_added;
                $o['description'] = $item->description;
                $o['images'] = @json_decode($item->images, true);
                $o['image'] = array_shift($o['images']);
                $out[] = $o;
            }

            if (isset($out)) {
                return $out;
            }

        }

        return array();

    }

    public function filterMenu()
    {

        foreach ($this->menus['menus'] as $menu) {

            if (isset($menu['slug']) AND $menu['slug'] == 'filtering') {
                $filter_not_active = $menu['id'];
            }
            if (isset($menu['slug']) AND $menu['slug'] == 'filtering-active') {
                $filter_active = $menu['id'];
            }
        }

        $filters = $this->getUserFilters();

        if ($filters AND isset($filter_active)) {
            $this->rewriteActionConfigField('menu_id', $filter_active);
        } elseif (isset($filter_not_active)) {
            $this->rewriteActionConfigField('menu_id', $filter_not_active);
        }

    }

    /**
     * Returns the item filters for the currently active user.
     * If there isn't an entry it will return null.
     *
     * @return array|mixed|null|static
     */
    public function getUserFilters()
    {
        $filters = ItemFilterModel::model()->find('play_id = :playId', array(
            ':playId' => $this->playid
        ));

        return $filters;
    }

    public function clearUserFilters()
    {
        ItemFilterModel::model()->deleteAllByAttributes(array('play_id' => $this->playid));
    }

    /**
     * Store item filter settings in database for the current user.
     * They are then used to show only items that fit the current filters.
     *
     * @return ItemFilterModel
     */
    public function saveItemFilters()
    {
        $variables = $this->getAllSubmittedVariablesByName();

        $categories = array();

        foreach ($variables as $key => $value) {
            if (strstr($key, 'category|') && !empty($value)) {
                $categories[] = $value;
            }
        }

        $filter = ItemFilterModel::model()->find('play_id = :playId', array(
            ':playId' => $this->playid
        ));
        $method = 'update';

        if (empty($filter)) {
            $filter = new ItemFilterModel();
            $method = 'insert';
        }

        $filter->play_id = $this->playid;
        $filter->price_from = $variables['price_from'];
        $filter->price_to = $variables['price_to'];
        $filter->tags = json_encode($this->sessionGet('filter_tags'));
        $filter->categories = json_encode($categories);
        $filter->$method();

        if (isset($variables['filter_distance'])) {
            $this->saveVariable('filter_distance', $variables['filter_distance']);
        }

        return $filter;
    }

    /**
     * Clear item image variables. This is used to ensure
     * that when certain views are opened there are no images
     * remaining from the previous item.
     *
     * @return void
     */
    public function clearImageVariables()
    {
        $this->saveVariable('itempic', null);

        for ($i = 2; $i < 7; $i++) {
            $picture = 'itempic' . $i;
            $this->saveVariable($picture, null);
        }

        $this->saveVariable('category_name', null);
        $this->saveVariable('category', null);
    }

    /**
     * Save the passed image variables. This method is used
     * when displaying a single item to populate the variables
     * which are used in the image grid.
     *
     * @param $images
     */
    public function loadItemImages($images)
    {
        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $this->saveVariable($key, $value);
            }
        }
    }

    public function setBackgroundColor($color = '#edac34')
    {
        $this->rewriteActionConfigField('background_color', $color);
    }

    public function reportItem($itemid=false)
    {

        $marker = $itemid ? $itemid : $this->sessionGet('item_id_' . $this->action_id);

        $item = ItemModel::model()->findByPk($marker);
        $reasons = array();

        if(!$item){
            return false;
        }

        foreach ($this->getAllSubmittedVariablesByName() as $key => $value) {
            if (!$value) {
                continue;
            }

            $reasons[$key] = $value;
        }

        $report = new ItemReportModel();
        $report->play_id = $this->playid;
        $report->item_id = $item->id;
        $report->item_owner_id = $item->play_id;
        $report->reason = json_encode($reasons);
        $report->save();
        $this->selectItemStatus($item->id, 'reported');
    }


    public function updateItemsLocation($lat, $lon)
    {
        ItemModel::model()->updateAll(
            array('lat' => $lat, 'lon' => $lon),
            "play_id = :playId",
            array('playId' => $this->playid)
        );
    }

    public static function updateItemsPrice($price, $playId)
    {
        ItemModel::model()->updateAll(
            array('price' => $price),
            "play_id = :playId",
            array('playId' => $playId)
        );
    }

    public function deleteItem($itemId)
    {
        ItemModel::model()->deleteByPk($itemId);
    }

    public function validateRoutine()
    {
        $is_valid = true;
        $submitted_data = $this->getAllSubmittedVariablesByName();

        if (
            (!isset($submitted_data['default_routine']) OR empty($submitted_data['default_routine'])) AND
            (!isset($submitted_data['routine_name']) OR empty($submitted_data['routine_name']))
        ) {
            $this->validation_errors['missing_name'] = '{#please_choose_a_routine_or_create_a_new_one#}';
            $is_valid = false;
        }

        if (!isset($submitted_data['event_date']) AND !isset($submitted_data['recurring_start_date'])) {
            $this->validation_errors['missing_date'] = '{#please_select_a_routine_date#}';
            $is_valid = false;
        }

        if (
            !isset($submitted_data['event_start_time_hour']) OR
            empty($submitted_data['event_start_time_hour']) OR
            $submitted_data['event_start_time_hour'] == 'false'
        ) {
            $this->validation_errors['missing_event_time'] = '{#please_select_a_time_for_your_event#}';
            $is_valid = false;
        }

        if (!$is_valid) {
            return false;
        }

        return true;
    }

    public function saveRoutine($mode = 'insert', $event_id = null)
    {
        $submitted_data = $this->getAllSubmittedVariablesByName();

        $routine_data = [
            'play_id' => $this->playid,
            'game_id' => $this->appid,
            'status' => 'active',
            'type' => 'routine',
            'date_added' => time(),
            'lat' => $this->getSavedVariable('lat'),
            'lon' => $this->getSavedVariable('lon'),
            'category_id' => null
        ];

        if ($event_id) {
            $routine_data['id'] = $event_id;
        }

        if (isset($submitted_data['routine_name']) AND $submitted_data['routine_name']) {
            $routine_data['name'] = $submitted_data['routine_name'];
        } else if (isset($submitted_data['default_routine']) AND $submitted_data['default_routine']) {
            $routine_data['name'] = $submitted_data['default_routine'];
        }

        if (isset($submitted_data['routine_description']) AND $submitted_data['routine_description']) {
            $routine_data['description'] = $submitted_data['routine_description'];
        }

        if (!$this->getSubmittedVariableByName('time')) {
            $routine_data['time'] = '1';
        }

        $routine = ItemModel::store($routine_data);

        if (!isset($routine->id) OR empty($routine->id)) {
            return false;
        }

        $reminder_data['item_id'] = $routine->id;
        $reminder_data['type'] = 'routine';

        // The event is recurring
        if (isset($submitted_data['recurring_start_date'])) {
            $date = $this->precalculateDate($submitted_data['recurring_start_date'], $submitted_data);

            $reminder_data['date'] = $date;
            $reminder_data['end_date'] = $date + 31557600; // + 1 year in seconds
            $reminder_data['recurring'] = 1;

            $add_pattern = true;
        } else {
            $date = $this->precalculateDate($submitted_data['event_date'], $submitted_data);

            $reminder_data['date'] = $date;
            $reminder_data['end_date'] = $date + 86400;
            $reminder_data['is_full_day'] = 1;
        }

        $reminder = ItemRemindersModel::store($reminder_data);

        if (!isset($reminder->id) OR empty($reminder->id)) {
            return false;
        }

        $pattern_data = [];

        $pattern = '';
        $subject = '!MPROVE - Your Routine - ' . $reminder->item->name;

        // Add pattern for recurring events
        if (isset($add_pattern)) {

            $pattern_data['item_reminder_id'] = $reminder->id;

            if ($days = $this->getSubmittedDays($submitted_data)) {
                $pattern_data['recurring_type'] = 'weekly';
                $pattern_data['day_of_week'] = implode(',', $days);
                $pattern_data['separation_count'] = $this->getSeparationCount($submitted_data, 'weekly_recurring_interval');

                $subject = '!MPROVE - Your weekly Routine - ' . $reminder->item->name;
                $pattern = 'weekly';
            } else {
                $pattern_data['recurring_type'] = 'monthly';
                $pattern_data['day_of_month'] = (isset($submitted_data['monthly_recurring_day']) ? $submitted_data['monthly_recurring_day'] : date('j'));
                $pattern_data['separation_count'] = $this->getSeparationCount($submitted_data, 'monthly_recurring_interval');

                $subject = '!MPROVE - Your monthly Routine - ' . $reminder->item->name;
                $pattern = 'monthly';
            }

            ItemRemindersPatternModel::store($pattern_data);
        }

        $this->sendOutlookInvite(
            $routine_data['name'],
            (isset($routine_data['description']) ? $routine_data['description'] : ''),
            $date,
            $reminder_data['end_date'],
            $this->getEmailDescription($routine_data['name'], $pattern),
            $pattern_data,
            $subject
        );

        // Send the Routine to additional recipients
        if ($submitted_data['routine_emails']) {
            $emails = explode(',', $submitted_data['routine_emails']);

            foreach ($emails as $email) {

                if (empty($email) OR strlen($email) < 4) {
                    continue;
                }

                // Make sure the email has no white spaces and is lowercase
                $email = strtolower(trim($email));

                $this->sendOutlookInvite(
                    $routine_data['name'],
                    (isset($routine_data['description']) ? $routine_data['description'] : ''),
                    $date,
                    $reminder_data['end_date'],
                    $this->getEmailDescriptionToAll($routine_data['name'], $pattern),
                    $pattern_data,
                    $subject,
                    $email
                );
            }
        }

        return true;
    }

    public function getEmailDescription($routine_name, $pattern = false)
    {
        $name = $this->getSavedVariable('firstname');

        $body = "Dear {$name},<br><br>";

        if ($pattern) {
            $body .= "please find attached your calendar entry for your {$pattern} routine to {$routine_name}.<br><br>";
        } else {
            $body .= "please find attached your calendar entry for your routine to {$routine_name}.<br><br>";
        }

        $body .= "To import it into your Outlook calendar, please open the attached ICS file and click \"Save & Close\".<br><br>";
        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: Dgf-firstchoice@dhl.com";

        return $body;
    }

    public function getEmailDescriptionToAll($routine_name, $pattern = false)
    {
        $body = "Dear colleagues,<br><br>";
        $name = $this->getSavedVariable('real_name');

        if ($pattern) {
            $body .= "please find attached the calendar entry created by {$name} for your {$pattern} routine to {$routine_name}.<br><br>";
        } else {
            $body .= "please find attached the calendar entry created by {$name} for your routine to {$routine_name}.<br><br>";
        }

        $body .= "To import it into your Outlook calendar, please open the attached ICS file and click \"Save & Close\".<br><br>";
        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: Dgf-firstchoice@dhl.com";

        return $body;
    }

    public function sendOutlookInvite($title, $description, $date, $end_date, $email_body, $pattern_data = array(), $subject = '', $email_to = '')
    {

        if (!$email_to) {
            $email_to = $this->getSavedVariable('email');
        }

        if ($offset = $this->getSavedVariable('offset_in_seconds')) {
            $date = $date - ($offset);
        }

        $end_time = $date + 3600;

        $parameters = array(
            'starttime' => $date,
            'endtime' => $end_time,
            'organizer' => $this->getSavedVariable('real_name'),
            'organizer_email' => $email_to,
            'subject' => $title,
            'description' => $description
        );

        if ($pattern_data) {
            $type = $pattern_data['recurring_type'];

            if ($type == 'weekly') {
                $parameters['repeat_weekly_until'] = $end_date;

                $map = $this->numberToWeekDays();
                $days = explode(',', $pattern_data['day_of_week']);

                $byday = [];

                foreach ($days as $day) {
                    $byday[] = $map[$day];
                }

                $parameters['BYDAY'] = implode(',', $byday);
            } else if ($type == 'monthly') {
                $parameters['repeat_monthly_until'] = $end_date;
                $parameters['BYMONTHDAY'] = isset($pattern_data['day_of_month']) ? $pattern_data['day_of_month'] : date('d');
            }

            if ($interval = $pattern_data['separation_count']) {
                $parameters['interval'] = $interval;
            }

            $parameters['location'] = 'frequency - ' . $type;
        }

        $calpath = 'documents/games/' . $this->appid . '/calendars/';
        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . $calpath;

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        $template = $this->getCalendarTemplate($parameters);
        $filename = 'Routine - ' . time() . '.ics';
        file_put_contents($path . $filename, $template);

        \Aenotification::addUserEmail(
            $this->playid,
            $subject,
            $email_body,
            $this->actionobj->game_id,
            $email_to,
            array(
                'file1' => $path . $filename,
            )
        );

        return true;
    }

    private function getDayNumber($day_of_week)
    {

        $map = array(
            'monday' => '1',
            'tuesday' => '2',
            'wednesday' => '3',
            'thursday' => '4',
            'friday' => '5',
            'saturday' => '6',
            'sunday' => '7',
        );

        return (isset($map[$day_of_week]) ? $map[$day_of_week] : null);
    }

    private function getSubmittedDays($submitted_data)
    {

        $days = [];

        foreach ($submitted_data as $submitvariable => $value) {
            if (preg_match('~recurs_~', $submitvariable) AND $value === '1') {
                $day = str_replace('recurs_', '', $submitvariable);
                $days[] = $this->getDayNumber($day);
            }
        }

        return $days;
    }

    private function getSeparationCount($submitted_data, $var)
    {

        if (isset($submitted_data[$var]) AND $submitted_data[$var] != '1') {
            return $submitted_data[$var];
        }

        return 0;
    }

    /*
	 * Update the user's timezone on certain interval
     * As per default, the update action would happen every 30 minutes
	 */
    public function updateTimezone($timetolive = 600)
    {

        if (!$this->getSavedVariable('lat'))
            return false;

        // Update the user's timezone on certain interval
        $location_data = $this->getTimezone(
            $this->getSavedVariable('lat'),
            $this->getSavedVariable('lon'),
            '',
            $timetolive
        );

        if ($location_data) {
            $this->saveVariable('timezone_id', $location_data['timezone_id']);
            $this->saveVariable('offset_in_seconds', $location_data['offset_in_seconds']);
        }
    }

    /*
     * Calculate the event date ( and time ) based on the user's input
     * Note: We are making sure that the generated timestamp is saved in a UTC format
     */
    private function precalculateDate($event_date, $submitted_data)
    {

        if (
            !isset($submitted_data['event_start_time_hour']) OR
            !isset($submitted_data['event_start_time_minutes'])
        ) {
            return $event_date;
        }

        $date = gmdate('F j, Y', $event_date);
        $hour = $submitted_data['event_start_time_hour'];
        $minutes = $submitted_data['event_start_time_minutes'];

        $current_time = strtotime($date . ' ' . $hour . ':' . $minutes);
        $gmt_time = gmdate('d.m.Y H:i', $current_time);
        return strtotime($gmt_time . ' UTC');
    }

    public function numberToWeekDays()
    {
        return [
            '1' => 'MO',
            '2' => 'TU',
            '3' => 'WE',
            '4' => 'TH',
            '5' => 'FR',
            '6' => 'SA',
            '7' => 'SU',
        ];
    }

    public function getUserSubscriptionStatus()
    {
        if (
            $this->getSavedVariable('purchase_once') OR
            $this->getSavedVariable('purchase_monthly') OR
            $this->getSavedVariable('purchase_yearly')
        ) {
            return true;
        }

        return false;
    }

}