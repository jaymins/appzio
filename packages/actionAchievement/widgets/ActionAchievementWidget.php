<?php

/* renders individual task

    $this->task->icon
    $this->task->aetask->config
    $this->task->id
    $this->token
    $this->task->aetask->negative_points
    $this->task->aetask->type_id
    $this->taskdata->aetask->taskid
    $this->taskdata->id
    $this->taskdata->added
    $this->taskdata->aetask->timelimit
    $this->taskdata->play_id

*/

class ActionAchievementWidget extends CWidget
{

    
	//public $task;
	public $tasks;
    public $playid;
	public $skin;
	public $reached;
	public $currentTaskId;
    
    public function run(){

        $html = '';
		$lastReachedid=0;
		
		 if(count($this->tasks)>0){
           
		   
		      while($g = each($this->tasks)){
			   
			  $config= json_decode($g[1]['config']);
			 
			 
			  if ( isset($config->show_greyout) && ($config->show_greyout==1))  {
				 ///
				 
				 //////////////////
				 $completedId=0;
				// print_r($this->reached); die;
				 foreach ($this->reached  as $k=>$v){
					   
					   if ($g[1]['id'] == $v['action_id']) {
						   $completedId=$v['id']; $shorturl=$v['shorturl'];
					   if ($v['time_showed_as_reached']==0) 
						         $lastReachedid=$g[1]['id'];
					   }
					   // update time  
					   $badge=AeplayAction::model()->FindByPK($v['id']);
					   $badge->time_showed_as_reached = time();
					   $badge->update();
				   }
				 //////////////////
				 $url='';
				 $classGreyOut="greyout";
				 if ($completedId!=0) {
					  $classGreyOut="";
					  $url=Yii::app()->Controller->getDomain().Yii::app()->i18n->getCurrentLang().'/aeplay/home/showtask?ptid='.$completedId.'&token='.$shorturl;
				  }
				  $html.= '<div class="diploms '.$classGreyOut.'">';
				  $color=$config->badgecolor;
				  
				   $clasOpacity='';
				   if ($g[1]['id']==$lastReachedid) {
					   $clasOpacity='opacityNull';
				   }
				  
				  $icon=$config->badge;
				  $html.= '<a id="badge'.$g[1]['id'].'" href="#" title="'.$config->title.": ".strip_tags($config->text).'"  class="actionbadge '.$clasOpacity.'"><img src="'.Yii::app()->Controller->getDomain() .'/images/badges/'.$color .'/'.$icon.'" width="80" id="icon_' .$g[1]['id'] .' style="margin:5px; "></a>';
			      $html.= '';
				 // $html.='<div class="diplomtitle">'.substr($config->title,0,20).'</div>';
				 if ($g[1]['id']==$lastReachedid) {
				  $html.="<script type=\"text/javascript\">
                  $(document).ready(function(){
                        $('#badge".$g[1]['id']."').fadeTo( 'slow', 0);
		                $('#badge".$g[1]['id']."').fadeTo( 3000, 1, function() {
			            $('#badge".$g[1]['id']."').addClass('opacityOpague');
			             });

   

                 });
                </script>";
				 }
				  
				  ///url should be public
				 /* if ($url!='') {
				  $html.="<div class='diplomsocial'><a href=\"#\" onclick=\"
    window.open(
      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('".$url."'),'facebook-share-dialog','width=626,height=436'); return false;\" class=\"fb\"><img src=\"/images/fb-modern.png\" /></a>
		  <a href=\"#\" onclick=\"window.open( 'https://twitter.com/intent/tweet?url='+encodeURIComponent('".$url."'), 'myWindow', 
              'status = 1, height = 300, width = 600, resizable = 0' )\" class=\"tw\"><img src=\"/images/tw-modern.png\" /></a>
		  <a href=\"#\" onclick=\"window.open( 'https://plus.google.com/share?url='+encodeURIComponent('".$url."'), 'myWindow', 
              'status = 1, height = 300, width = 600, resizable = 0' )\" class=\"goo\"><img src=\"/images/g-modern.png\" /></a></div>";
			         }    */
			     $html.= '';
				 $html.= '</div>';
			   } 
            
			
			}
			
			
			
		 }
		
        
         
        echo($html);

        return true;
    }


   


}