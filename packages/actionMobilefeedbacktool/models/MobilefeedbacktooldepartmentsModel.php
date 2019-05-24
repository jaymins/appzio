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

class MobilefeedbacktooldepartmentsModel extends ArticleModel {

    public $game_id;
    public $title;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public static function initDepartments($game_id)
    {
        if(!$game_id){
            return false;
        }

        /* app specific key storage */
        $storage = new AegameKeyvaluestorage();
        $storage->game_id = $game_id;
        $data = $storage->getOne('departments');
        $data = explode(chr(10),$data);

        foreach($data as $item){
            $bits = explode(';',$item);
            $id = $bits[0];
            $title = $bits[1];
            $ob = MobilefeedbacktooldepartmentsModel::model()->findByPk($id);
            if(is_object($ob) AND $title != $ob->title){
                $ob->title = $title;
                $ob->update();
            } elseif(!is_object($ob)) {
                $ob = new MobilefeedbacktooldepartmentsModel();
                $ob->title = $title;
                $ob->id = $id;
                $ob->game_id = $game_id;
                $ob->insert();
            }
        }
    }

    public function tableName()
    {
        return 'ae_ext_mobilefeedbacktool_departments';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }

    


}