<?php

class ListappointmentsModel extends ArticleModel {

	public $gid;
	
	public function getBookingInfo() {
        $sql = "SELECT gp.id, gv.name, gpv.value, gpv.play_id FROM ae_game_play AS gp
				INNER JOIN ae_game_play_variable AS gpv
					ON gp.id = gpv.play_id
				INNER JOIN ae_game_variable AS gv
					ON gpv.variable_id = gv.id
				WHERE gp.game_id = :gid
				AND gv.name = 'complete_booking_info'";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gid' => $this->gid))
            ->queryAll();

        return $rows;
	}


}