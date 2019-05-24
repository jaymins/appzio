<?php

namespace backend\modules\tatjack\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tatjack\models\AeExtBooking]].
 *
 * @see \backend\modules\tatjack\models\AeExtBooking
 */
class AeExtBookingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtBooking[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtBooking|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
