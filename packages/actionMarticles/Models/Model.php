<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMarticles\Models;

use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel
{

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;
    public $category_id;
    public $article_id;

    /**
     * Gets a list of configuration fields from the web form configuration. These are used by the view.
     * @return array
     */
    public function getFieldList()
    {
        $params = $this->getAllConfigParams();
        $output = array();

        foreach ($params as $key => $item) {
            if (stristr($key, 'mreg') AND $item) {
                $output[] = $key;
            }
        }

        return $output;
    }

    /**
     * @return bool|mixed
     */
    public function setArticleId()
    {
        $this->article_id = $this->getItemId();
        return $this->article_id;
    }

    /**
     * @return bool|mixed
     */
    public function getArticleId()
    {
        if ($this->article_id) {
            return $this->article_id;
        }

        return $this->setArticleId();
    }

    /**
     * @return bool|mixed
     */
    public function setCategoryId()
    {

        if ($this->getConfigParam('default_category')) {
            $id = $this->getConfigParam('default_category');
            $this->category_id = $id;
            return $id;
        }

        $this->category_id = $this->getItemId();
        return $this->category_id;
    }

    /**
     * @return bool|mixed
     */
    public function getCategoryId()
    {
        if ($this->category_id) {
            return $this->category_id;
        }

        return $this->setCategoryId();
    }

    /**
     * @return array|mixed|null|static
     */
    public function getCategoryInfo()
    {
        $id = $this->getCategoryId();

        if ($id) {
            return ArticlecategoriesModel::model()->findByPk($id);
        }
    }

    /**
     * @return array
     */
    public function searchArticles()
    {
        $term = $this->getSubmittedVariableByName('searchterm');

        $sql = "SELECT * FROM ae_ext_article WHERE
                (`title` LIKE '%$term%' OR `header` LIKE '%$term%' OR `description` LIKE '%$term%')
                AND app_id = :appid
                ORDER BY `id` DESC 
        ";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':appid' => $this->appid,
                )
            )
            ->queryAll();

        $output = array();

        if ($rows) {
            foreach ($rows as $row) {
                $output[] = (object)$row;
            }

            return $output;
        }

        return array();

    }

    /**
     * Get an array of Articles, associated with the current App.
     *
     * @param string $sorting
     * @param $get_by_category
     * @return array|mixed|null|static[]
     */
    public function getArticles($sorting = 'id', $get_by_category = true)
    {
        switch ($sorting) {
            case 'id':
                $sort = 't.id';
                break;

            default:
                $sort = 't.id';
                break;
        }

        $criteria = array(
            'app_id' => $this->appid,
        );

        if ($get_by_category AND $this->category_id) {
            $criteria['category_id'] = $this->category_id;
        }

        return ArticlesModel::model()->with('photos')->findAllByAttributes($criteria, array(
            'order' => $sort
        ));

        /*return ArticlesModel::model()->with(array(
            'photos' => array(
                'select' => '*',
                'condition' => 'photos.position=featured',
            ),
        ))->findAllByAttributes($criteria, array(
            'order' => $sort
        ));*/
    }

    /**
     * @return array|mixed|null|static[]
     */
    public function getArticle($article_id = false)
    {
        if (empty($article_id)) {
            $article_id = $this->getArticleId();
        }

        return ArticlesModel::model()->with('photos')->findByPk(
            $article_id
        );
    }

    /**
     * @param string $current_id
     * @param bool $in_same_category
     * @return array|mixed|null|static[]
     */
    public function getNextArticle($current_id, $in_same_category = true)
    {

        $params = array(
            ':current_id' => $current_id
        );

        $condition = 'id > :current_id';

        if ($in_same_category) {
            $condition .= ' AND category_id = :category_id';
            $params[':category_id'] = $this->category_id;
        }

        $record = ArticlesModel::model()->with('photos')->find(array(
            'condition' => $condition,
            'order' => 'id ASC',
            'limit' => 1,
            'params' => $params,
        ));

        if ($record !== null) {
            return $record;
        }

        return null;
    }

    /**
     * @return array|mixed|null|static[]
     */
    public function getCategories($parent_id = false, $level = false)
    {

        $criteria = new \CDbCriteria();

        $criteria->condition = 'app_id = :app_id';
        $criteria->params = array(
            ':app_id' => $this->appid,
        );

        /*if ( $parent_id ) {
            $criteria->condition .= ' AND parent_id = :parent_id';
            $criteria->params[':parent_id'] = $parent_id;
        }*/

        $criteria->order = 'sorting DESC';

        $categories = ArticlecategoriesModel::model()->findAllByAttributes(array(), $criteria);

        foreach ($categories as $category) {
            $output[$category->id] = array(
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'title' => $category->title,
                'headertext' => $category->headertext,
                'description' => $category->description,
                'picture' => $category->picture,
            );
        }

        if (!isset($output)) {
            return array();
        }

        foreach ($output as $id => $item) {
            $top_level[$id] = $item;
            $top_level[$id]['level'] = $this->getLevel($id, $output);
            $top_level[$id]['parents'] = $this->getAllParents($id, $output);
        }

        if (!isset($top_level)) {
            return array();
        }

        foreach ($top_level as $id => $item) {
            $children[$id] = $item;
            $children[$id]['children'] = $this->getChildren($id, $top_level);
        }

        if (!isset($children)) {
            return array();
        }

        foreach ($children as $id => $item) {
            $result[$id] = $item;
            $result[$id]['all_children'] = $this->getAllChildren($id, $children);

            // Filter by ID
            if ($parent_id !== false AND ($parent_id != $item['parent_id'])) {
                unset($result[$id]);
                continue;
            }

            // Filter the categories by level
            if ($level !== false AND ($level != $item['level'])) {
                unset($result[$id]);
                continue;
            }

        }

        if (!isset($result)) {
            return array();
        }

        return $result;
    }

    private function getLevel($id, $list, $total = 0)
    {
        if ($list[$id]['parent_id'] == 0) {
            return $total;
        }

        $total++;
        return $this->getLevel($list[$id]['parent_id'], $list, $total);
    }

    private function getAllParents($id, $list, $output = array())
    {
        if ($list[$id]['parent_id'] == 0) {
            return array();
        }

        $output[] = $list[$id]['parent_id'];

        $output = array_merge($output, $this->getAllParents($list[$id]['parent_id'], $list, $output));
        return $output;
    }

    private function getChildren($id, $list, $output = array())
    {

        foreach ($list as $key => $value) {
            if ($list[$key]['parent_id'] == $id) {
                $output[] = $key;
            }
        }

        return array_unique($output);
    }

    private function getAllChildren($id, $list, $output = array(), $recourse = 0)
    {

        foreach ($list as $key => $value) {
            if ($list[$key]['children'] AND $recourse < 5) {
                if ($id == $key) {
                    $output = array_merge($output, $list[$key]['children']);
                    $recourse++;
                    foreach ($list[$key]['children'] as $childkey) {
                        $output = array_merge($output, $this->getAllChildren($childkey, $list, $output, $recourse));
                    }
                }
            }
        }

        if (is_array($output)) {
            return array_unique($output);
        }

        return false;
    }

    public function getImageByID($image_id)
    {
        $image = ArticlephotosModel::model()->findByPk($image_id);

        if (empty($image)) {
            return false;
        }

        return $image->photo;
    }

    /**
     * Get all gallery images, associated to a certain article
     *
     * @param string $ref
     * @param int $article_id
     *
     * @return mixed
     */
    public function getGalleryImages($ref, $article_id)
    {

        $images = ArticlephotosModel::model()->findAllByAttributes(array(
            'position' => $ref,
            'article_id' => $article_id,
        ));

        if (empty($images)) {
            return false;
        }

        $photos = [];

        foreach ($images as $image) {

            if (empty($image->photo))
                continue;

            $photos[] = $image->photo;
        }

        return $photos;
    }

}