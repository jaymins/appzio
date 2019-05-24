<?php

namespace backend\modules\users\models;

use Yii;
use \backend\modules\users\models\base\AeGamePlayVariable as BaseAeGamePlayVariable;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_game_play_variable".
 */
class AeGamePlayVariable extends BaseAeGamePlayVariable
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public function getPlayVariables( $play_id ) {

        $connection = Yii::$app->db;

        $sql = "SELECT ae_game_play_variable.*,
                ae_game_variable.name AS variable_name
                FROM ae_game_play_variable
                LEFT JOIN ae_game_variable
                    ON ae_game_variable.id = ae_game_play_variable.variable_id
                WHERE ae_game_play_variable.play_id = :play_id
                GROUP BY ae_game_play_variable.id";

        $args = array(
            ':play_id' => $play_id,
        );

        $command = $connection->createCommand( $sql );

        if ( !empty($args) ) {
            $command->bindValues( $args );
        }
        
        $vars = $command->queryAll();

        $output = array();

        if ( $vars ) {
            $output = array();

            foreach($vars as $var){
                $name = $var['variable_name'];
                $value = $var['value'];
                $var_id = $var['variable_id'];
                $output[$name] = array(
                    'id' => $var_id,
                    'value' => $value,
                );
            }
        }

        return $output;
    }

}