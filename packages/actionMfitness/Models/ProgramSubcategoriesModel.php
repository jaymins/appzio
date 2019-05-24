<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramSubcategoriesModel extends CActiveRecord
{

    public $id;
    public $app_id;
    public $name;
    public $type;
    public $category_order;

    public function tableName()
    {
        return 'ae_ext_fit_program_subcategory';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'program' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramModel', 'program_id'),
        );
    }

    public static function getSubcategoriesWithProgram($type){
        $entries = ProgramModel::model()->with('subcategory')->findAllByAttributes([
            //'subcategory.type' => $type,
        ], [
            'condition' => 'subcategory.type = :type',
            'order' => 'subcategory.category_order ASC, subcategory.id ASC',
            'params' => [':type' => $type]
        ]);

        if (empty($entries)) {
            return [];
        }

        $data = [];

        /** @var ProgramSubcategoriesModel $entry; */
        foreach ($entries as $entry) {
            $data[] = $entry->subcategory->name;
        }

        return $data;

    }

    public static function getSubcategoriesList($type)
    {
        $entries = self::model()->findAllByAttributes([
            'type' => $type,
        ], [
            'order' => 'id ASC',
        ]);

        if (empty($entries)) {
            return [];
        }

        $data = [];

        /** @var ProgramSubcategoriesModel $entry; */
        foreach ($entries as $entry) {
            $data[] = $entry->name;
        }
        
        return $data;
    }

}