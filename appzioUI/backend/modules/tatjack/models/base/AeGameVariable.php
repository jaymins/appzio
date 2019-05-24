<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tatjack\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_game_variable".
 *
 * @property string $id
 * @property string $game_id
 * @property string $name
 * @property string $used_by_actions
 * @property integer $set_on_players
 * @property resource $value_type
 *
 * @property \backend\modules\tatjack\models\AeGamePlayVariable[] $aeGamePlayVariables
 * @property \backend\modules\tatjack\models\AeGame $game
 * @property string $aliasModel
 */
abstract class AeGameVariable extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_game_variable';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id', 'name', 'used_by_actions', 'set_on_players'], 'required'],
            [['game_id', 'set_on_players'], 'integer'],
            [['used_by_actions'], 'string'],
            [['name', 'value_type'], 'string', 'max' => 255],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tatjack\models\AeGame::className(), 'targetAttribute' => ['game_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'game_id' => Yii::t('backend', 'Game ID'),
            'name' => Yii::t('backend', 'Name'),
            'used_by_actions' => Yii::t('backend', 'Used By Actions'),
            'set_on_players' => Yii::t('backend', 'Set On Players'),
            'value_type' => Yii::t('backend', 'Value Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayVariables()
    {
        return $this->hasMany(\backend\modules\tatjack\models\AeGamePlayVariable::className(), ['variable_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(\backend\modules\tatjack\models\AeGame::className(), ['id' => 'game_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\query\AeGameVariableQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tatjack\models\query\AeGameVariableQuery(get_called_class());
    }


}
