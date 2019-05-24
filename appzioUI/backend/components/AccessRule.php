<?php
 
namespace backend\components;
 
use Yii;
use backend\models\User;

class AccessRule extends \yii\filters\AccessRule {
 
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {

        if (empty($this->roles)) {
            return true;
        }

        if ( !isset(Yii::$app->session['priv']) ) {
            return false;
        }

        $priv = Yii::$app->session['priv'];

        foreach ($this->roles as $role) {
            if ( $role == $priv ) {
                return true;
            }
        }
 
        return false;
    }
    
}