<?php


namespace packages\actionMnotifications\Models;
use Bootstrap\Models\BootstrapModel;
use CActiveRecord;

class NotificationsModel extends CActiveRecord {

    public $id;
    public $app_id;
    public $playid;
    public $theme;
    public $task_id;

    public $email_to;

    /**
     * this refers to record in ae_notification table and via this
     * you can get delivery info etc. Note however, that the content
     * of ae_notification table is not persistent, it is usually flushed
     * every seven days
     */
    public $notification_id;
    public $play_id_from;
    public $play_id_to;

    /**
     * if there is no play_id_to, we should fill it with target users email
     * address to fill in the play_id at later stage (upon registration). If this
     * is used, you should make the you can register with the same email only once
     */
    public $temporary_email;
    public $subject;
    public $msg;
    public $status;

    /**
     * all these should be either 0 or 1
    */
    public $push;
    public $email;
    public $sms;

    public $created_date;
    public $read_date;
    public $icon;
    public $image;

    public $item_id;
    public $type;

    /**
     * @var BootstrapModel;
     */
    public $model;

    /**
     * when user clicks on a link (short links) it can link directly to an action & submit a menu command
     */
    public $action_id;
    public $action_param;

    /**
     * we use this with universal urls
     */
    public $shorturl;

    public function tableName()
    {
        return 'ae_ext_notifications';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            //'products' => array(self::BELONGS_TO, 'ProductitemsModel','product_id'),
        );
    }

    /**
     * Inserts first a notification row which can be accessed by the action. Then it
     * register the notification for Appzio's notification system either as a push or email,
     * depending on whether to_play_id has been defined or not.
     *
     * @param array $parameters
     * <code>
     * to_playid=false,
     *
     * to_email=false,
     *
     * subject = '',
     *
     * $msg='',
     *
     * action_id=false,
     *
     * action_param='',
     *
     * image='',
     *
     * type='invitation'
     *
     * </code>
     *
     *
     *
     */

    public function addNotification(array $parameters){

        $toplay = isset($parameters['to_playid']) ? $parameters['to_playid'] : null;
        $email = isset($parameters['to_email']) ? $parameters['to_email'] : '';
        $subject = isset($parameters['subject']) ? $parameters['subject'] : '';
        $msg = isset($parameters['msg']) ? $parameters['msg'] : '';
        $image = isset($parameters['image']) ? $parameters['image'] : '';
        $type = isset($parameters['type']) ? $parameters['type'] : '';
        $action_id = isset($parameters['action_id']) ? $parameters['action_id'] : null;
        $action_param = isset($parameters['action_param']) ? $parameters['action_param'] : '';
        $id = isset($parameters['id']) ? $parameters['id'] : false;

        $test = \Aeplay::model()->findByPk($toplay);

        if(!$test){
            $toplay = null;
        }

        $obj = new NotificationsModel();
        $obj->app_id = $this->app_id;
        $obj->play_id_from = $this->playid;
        $obj->play_id_to = $toplay;
        $obj->temporary_email = $email;
        $obj->subject = $subject;
        $obj->msg = $msg;
        $obj->status = 'created';
        $obj->notification_id = null;
        $obj->created_date = time();
        $obj->action_id = $action_id;
        $obj->action_param = $action_param;
        $obj->type = $type;
        $obj->item_id = $id;

        if($toplay){
            $obj->push = 1;
        }

        if($email AND !$toplay){
            $obj->email_to = $email;
        }

        $obj->image = $image;

        if($type){
            $obj = $this->handleType($obj,$type,$id);
        }

        unset($obj->playid);
        unset($obj->theme);

        $obj->insert();

        /* this register the notification to Appzio notification system */
        $clickobj = new \stdClass();
        $clickobj->action = 'open-action';
        $clickobj->action_config = $action_id;
        $clickobj->id = $action_param;

        if($obj->push AND $toplay){
            $notification_id = \Aenotification::addUserNotification($toplay, $subject, $msg,+1,$this->app_id,$clickobj);
        }

        if($obj->email_to AND $email){
            $notification_id = \Aenotification::addUserEmail($this->playid, $subject, $msg, $this->app_id,$email,$clickobj);
        }

        if(isset($notification_id) AND $notification_id){
            $obj->notification_id = $notification_id;
            $obj->update();
        }

    }


    /**
     * Little helper for enriching / rewriting based on the type. This would typically be set in the theme's own file.
     * @param $obj \stdClass
     * @param $type string
     * @return \stdClass
     */
    public function handleType($obj, $type, $id=false){
        switch($type){
            case 'delivery':
                $obj->icon = 'notifications-delivery-icon.png';
                break;

            case 'task':
                $obj->icon = 'notifications-task-icon.png';
                break;

            case 'match':
                $obj->icon = 'notifications-match-icon.png';
                break;

            case 'message':
                $obj->icon = 'notifications-message-icon.png';
                break;

            case 'invitation':
                $obj->icon = 'notifications-invitation-icon.png';
                break;

            default:
                $obj->icon = 'notifications-invitation-icon.png';
                break;
        }

        return $obj;
    }

    public static function getMyNotificationCount($playid){
        $sql = "SELECT count(ae_ext_notifications.id) as count
                  FROM ae_ext_notifications 
                  WHERE ae_ext_notifications.play_id_to = :playID
                  AND read_date < 1
                  ";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':playID' => $playid,
                )
            )

            ->queryAll();

        if(isset($rows[0])){
            return $rows[0]['count'];
        }

        return 0;

    }



}
