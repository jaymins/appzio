<?php

class MobilepropertiesBookmarkModel extends ArticleModel
{
    public $id;
    public $play_id;
    public $mobileproperty_id;
    public $status;

    const STATUS_LIKE = 'like';
    const STATUS_SKIP = 'skip';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileproperty_bookmark';
    }

    public function relations()
    {
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
            'property' => array(self::BELONGS_TO, 'MobilepropertiesModel', 'mobileproperty_id')
        );
    }

    /**
     * Store a bookmark in the database
     *
     * @param $propertyId
     * @param $userId
     * @param $status
     * @param $gameId
     */
    protected static function store($propertyId, $userId, $status, $gameId)
    {
        $model = new MobilepropertiesBookmarkModel();
        $model->mobileproperty_id = $propertyId;
        $model->play_id = $userId;
        $model->game_id = $gameId;
        $model->status = $status;
        $model->insert();
    }

    /**
     * Mark a property as liked
     *
     * @param $propertyId
     * @param array|null $playId
     */
    public static function like($propertyId, $playId, $gameId)
    {
        $exists = self::getStatus($propertyId, $playId, $gameId);

        if ( ! $exists) {
            self::store($propertyId, $playId, self::STATUS_LIKE, $gameId);
        }
    }

    /**
     * Mark a property as skipped
     *
     * @param $propertyId
     * @param $playId
     */
    public static function skip($propertyId, $playId, $gameId)
    {
        $exists = self::getStatus($propertyId, $playId, $gameId);

        if ( ! $exists) {
            self::store($propertyId, $playId, self::STATUS_SKIP, $gameId);
        }
    }


    public static function deleteBookmark($propertyId, $userId, $gameId)
    {
        $bookmark = self::model()->find(array(
            'condition' => 'play_id = :playId AND game_id = :gameId AND mobileproperty_id = :propertyId',
            'params' => array(
                ':playId' => $userId,
                ':gameId' => $gameId,
                ':propertyId' => $propertyId
            )
        ));

        $bookmark->delete();
    }

    /**
     * Delete bookmark
     *
     * @param $propertyId
     * @param $userId
     */
    public static function remove($propertyId, $userId, $gameId)
    {
        $bookmark = self::model()->find(array(
            'condition' => 'play_id = :playId AND game_id = :gameId AND mobileproperty_id = :propertyId',
            'params' => array(
                ':playId' => $userId,
                ':propertyId' => $propertyId
            )
        ));

        $bookmark->status = 'remove';
        $bookmark->update();
    }


    /**
     * Get count of bookmarked properties from an agent/landlord
     *
     * @param int $userId
     * @param int $agentId
     * @return int
     */
    public static function bookmarkedPropertiesForAgent(int $userId, int $agentId, int $gameId)
    {
        $properties = MobilepropertiesModel::model()->findAll(array(
            'select' => 'id',
            'condition' => 'play_id = :playId AND game_id = :gameId',
            'params' => array(':playId' => $agentId, ':gameId' => $gameId)
        ));

        $propertyIds = array();

        foreach ($properties as $property) {
            $propertyIds[] = $property['id'];
        }

        $bookmarks = Yii::app()->db->createCommand()
            ->select('id')
            ->from('ae_ext_mobileproperty_bookmark')
            ->where('play_id = :playId AND game_id = :gameId', array(':playId' => $userId, ':gameId' => $gameId))
            ->where(array('in', 'mobileproperty_id', $propertyIds))
            ->query();

        return count($bookmarks);
    }

    /**
     * Get the bookmarks of a user together with their properties
     *
     * @param int $userId
     * @param int $gameId
     * @return array
     */
    public static function getBookmarkedProperties($userId, $gameId)
    {
        $bookmarks = self::model()->with('property')->findAll(array(
            'select' => '*',
            'condition' => 't.play_id = :playId AND t.game_id = :gameId AND status = "' . self::STATUS_LIKE . '"',
            'params' => array(':playId' => $userId, ':gameId' => $gameId)
        ));

        $properties = array();

        foreach ($bookmarks as $bookmark) {
            $properties[] = $bookmark->property;
        }

        return $properties;
    }

    /**
     * Get bookmark status for given property
     *
     * @param $propertyId
     * @param $userId
     * @return mixed|null
     */
    public static function getStatus($propertyId, $userId, $gameId)
    {
        $bookmark = self::model()->find(array(
            'select' => 'status',
            'condition' => 'play_id = :playId AND game_id = :gameId AND mobileproperty_id = :propertyId',
            'params' => array(
                ':playId' => $userId,
                ':gameId' => $gameId,
                ':propertyId' => $propertyId
            )
        ));

        if (!$bookmark) {
            return null;
        }

        return $bookmark->status;
    }
}