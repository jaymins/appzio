<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\Models;
use Bootstrap\Models\BootstrapModel;

trait Categories {


    public $output;


    public function getHierarchicalCategoryList(){

        $categories = ItemCategoryModel::model()->findAllByAttributes(array('app_id' => $this->appid),array('order' => 'parent_id'));

        foreach($categories as $category){
            $id = $category->id;
            $values['title'] = $category->name;
            $values['id'] = $category->id;
            $values['parent_id'] = $category->parent_id;
            $output[$id] = $values;
        }

        if(!isset($output)){
            return array();
        }

        foreach($output as $id=>$item){
            $new[$id] = $item;
            $new[$id]['level'] = $this->getLevel($id, $output);
            $new[$id]['parents'] = $this->getAllParents($id,$output);
        }

        if(!isset($new)){
            return array();
        }

        foreach($new as $id=>$item){
            $new2[$id] = $item;
            $new2[$id]['children'] = $this->getChildren($id,$new);
        }

        if(!isset($new2)){
            return array();
        }

        foreach($new2 as $id=>$item){
            $new3[$id] = $item;
            $new3[$id]['all_children'] = $this->getAllChildren($id,$new2);
        }

        if(!isset($new3)){
            return array();
        }

        return $new3;
    }


    private function getAllChildrenForSorting($input,$output=array(),$item){
        $id = $item['id'];

        if(!isset($this->output[$id])){
            $this->output[$id] = $input[$id];
        }

        if(!empty($item['children'])){
            foreach ($item['children'] as $childid){
                $this->getAllChildrenForSorting($input, $output, $input[$childid]);
            }
        } else {
            $this->output[$id] = $item;
        }

    }

    private function getChildren($id,$list,$output=array()){

        foreach($list as $key=>$value){
            if($list[$key]['parent_id'] == $id){
                $output[] = $key;
            }
        }

        return array_unique($output);
    }

    private function getAllChildren($id,$list,$output=array(),$recourse=0){

        foreach($list as $key=>$value){
            if($list[$key]['children'] AND $recourse < 5){
                if($id == $key){
                    $output = array_merge($output,$list[$key]['children']);
                    $recourse++;
                    foreach($list[$key]['children'] as $childkey){
                        $output = array_merge($output,$this->getAllChildren($childkey,$list,$output,$recourse));
                    }
                }
            }
        }

        if(is_array($output)){
            return array_unique($output);
        }

    }


    private function getAllParents($id,$list,$output=array()){
        if($list[$id]['parent_id'] == 0){
            return array();
        }

        $output[] = $list[$id]['parent_id'];

        $output = array_merge($output,$this->getAllParents($list[$id]['parent_id'], $list,$output));
        return $output;
    }

    private function getLevel($id,$list,$total=0){
        if($list[$id]['parent_id'] == 0){
            return $total;
        }

        $total++;
        return $this->getLevel($list[$id]['parent_id'], $list,$total);
    }


    /**
     * Return all item categories
     *
     * @return array|mixed|null|static[]
     */
    public function getItemCategories($prettytags=false)
    {
        $categories = ItemCategoryModel::model()->findAllByAttributes(array('app_id' => $this->appid));

        if(!$prettytags){
            return $categories;
        }

        foreach($categories as $tag){
            $values['title'] = $tag->name;
            $values['id'] = $tag->id;
            $tags[] = $values;
        }

        if(isset($tags) AND !empty($tags)){
            return $tags;
        } else {
            array();
        }

    }

    public function getCategory($categoryId)
    {
        return ItemCategoryModel::model()->findByPk($categoryId);
    }

    public function getCategoryPath($categoryid){

        $categories = ItemCategoryModel::model()->findAllByAttributes(array('app_id'=> $this->appid));

        foreach($categories as $category){
            $list[$category->id]['name'] = $category->name;
            $list[$category->id]['id'] = $category->id;
            $list[$category->id]['parent'] = $category->parent_id;
        }

        if(isset($list)){
            return $this->getPath($list,$categoryid);
        }
    }

    private function getPath($input,$categoryid,$out=array()){

        $out[] = $this->getName($input, $categoryid);

        if(isset($input[$categoryid]['parent'])){
            $parent = $input[$categoryid]['parent'];
            $out[] = $this->getName($input, $input[$categoryid]['parent']);
            if(isset($input[$parent]['parent'])){
                $out[] = $this->getName($input, $input[$parent]['parent']);
            }
        }

        $out = array_reverse($out);
        $out = implode(' › ', $out);
        return $out;
    }

    private function getName($input,$categoryid){
        if(isset($input[$categoryid]['name'])){
            return $input[$categoryid]['name'];
        }
    }

    public function generateTree(array $array, $idKeyName = 'id', $parentIdKey = 'parent', $childNodesField = 'children')
    {
        $indexed = array();
        // first pass - get the array indexed by the primary id
        foreach ($array as $row) {
            $indexed[$row[$idKeyName]]                   = $row;
            $indexed[$row[$idKeyName]][$childNodesField] = array();
        }
        // second pass
        $root = array();

        foreach ($indexed as $id => $row) {
            $indexed[$row[$parentIdKey]][$childNodesField][$id] = &$indexed[$id];
            if (!$row[$parentIdKey]) {
                $root[$id] = &$indexed[$id];
            }
        }
        return $root;
    }


    public function saveCategory(){
        $id = $this->getMenuId();

        if(is_numeric($id)){
            $this->saveVariable('category', $id);
            $this->saveVariable('category_name', $this->getCategoryPath($id));
        }

    }

}
