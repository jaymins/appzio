<?php

namespace packages\actionMfood\themes\uikit\Models;

use packages\actionMcalendar\Models\CalendarModel;
use packages\actionMfood\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{

    public function getRecipeIDFromCalendar(int $calendar_item_id)
    {
        if (empty($calendar_item_id)) {
            return false;
        }

        $calendar_entry = CalendarModel::model()->findByPk($calendar_item_id);

        if (empty($calendar_entry) OR empty($calendar_entry->recipe_id)) {
            return false;
        }

        return $calendar_entry->recipe_id;
    }

}