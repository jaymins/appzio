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

Yii::import('application.modules.aelogic.controllers.*');
Yii::import('application.modules.aechat.models.*');

class MobileadminModel extends ArticleModel {


    public static function getAllUsers($gid){



    }

    public static function searchVariables($searchterm,$gid,$limit='0,50'){

        if($searchterm == false){
            $ss = false;
        } else {
            $ss = "AND `value` LIKE '%$searchterm%'";
        }

        $sql = "SELECT *,ae_game_play_variable.play_id AS playid FROM `ae_game_play_variable`
                        LEFT JOIN ae_game_play ON ae_game_play_variable.play_id = ae_game_play.id
                        LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                        LEFT JOIN ae_ext_mobilematching ON ae_game_play.id = ae_ext_mobilematching.play_id
                        WHERE ae_game_play.game_id =:gameId
                        $ss
                        GROUP BY playid
                        ORDER BY ae_ext_mobilematching.play_id DESC                                                                      
                        LIMIT $limit
                        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':gameId' => $gid,
            ))
            ->queryAll();

        return $rows;

    }

    public static function getNonActivatedUsers($gid){

        $sql = "SELECT *,ae_game_play_variable.play_id AS playid FROM `ae_game_play_variable` 
                        LEFT JOIN ae_game_play ON ae_game_play_variable.play_id = ae_game_play.id
                        LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                        LEFT JOIN ae_ext_mobilematching ON ae_game_play.id = ae_ext_mobilematching.play_id
                        WHERE ae_ext_mobilematching.`match_always` = '0'
                        AND ae_game_play.game_id =:gameId
                        GROUP BY ae_game_play.id
                        ORDER BY ae_game_play.id DESC
                        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':gameId' => $gid,
            ))
            ->queryAll();

        return $rows;

    }


    public static function getFlaggedUsers($gid){

        $sql = "SELECT *,ae_game_play_variable.play_id AS playid  FROM `ae_game_play_variable`
                        LEFT JOIN ae_game_play ON ae_game_play_variable.play_id = ae_game_play.id
                        LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                        LEFT JOIN ae_ext_mobilematching ON ae_game_play.id = ae_ext_mobilematching.play_id
                        WHERE `flag` > '0'
                        AND ae_game_play.game_id =:gameId

                        GROUP BY ae_game_play.id
                        ORDER BY flag DESC
                        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':gameId' => $gid,
            ))
            ->queryAll();

        return $rows;

    }




}