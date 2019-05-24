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


class EssayExport 
{
 public $varCount=2;
 public $postsArr=array();
 public $VariableNameMap=array();


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
	  
	    $rows= $this->getUsersVars($ai); 
		
		  
        while($row = each($rows)){
            $r = $row[1];
			$this->postsArr[$r['userId']]['userId']=$r['userId'];
			$this->postsArr[$r['userId']]['userEmail']=$r['userEmail'];
			$this->postsArr[$r['userId']]['userFirstName']=$r['userFirstName'];
			$this->postsArr[$r['userId']]['userLastName']=$r['userLastName'];
			$this->postsArr[$r['userId']]['Variables'][$r['variableId']]= array($r['variableName'] => strip_tags($r['variableValue']) );
			
		}
		
		
			
	         foreach ($this->postsArr as $k=>$v){
			     if (isset($v['Variables'])) {
				  array_push($this->postsArr[$k],$this->flatten($v['Variables']));
			      unset($this->postsArr[$k]['Variables']);
				}
		     }	
		
		     foreach ($this->postsArr as $k=>$v){
			    $this->postsArr[$k]=$this->flatten($v);
		     }
		   
		   
		    $this->VariableNameMap=array_unique($this->VariableNameMap);
			
		 
		    foreach ($this->postsArr as $k=>$v){
			    
				$this->postsArr[$k]['userId']=$v['userId'];
				$this->postsArr[$k]['userEmail']=$v['userEmail'];
			    $this->postsArr[$k]['userFirstName']=$v['userFirstName'];
			    $this->postsArr[$k]['userLastName']=$v['userLastName'];
			 
			  foreach ($this->VariableNameMap as $kk=>$vv){
			    if (!isset($this->postsArr[$k][$vv]))  {
				       	$this->postsArr[$k][$vv]='';
				 } else {
					   $this->postsArr[$k][$vv]=$v[$vv];
				}
		      }
			
		  }
		  
		  $i=0;
		  foreach ($this->postsArr as $k=>$v){
			  if ($i==0)  $sorted=$v;
			  $i++;
			  foreach($sorted as $kk=>$vv){ 
			       $value=$this->postsArr[$k][$kk];
				   unset($this->postsArr[$k][$kk]);
                   $this->postsArr[$k][$kk]=$value;
              }
			 
		  }
		 
		 
			   	
	   return $this->postsArr;
   }
   
   
   
   
     
	 public function flatten($array, $prefix = '') {
    
	  $result = array();
        foreach($array as $key=>$value) {
           if(is_array($value)) {
             $result = $result + $this->flatten($value, $key );
          
		   } else {
            $result[$key] = $value;
			$this->VariableNameMap[]=$key;
          }
        }
		
     return $result;
    }
   
	 
  
  
  
  
   public function getUsersVars($ai) {
	
        $rowsVars=array();
	  
	  
		$sql="SELECT config 
              FROM ae_game_branch_action
			  WHERE ae_game_branch_action.id=:actionId";
			  
		$rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $ai))
            ->queryAll();
		
			
		  $variableId='';
		  $varArr=array();
		 
		  if (isset($rows[0]['config'])) {
			$config=json_decode($rows[0]['config']);
			    
				 for ($i=1; $i<=$this->varCount; $i++) {
				     $v='variable'.$i;
				     if (isset($config->$v) && ($config->$v!=0)) 
			                    $varArr[]=$config->$v;
				  }
				 
				
			
			 if (count($varArr)>0)
				   $variableId='AND ae_game_variable.id IN ('.implode(',',$varArr).')';
		  }
		  
		  
		
		  if ($variableId!='') {
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
             WHERE ae_game_play_action.action_id=:actionId '.$variableId.'
			 GROUP BY ae_game_play_variable.id
                    ';


        $rowsVars = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $ai))
            ->queryAll();
		  }
      
	    return $rowsVars;
	   
  }
  



}