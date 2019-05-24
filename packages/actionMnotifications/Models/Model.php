<?php


namespace packages\actionMnotifications\Models;
use Aenotification;
use Bootstrap\Models\BootstrapModel;
use function is_numeric;
use packages\actionMearnster\Models\ConfigureMenu;
use function str_replace;
use function stristr;
use function strtolower;
use function ucwords;

class Model extends BootstrapModel {

    use ConfigureMenu;


    public function getMarkReadMenu(){
        if(isset($_REQUEST['menuid'])){

            foreach ($this->menus['menus'] as $menu){
                if(isset($menu['items'])){
                    foreach($menu['items'] as $item){
                        if(isset($menu['slug']) AND $menu['slug'] == 'markread' AND $_REQUEST['menuid'] == $item['id']){
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function getMyNotifications(){
        
        $sql = "SELECT username.value as username, 
                    profilepic.value as profilepic,
                    ae_ext_notifications.*
                  FROM ae_ext_notifications 
                  LEFT JOIN ae_game_play_variable AS username ON ae_ext_notifications.play_id_from = username.play_id AND username.variable_id = :username
                  LEFT JOIN ae_game_play_variable AS profilepic ON ae_ext_notifications.play_id_from = profilepic.play_id AND profilepic.variable_id = :profilepic
                  WHERE ae_ext_notifications.play_id_to = :playID
                  GROUP BY ae_ext_notifications.id
                  ORDER BY status,id DESC";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':playID' => $this->playid,
                    ':username' => $this->getVariableId('firstname'),
                    ':profilepic' => $this->getVariableId('profilepic')
                )
            )

            ->queryAll();

        if(isset($rows[0])){
            return $rows;
        }

        return array();

    }

    public function markRead($id){
        $obj = NotificationsModel::model()->findByPk($id);
        $obj->status = 'read';
        $obj->read_date = time();
        $obj->update();
    }

    public function markAllRead(){
        $sql = "UPDATE ae_ext_notifications SET `read_date` = :date, `status` = 'read' WHERE play_id_to = :playid";

        \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':playid' => $this->playid,
                    ':date' => time(),
                )
            )
            ->query();
    }

    public function deleteNotification($id) {
        return NotificationsModel::model()->deleteByPk($id);
    }



}
