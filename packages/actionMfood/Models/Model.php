<?php


namespace packages\actionMfood\Models;

use packages\actionMcalendar\Models\CalendarModel;
use Bootstrap\Models\BootstrapModel;
use CDbCriteria;
use packages\actionMfitness\Models\Units;
use Yii;

class Model extends BootstrapModel
{


    use Units;

    /**
     * @param $recipe_id
     * @return array|mixed|null
     */
    public function getRecipe($recipe_id)
    {
        $model = RecipeModel::model();
        $list = $model->with('type')->find([
            'condition' => '`t`.`id`=:id',
            'params' => [':id' => $recipe_id]
        ]);
        return $list;
    }

    public function getFilters($type = null)
    {
        $category = @json_decode($this->getSavedVariable('categories', null));
        $key_ingredients = @json_decode($this->getSavedVariable('key_ingredients', null));

        if ($type == 'category') {
            $sql = "
                SELECT 
                    ae_ext_food_recipe_type.name as 'type',
                    ae_ext_food_recipe_type.id as 'type_id'
                FROM `ae_ext_food_recipe` 
                    INNER JOIN  ae_ext_food_recipe_type ON ae_ext_food_recipe.type_id = ae_ext_food_recipe_type.id
                    INNER JOIN ae_ext_food_recipe_ingredient ON ae_ext_food_recipe.id = ae_ext_food_recipe_ingredient.recipe_id";
            if (!empty($key_ingredients)) {
                $sql .= " WHERE ae_ext_food_recipe_ingredient.ingredient_id IN (:filter)";
            }
            $sql .= " GROUP BY type; ";

            $bind [':filter'] = (!empty($category))?implode(', ', $key_ingredients):'';
        }

        if ($type == 'ingredients') {
            $sql = "
                SELECT 
                    ae_ext_food_ingredient.name as 'ingredient',
                    ae_ext_food_ingredient.id as 'ingredient_id'
                FROM `ae_ext_food_recipe` 
                    INNER JOIN ae_ext_food_recipe_ingredient ON ae_ext_food_recipe.id = ae_ext_food_recipe_ingredient.recipe_id
                    INNER JOIN ae_ext_food_ingredient ON ae_ext_food_recipe_ingredient.ingredient_id = ae_ext_food_ingredient.id";

           if (!empty($category)) {
               $sql .= " WHERE ae_ext_food_recipe.type_id IN (:filter)";
           }
            $sql .= "    GROUP BY ingredient_id;";

            $bind [':filter'] = (!empty($category))?implode(', ', $category):'';
        }

        $result = Yii::app()->db
            ->createCommand($sql)
            ->bindValues($bind)
            ->query();
        return $result;
    }

    /**
     * @param $recipe_id
     * @return array|mixed|null|IngredientRecipeModel
     */
    public function getRecipeIngredients($recipe_id)
    {
        $model = IngredientRecipeModel::model();
        $list = $model->with([
            'recipe_ingredient'
        ])->findAll([
            'condition' => 'recipe_id = :recipe_id',
            'params' => [':recipe_id' => $recipe_id]
        ]);

        if($this->getSavedVariable('units') == 'imperial'){
            foreach($list as $key=>$item){
                $converted = $this->convertUnits($item->recipe_ingredient->unit, $item->quantity);
                $list[$key]->recipe_ingredient->unit = $converted['unit'];
                $list[$key]->quantity = $converted['value'];
            }
        }

        return $list;
    }

    public function getListIngredients($filters = [])
    {
        $criteria = new CDbCriteria();
        $criteria->with = [
            'recipe_ingredient',
            'recipe'
        ];

        if ($filters) {
            $criteria->addInCondition("recipe.type_id", $filters);
        }

        $criteria->group = 'ingredient_id';
        $criteria->order = 'recipe.name ASC';

        return IngredientRecipeModel::model()->findAll($criteria);
    }

    /**
     * @param $recipe_id
     * @return array|mixed|null
     */
    public function getRecipeSteps($recipe_id)
    {
        $sql = "
        SELECT  ae_ext_food_recipe_step.time,ae_ext_food_recipe_step.description
            FROM `ae_ext_food_recipe_step` 
            WHERE `recipe_id`= :recipe_id
        ";
        $bind = [
            ':recipe_id' => $recipe_id
        ];
        return Yii::app()->db
            ->createCommand($sql)
            ->bindValues($bind)
            ->queryAll();
    }

    public function getSummaryBarInfo($recipe_id)
    {
        $sql = "
            SELECT  sum(ae_ext_food_recipe_step.time) as 'time',ae_ext_food_recipe.difficult as 'diff', ae_ext_food_recipe.serve as 'serve',
                    ae_ext_food_recipe.total_time
                    FROM `ae_ext_food_recipe_step` 
                    LEFT JOIN ae_ext_food_recipe ON ae_ext_food_recipe.id = ae_ext_food_recipe_step.recipe_id
                    WHERE `recipe_id` = :recipe_id
                    GROUP BY recipe_id
        ";
        $bind = [
            ':recipe_id' => $recipe_id
        ];
        $result = Yii::app()->db
            ->createCommand($sql)
            ->bindValues($bind)
            ->queryALL();

        if(!isset($result['0'])){
            return array();
        }

        $time = $result[0]['total_time'] ? $result[0]['total_time'] : $result[0]['time'];

        if(!$time){
            $time = '< 60';
        }

        return [
            ['icon' => 'theme-icon-5min.png', 'name' => $time . ' min'],
            ['icon' => 'theme-icon-serving.png', 'name' => $result[0]['serve'] . ' serve'],
            ['icon' => 'theme-icon-easy.png', 'name' => $result[0]['diff']]
        ];
    }

    public function getNutritionList($filters = [], $order = null)
    {

        //if(isse)

        $criteria = new CDbCriteria();
        $criteria->with = 'recipe';

        if (isset($filters['ingredient_id']) && $filters['ingredient_id']) {
            foreach($filters['ingredient_id'] as $ingredient){
                if(strlen($ingredient) > 1){
                    $conditions[] = "recipe_ingredient.name = '$ingredient'";
                }
            }
            $criteria->condition = '('.implode(' AND ', $conditions).')';
        }


        if (isset($filters['type_id']) && $filters['type_id']) {
            $criteria->addInCondition("type_id", $filters['type_id']);
        }

        $keyword = $this->getSavedVariable('recipe_filter_keyword');

        if($keyword){
            $criteria->addCondition("recipe.name COLLATE utf8_general_ci LIKE '%$keyword%'");
        }

        $criteria->group = 'recipe_id';
        $criteria->limit = 20;
        if(isset($_REQUEST['next_page_id'])){
            $criteria->offset = $_REQUEST['next_page_id'];
        }

        if ($order) {
            $criteria->order = 'name ' . $order;
        }

        return IngredientRecipeModel::model()->with('recipe_ingredient')->findAll($criteria);

    }

    /**
     * @param null $type_id
     * @param null $order
     * @return array|mixed|mixed[]|null|RecipeModel[]
     */
    public function getRecipeList($type_id = null, $order = 'ASC')
    {
        $criteria = new CDbCriteria();
        if ($type_id) {
            $criteria->addInCondition("type_id", $type_id);
        }
        if ($order) {
            $criteria->order = 'name ' . $order;
        }
        return RecipeModel::model()->findAll($criteria);
    }

    /**
     * SELECT * FROM `ae_ext_food_recipe` as recipe
     * left join    ae_ext_food_recipe_type as type on `type_id`= type.id
     * left join    ae_ext_food_recipe_ingredient as recipe_i on recipe.id = recipe_i.recipe_id
     *
     * WHERE recipe_i.ingredient_id IN ('4')
     * GROUP BY type_id
     * @return array|mixed|mixed[]|null|RecipeTypeModel[]
     */
    public function getRecipeCategories($filters = [])
    {
        $criteria = new CDbCriteria();
        $criteria->distinct = "type_id";
        $criteria->alias = 'recipe';
        $criteria->join = "left join 	ae_ext_food_recipe_type as type on `recipe`.`type_id`= type.id";

        if ($filters) {

            $criteria->join = "left join 	ae_ext_food_recipe_ingredient as recipe_i on recipe.id = recipe_i.recipe_id";
            $criteria->addInCondition("recipe_i.ingredient_id", $filters);
        }

        return RecipeModel::model()->findAll($criteria);
    }

    /* public function getUnits()
     {

     }*/

    /**
     * @param $recipe_id
     * @return array|mixed|null
     */
    public function addCustomIngredient($name, $type)
    {

        if(!is_numeric($type)){
            $typeobj = IngredientTypeModel::model()->findByAttributes(['name' => $type]);
            $type = $typeobj->id;
        }

        $model = new IngredientCustomModel();
        $model->play_id = $this->playid;
        $model->name = $name;
        $model->ingredient_category = $type;
        $model->date = time();
        $model->validate();
        $model->insert();
        return true;

    }

    public function addCompleteRecord($data)
    {
        $model = new ShoppingList();
        $model->play_id = $this->playid;
        $model->ingredient_id = $data['id'];
        $model->quantity = $data['quantity'];
        $model->date_from = $data['date_from'];
        $model->date_to = $data['date_to'];
        $model->validate();
        $model->insert();
        return true;
    }

    public function removeCompleteRecord($id)
    {
        ShoppingList::model()->deleteAll('id = :id',
            [':id' => $id]);
    }

    private function getPendingProducts()
    {
        return "
            SELECT 
                SUM(ae_ext_food_recipe_ingredient.quantity) as 'sum_quantity',
                null as 'custom_id',
                ae_ext_food_ingredient.id as 'ingredient_id',
                ae_ext_food_ingredient.name as 'ingredient_name',
                ae_ext_food_ingredient.unit as 'ingredient_unit',
                ae_ext_food_ingredient.category_id as 'category_id',
                ae_ext_food_ingredient_category.name as 'category_name',
                ae_ext_food_ingredient_category.icon as 'category_icon'
            FROM `ae_ext_food_ingredient_category`
                LEFT JOIN ae_ext_food_ingredient ON `ae_ext_food_ingredient`.category_id = ae_ext_food_ingredient_category.id
                LEFT JOIN ae_ext_food_recipe_ingredient ON ae_ext_food_recipe_ingredient.ingredient_id = ae_ext_food_ingredient.id
                LEFT JOIN ae_ext_food_recipe ON ae_ext_food_recipe_ingredient.recipe_id = ae_ext_food_recipe.id
                INNER JOIN ae_ext_calendar_entry on ae_ext_food_recipe.id = ae_ext_calendar_entry.recipe_id
            WHERE  ae_ext_calendar_entry.is_completed = 0 
            AND ae_ext_calendar_entry.play_id = :play_id
            AND (ae_ext_calendar_entry.time >= :from_date 
            AND ae_ext_calendar_entry.time <= :to_date)
            GROUP BY ingredient_name
        ";
    }

    private function getCustomProducts()
    {
        return "SELECT
                1 as 'sum_quantity',
                ae_ext_food_custom_ingredient.id as 'custom_id',
                null as 'ingredient_id',
                ae_ext_food_custom_ingredient.name as 'ingredient_name',
                null as 'ingredient_unit',
                ae_ext_food_custom_ingredient.ingredient_category as 'category_id',
                ae_ext_food_ingredient_category.name as 'category_name',
                ae_ext_food_ingredient_category.icon as 'category_icon'
            FROM `ae_ext_food_ingredient_category`
                LEFT JOIN ae_ext_food_custom_ingredient ON ae_ext_food_custom_ingredient.ingredient_category = ae_ext_food_ingredient_category.id
                LEFT JOIN ae_ext_food_shopping_list ON ae_ext_food_shopping_list.ingredient_id = ae_ext_food_custom_ingredient.id
            WHERE  ae_ext_food_custom_ingredient.play_id = :play_id 
            ###AND ae_ext_food_shopping_list.quantity > 0
            AND ae_ext_food_custom_ingredient.date >= :from_date
            AND ae_ext_food_custom_ingredient.date <= :to_date
            GROUP BY ingredient_name ";
    }


    private function getCompletedShoppingItems()
    {
        $sql = "
                SELECT 
                  ae_ext_food_shopping_list.id as 'id',
                  SUM(ae_ext_food_shopping_list.quantity) as 'sum_quantity_b',
                  ae_ext_food_shopping_list.ingredient_id as 'ingredient_id',
                  ae_ext_food_shopping_list.date_to,
                  ae_ext_food_shopping_list.date_from
                FROM `ae_ext_food_ingredient_category`
                    LEFT JOIN `ae_ext_food_ingredient`  ON `ae_ext_food_ingredient`.category_id = ae_ext_food_ingredient_category.id
                    INNER JOIN ae_ext_food_shopping_list ON ae_ext_food_shopping_list.ingredient_id = ae_ext_food_ingredient.id
                WHERE ae_ext_food_shopping_list.play_id = :play_id
                AND (
                (date_from >= :from_date AND date_from <= :to_date) OR 
                (date_from >= :from_date AND date_to <= :to_date) OR 
                (date_to >= :from_date AND date_to <= :to_date)
                )
                GROUP BY `ingredient_id`
            ";

        return $sql;
    }

    private function getCompletedCustomItems()
    {
        $sql = "
                SELECT 
                  ae_ext_food_shopping_list.id as 'id',
                  SUM(ae_ext_food_shopping_list.quantity) as 'sum_quantity_b',
                  ae_ext_food_shopping_list.ingredient_id as 'ingredient_id',
                  ae_ext_food_shopping_list.date_to,
                  ae_ext_food_shopping_list.date_from
                FROM `ae_ext_food_ingredient_category`
                    LEFT JOIN `ae_ext_food_custom_ingredient`  ON `ae_ext_food_custom_ingredient`.ingredient_category = ae_ext_food_ingredient_category.id
                    INNER JOIN ae_ext_food_shopping_list ON ae_ext_food_shopping_list.ingredient_id = ae_ext_food_custom_ingredient.id
                WHERE ae_ext_food_shopping_list.play_id = :play_id
                AND (
                (date_from >= :from_date AND date_from <= :to_date) OR 
                (date_from >= :from_date AND date_to <= :to_date) OR 
                (date_to >= :from_date AND date_to <= :to_date)
                )
                GROUP BY `ingredient_id`
            ";

        return $sql;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getShoppingList()
    {
        $pending_products = $this->getIngredientCalculations(
            $this->getPendingProducts(),
            'recipe_ingredients'
        );
        $custom_products = $this->getIngredientCalculations(
            $this->getCustomProducts(),
            'custom_ingredients'
        );

        $result = array_merge($pending_products, $custom_products);

        return $this->mapShoppingList($result);
    }

    private function getIngredientCalculations($shoppingList, $table)
    {
        $sql = "SELECT (case when ( t2.sum_quantity_b >  0)
             THEN
                (t1.sum_quantity - t2.sum_quantity_b)
             ELSE
                t1.sum_quantity
             END) as 'quantity',
            t1.sum_quantity as 'order_quantity',
            t1.custom_id as 'custom_id',
            t1.ingredient_id as 'ingredient_id',
            t1.ingredient_name as 'name',
            t1.ingredient_unit as 'unit',
            t1.category_id as 'category_id',
            t1.category_name as 'category_name',
            t1.category_icon as 'category_icon',
            t2.ingredient_id as 'check',
             t2.id as 'complete_id'";

        $sql .= 'FROM (' . $shoppingList . ' ) t1 ';
        if ($table == 'recipe_ingredients') {
            $sql .= 'LEFT JOIN (' . $this->getCompletedShoppingItems() . ') as t2 on t1.ingredient_id = t2.ingredient_id';
        } else {
            $sql .= 'LEFT JOIN (' . $this->getCompletedCustomItems() . ') as t2 on t1.custom_id = t2.ingredient_id';
        }

        $start_date = time();
        $to_date = strtotime('+7 days', time());

        $bind = [
            ':play_id' => $this->playid,
            ':from_date' => $this->getSavedVariable('start_date', $start_date),
            ':to_date' => $this->getSavedVariable('to_date', $to_date),
        ];

        return Yii::app()->db
            ->createCommand($sql)
            ->bindValues($bind)
            ->queryAll();
    }

    public function mapShoppingList($list)
    {
        $arr = [];
        $type = array_unique(array_column($list, 'category_id'));
        foreach ($list as $item) {
            foreach ($type as $cat) {
                if ($item['category_id'] === $cat) {
                    $arr[$cat]['category'] = ['icon' => $item['category_icon'],
                        'name' => $item['category_name']];
                    $arr[$cat]['item'][] = $item;
                }
            }
        }
        return $arr;
    }

    public function getIngredientCategory()
    {
        $criteria = new CDbCriteria();
        $criteria->select = ["id", "name"];
        $criteria->order = 'name ASC';

        $list = IngredientTypeModel::model()->findAll($criteria);
        $type_list = [];

        foreach ($list as $type) {

            if ($type['name']) {
                $type_list[$type['id']] = $type['name'];
            }
        }

        if (empty($type_list)) {
            return [];
        }
        return $type_list;
    }

    public function getHourSelectorData()
    {
        return '4;4am;5;5am;6;6am;7;7am;8;8am;9;9am;10;10am;11;11am;12;12:00;13;1pm;14;2pm;15;3pm;16;4pm;17;5pm;18;6pm;19;7pm;20;8pm;21;9pm;22;10pm;23;11pm';
    }

    public function getHourSelectorData24h()
    {
        return '4;4;5;5;6;6;7;7;8;8;9;9;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23';
    }

    public function getMinuteSelectorData()
    {
        return '0;00;15;15;30;30;45;45';
    }

    public function addToCalendar($recipe_id, $date)
    {
        CalendarModel::insertEntries([
            [
                'play_id' => $this->playid,
                'type_id' => 7, // Nutrition
                'exercise_id' => null,
                'program_id' => null,
                'recipe_id' => $recipe_id,
                'notes' => '',
                'points' => 8,
                'time' => $date,
                'completion' => 0,
                'is_completed' => 0,
            ]
        ]);
    }

    public function editCalendar($calendar_id, $recipe_id)
    {
        $calendar_entry = CalendarModel::model()->findByPk($calendar_id);
        $calendar_entry->recipe_id = $recipe_id;
        $calendar_entry->save();
        return true;

    }

    public function saveFilters()
    {
        if($this->getSubmittedVariableByName('keyword')){
            $this->saveVariable('recipe_filter_keyword', $this->getSubmittedVariableByName('keyword'));
        }

        if($ingredients = $this->getSubmittedVariableByName('key_ingredients')){
            $ingredients = explode(',', $ingredients);

            foreach ($ingredients as $ingredient){
                if(strlen($ingredient) > 1){
                    $encode[] = $ingredient;
                }
            }

            if(isset($encode)){
                $ingredients = json_encode($encode);
                $this->saveVariable('key_ingredients', $ingredients);
            }
        }
    }

}