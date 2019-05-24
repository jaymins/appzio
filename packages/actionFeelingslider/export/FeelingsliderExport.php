<?php
/* array for excel sheet looks like this
 $postsArr = array ('0' => array(
        'userEmail'=>'dora@east.fi',
	    'userPhone' =>'1111111',
		'userFirstName' => 'Dora',
		'userLastName' => 'Stoyanova',
		'VariableName1' => 'VariableValue1',
		'VariableName2' => 'VariableValue2',
		'VariableName3' => 'VariableValue3',
	    )
   );
 */

/* export class must contain at least ExportGame method */


class FeelingsliderExport 
{
 public $postsArr=array();


   public function ExportGame($gid){
         $sql ="SELECT ae_game_play.user_id AS userid,
		               ae_game_play_variable.value as variableValue,
					   ae_game_play_variable.parameters as variableParameters,
					   usergroups_user.*
             FROM ae_game_play
             LEFT JOIN usergroups_user ON usergroups_user.id=ae_game_play.user_id
			 LEFT JOIN ae_game_play_variable ON ae_game_play.id= ae_game_play_variable.play_id 
             WHERE ae_game_play.game_id=:gameID
			 GROUP BY ae_game_play.id";
	
	
	  $result = Yii::app()->db
            ->createCommand($sql)
			->bindValues(array(':gameID' => $gameid))
            ->queryAll();  
		
	 return  $result;
    }

  
  
  
   public function exportAction($ai) {
	  
	    
		$rows= FeelingsliderExport::getUsersVars($ai); 
		
		  
        while($row = each($rows)){
            $r = $row[1];
			$this->postsArr[$r['userId']]['userId']=$r['userId'];
			$this->postsArr[$r['userId']]['userEmail']=$r['userEmail'];
			$this->postsArr[$r['userId']]['userFirstName']=$r['userFirstName'];
			$this->postsArr[$r['userId']]['userLastName']=$r['userLastName'];
			$this->postsArr[$r['userId']]['Variables'][$r['variableId']]= array($r['variableName'] => strip_tags($r['variableValue']));
		}
		
		
		foreach ($this->postsArr as $k=>$v){
			if (isset($v['Variables'])) {
			  foreach ($v['Variables'] as $n=>$s){
			  
			             foreach ($s as $nn=>$ss){
				           $this->postsArr[$k][$nn]=$ss;
						 }
			  }
			 
			}
			 unset($this->postsArr[$k]['Variables']);
		}
		
	   
	   return $this->postsArr;
  }
   
   
     
  
  
   public static function getUsersVars($ai) {
	
      $rowsVars=array();
	  
	  
		$sql="SELECT config 
              FROM ae_game_branch_action
			  WHERE ae_game_branch_action.id=:actionId";
			  
		$rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $ai))
            ->queryAll();
		
			
		  $variableId=0;
		 
		  if (isset($rows[0]['config'])) {
			$config=json_decode($rows[0]['config']);
			$variableId=$config->variable;
		  }
		
		  if ($variableId!=0) {
          $sql = 'SELECT ae_game_play.user_id AS userId,
		               usergroups_user.email AS userEmail,
					   usergroups_user.firstname AS userFirstName,
					   usergroups_user.lastname AS userLastName,
					   ae_game_variable.id as variableId,
		               ae_game_variable.name as variableName,
		               ae_game_play_variable.value as variableValue
             FROM ae_game_play_variable
			 LEFT JOIN ae_game_variable ON ae_game_variable.id=ae_game_play_variable.variable_id 
			 LEFT JOIN ae_game_play ON ae_game_play.id= ae_game_play_variable.play_id 
			 LEFT JOIN ae_game_play_action ON ae_game_play_action.play_id=ae_game_play.id
             LEFT JOIN ae_game_branch_action ON ae_game_branch_action.id=ae_game_play_action.action_id
		     LEFT JOIN usergroups_user ON usergroups_user.id=ae_game_play.user_id
             WHERE ae_game_play_action.action_id=:actionId AND ae_game_variable.id=:varId
			 GROUP BY ae_game_play_variable.id
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rowsVars = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $ai,':varId'=>$variableId))
            ->queryAll();
		  }
      
	    return $rowsVars;
	   
	   
  }
  



}