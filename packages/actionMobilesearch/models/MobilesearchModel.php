<?php

class MobilesearchModel extends ArticleModel {


    public static function searchUsers($searchterm,$gid,$limit='0,50'){

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


}