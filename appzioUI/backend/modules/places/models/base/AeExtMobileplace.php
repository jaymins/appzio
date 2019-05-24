<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\places\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_mobileplaces".
 *
 * @property string $id
 * @property string $game_id
 * @property string $lat
 * @property string $lon
 * @property string $last_update
 * @property string $name
 * @property string $address
 * @property integer $zip
 * @property string $city
 * @property string $county
 * @property string $country
 * @property string $info
 * @property string $logo
 * @property string $images
 * @property integer $premium
 * @property string $headerimage1
 * @property string $headerimage2
 * @property string $headerimage3
 * @property integer $import_date
 * @property string $code
 * @property string $hex_color
 *
 * @property \backend\modules\places\models\AeExtGolfHole[] $aeExtGolfHoles
 * @property \backend\modules\places\models\AeExtMobileevent[] $aeExtMobileevents
 * @property \backend\modules\places\models\AeGame $game
 * @property \backend\modules\places\models\AeLocationLog[] $aeLocationLogs
 * @property string $aliasModel
 */
abstract class AeExtMobileplace extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_mobileplaces';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id', 'name', 'address', 'zip', 'city', 'info', 'code'], 'required'],
            [['game_id', 'zip', 'premium', 'import_date'], 'integer'],
            [['last_update'], 'safe'],
            [['info', 'images'], 'string'],
            [['name', 'address', 'city', 'county', 'country', 'code'], 'string', 'max' => 255],
            [['hex_color'], 'string', 'max' => 7],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\places\models\AeGame::className(), 'targetAttribute' => ['game_id' => 'id']]
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
            'lat' => Yii::t('backend', 'Lat'),
            'lon' => Yii::t('backend', 'Lon'),
            'last_update' => Yii::t('backend', 'Last Update'),
            'name' => Yii::t('backend', 'Name'),
            'address' => Yii::t('backend', 'Address'),
            'zip' => Yii::t('backend', 'Zip'),
            'city' => Yii::t('backend', 'City'),
            'county' => Yii::t('backend', 'County'),
            'country' => Yii::t('backend', 'Country'),
            'info' => Yii::t('backend', 'Info'),
            'logo' => Yii::t('backend', 'Logo'),
            'images' => Yii::t('backend', 'Images'),
            'premium' => Yii::t('backend', 'Premium'),
            'headerimage1' => Yii::t('backend', 'Headerimage1'),
            'headerimage2' => Yii::t('backend', 'Headerimage2'),
            'headerimage3' => Yii::t('backend', 'Headerimage3'),
            'import_date' => Yii::t('backend', 'Import Date'),
            'code' => Yii::t('backend', 'Code'),
            'hex_color' => Yii::t('backend', 'Hex Color'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtGolfHoles()
    {
        return $this->hasMany(\backend\modules\places\models\AeExtGolfHole::className(), ['place_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileevents()
    {
        return $this->hasMany(\backend\modules\places\models\AeExtMobileevent::className(), ['place_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(\backend\modules\places\models\AeGame::className(), ['id' => 'game_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeLocationLogs()
    {
        return $this->hasMany(\backend\modules\places\models\AeLocationLog::className(), ['place_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\places\models\query\AeExtMobileplaceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\places\models\query\AeExtMobileplaceQuery(get_called_class());
    }


}
