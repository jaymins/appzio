<?php 
/* array for excel sheet looks like this
 $postsArr = array ('0' => array(
        'postAuthor'=>'dora@east.fi',
	    'Msg' =>'test photostream post',
		'Date' => '014-04-01 09:25:06',
		'Reply 1' => 'date: 014-04-01 09:25:06  user@user reply to postAuthor: msg',
		'Reply 2' => 'date: 014-04-01 09:25:06  user@user reply to user@user: msg',
		'Reply 3' => 'date: 014-04-01 09:25:06  user2@user reply to user@user: msg',
		'Reply 3' => 'date: 014-04-01 09:25:06  user3@user reply to user2@user: msg'
	    )
   );
 */

/* export class must contain at least ExportGame method */


class PhotostreamExport extends PhotostreamModel
{
   public $replies=array();
   public $postsArr=array();
   public $repliesId=array();
   public $fixedColumnCount=3;


   public static function ExportGame($gid){
        return PhotostreamModel::getGamePhotostream($gid);
		
		   
   }

	  
	 public function exportAction ($ai) {
		 
		
		 
		$photostream= new PhotostreamModel;
		$rows= $photostream->getAllPosts($ai); 
		  
		  
		$i=0;
        while($post = each($rows)){
            $post = $post[1];
           
           
            // this saves comments to $this->comments,
            // where index key is the parent's key (because we look for it
            // during the parents own function

           if($post['commentparentid'] > 0){
            
			   $this->replies[$post['commentparentid']][$i] = $post;
               $this->repliesId[$post['entryid']] = $post;
			   $i++;
            } else {
			   $entryId=$post['entryid'];
			   $this->postsArr[$entryId]['postAuthorId']=$post['usereid'];
               $this->postsArr[$entryId]['postAuthor']=$post['useremail'];
			   $this->postsArr[$entryId]['Msg']= html_entity_decode(strip_tags($post['msg'],'<img>'));
			   $this->postsArr[$entryId]['Date']=$post['maindate'];
			      
				       //check about replies /child
			           if(isset($this->replies[$entryId])){
			
                             $this->postsArr[$entryId]['Replies'] = $this->reply($entryId);
                         } 
			   
			   
            }
			
        }
	    
		// define maxCountOfReplies, so that set for each row equal column
		// flat Replies element
		// set Reply $j elements
        // unset Replies element
		$maxCountOfReplies=0;
		
		foreach($this->postsArr as $k=>$v) {
			$j=0;
			if (isset($v['Replies'])) {
			    
				 $flatenReplies=$this->flattenReplies($v['Replies']);
				 for ($j=0; $j<count($flatenReplies); $j++) { 
			        $this->postsArr[$k]['Reply '.($j+1)] = $flatenReplies[$j];
				 }
				
				if ($j>$maxCountOfReplies) 
								$maxCountOfReplies=$j;
			}
			
			unset($this->postsArr[$k]['Replies']);
		 }
		
		
	
	    ///  add additional column when count of column is smaller than maxCountOfReplies
		foreach($this->postsArr as $k=>$v) {
			$j=1;
			
				for ($j=1; $j<=$maxCountOfReplies; $j++) { 
				   if (!isset($this->postsArr[$k]['Reply '.$j]))
					      $this->postsArr[$k]['Reply '.$j] = '';
				}
				
		 }
		
		//print_r($this->postsArr); die;
		return $this->postsArr;
		
	
	 }
	 
	 
	 
	 
	 
	 public function flattenReplies($array) {
             $return = array();
             array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
             return $return;
     }
	 
	 
	 
	 
	public function reply($parentid){
      
	   $replay = array();
	  
	  
        if(isset($this->replies[$parentid])){
			
		   foreach ($this->replies[$parentid] as $answers) {
			     
				 $parentEmail='anonymous';
		         if (isset($this->postsArr[$answers['commentparentid']]['postAuthor'])) 
				         $parentEmail= $this->postsArr[$answers['commentparentid']]['postAuthor'];
						 
						 
                 $replay [] = $answers['maindate'].", "
                              .$answers['useremail']." to "
							  .$parentEmail
							  .": "
							  .html_entity_decode(strip_tags($answers['msg'],'<img>'));
			    
				   if(isset($this->replies[$answers['entryid']])){
					  
                       $replay [] = $this->reply($answers['entryid']);
                    } 
			       
			 }
			
        } 
		
        return $replay;
    }
	 
  
	
    
     



}