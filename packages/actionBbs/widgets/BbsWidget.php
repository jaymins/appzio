<?php
Yii::import('aeapi.controllers.ImagesController');

/*

    this widget return instead of doing echo

*/

class BbsWidget extends CWidget
{

    public $task;
    public $token;
	public $variable;


    public $baseurl;
    public $actiondata;
    public $templatepath;
    public $configdata;

    public $post;

    public $comments;
    public $comments_byid;
    public $comments_table;
    public $uservariables;
    public $parentid;
    public $entryid;


    public $color_bckg = false;
    public $color_bg = false;
    public $color_text = false;
    public $color_btn = false;
    public $color_text_main = false;
    public $color_btn_text = false;
	public $color_btn_icon = false;


    public $simplified_feed = false;

    public $adminuserid = 0;

    public $admin = false;

    public function init(){
        echo('hello');
    }

    public function run(){

    }

    /* this will load all the data */
    public function loadData(){

    }


    public static function showEntry(){

    }

    public static function showComment(){


    }

    public function userinfo($cid = false){
        
		$show=1;
        if ($cid) {
			if ( isset($this->comments_byid[$cid])) {
            $userid = $this->comments_byid[$cid]['userid'];
            $date = $this->comments_byid[$cid]['date'];
			$gameauthor = $this->comments_byid[$cid]['gameauthor'];
			$username = $this->comments_byid[$cid]['username'];
			$email = $this->comments_byid[$cid]['email'];
			$parentid = $this->comments_byid[$cid]['commentparentid'];
			$bbsid = $this->comments_byid[$cid]['entryid'];
			} else {
			$show=0;	
			}
        } else {
            $userid = $this->post['userid'];
            $date = $this->post['date'];
			$gameauthor = $this->post['gameauthor'];
			$username = $this->post['username'];
			$email = $this->post['email'];
			$parentid = $this->post['commentparentid'];
			$bbsid = $this->post['entryid'];
        }

        $output = '';
		
		if ($show==1) {
		
        $output .= '<div class="bbs_profile">';

		
        if(isset($this->configdata->profile_picture) AND $this->configdata->profile_picture > 0 AND isset($this->uservariables[$userid][$this->configdata->profile_picture]['variablevalue'])){

            $pic = $this->uservariables[$userid][$this->configdata->profile_picture]['variablevalue'];

            if(stristr($pic,'.jpg') OR stristr($pic,'.png') OR stristr($pic,'.gif')){
                $output .= '<div class="bbs_pic"><img src="';
                $ic = new ImagesController('bbs');
                $picurl = $ic->getImageUrl('120','120',$pic,'90','round','user');
                $output .= $picurl;
			    //$output .= $pic;
                $output .= '" width="30"></div>';
            } else { 
                $output .= '<div class="bbs_pic"><img width="30" src="/images/milky/48/profile.png"></div>';
           }

        } else {
            $output .= '<div class="bbs_pic"><img width="30" src="/images/milky/48/profile.png"></div>';

       }
        
		
        if(isset($this->configdata->name) AND $this->configdata->name > 0 AND isset($this->uservariables[$userid][$this->configdata->name]['variablevalue'])){
            $output .= '<div class="date">' .$date .'</div><div class="bbs_name">';
            $output .= $this->uservariables[$userid][$this->configdata->name]['variablevalue'];
            $output .= '</div>';
        }
	  }
        $output .= '</div>';
		
        return $output;


    }

    public function buttons($entryid){
	
		$queryStr='';

		if (Yii::app()->user->id == $this->post['gameauthor']) {
			$queryStr='&ai='.$this->post['action_id'].'&ptid='.$this->post['playtask_id'];
		}
		
        $output = '<div class="diarydelete"><div class="buttons">';
        $output .= '<a href="' .$this->baseurl .'&a=comment&id=' .$entryid .$queryStr.'#commentbox">
                <img src="/images/milky/32/comment_add.png" width="25"></a>';


        /* determine whether delete button should be active or not (always on for admin view */
        $del = false;

        if(Yii::app()->user->id === $this->adminuserid){
            $del = true;
        } elseif( $this->configdata->allow_delete==1){
           if(isset($this->comments_byid[$entryid]['userid'])){
                if(Yii::app()->user->id == $this->comments_byid[$entryid]['userid']){
                    $del = true;
                }
           } else {
               $del = true;
           }
        }

        if($del === true){
            $output .= '<a href="' .$this->baseurl .'&a=delete&item=' .$entryid .$queryStr.'#commentbox"
            onclick="return confirm(' ."'Are you sure you want to delete this item?'" .')">
            <img src="/images/milky/32/report_delete.png" width="25"></a>';
        }

        $output .= '</div></div>';
        return $output;

    }

    public function msg(){

        if($this->post['admin_comment'] == 1){
            $output = '<div class="diaryentry_admin">';
        } else {
            $output = '<div class="diaryentry">';
        }

        $output .= $this->userinfo();
        $output .= $this->post['msg'];
        $cid = $this->post['entryid'];

        if(isset($_REQUEST['a']) AND $_REQUEST['a'] == 'comment' AND isset($_REQUEST['id']) AND $_REQUEST['id'] == $this->post['entryid']){
            $output .= $this->commentBox($this->post['action_id'], $this->post['entryid']);
            $output .= '<a href="' .$this->baseurl .'" class="cancel">{%cancel%}</a>';
            if($this->simplified_feed){
                $output .= '<div class="formsubmitter" style="background:#000000;color:#ffffff" onClick="formSubmit();">reply</div>';
                $output .= '    <script type="text/javascript">
                                    function formSubmit(){
                                        document.getElementById("verticalForm").submit();
                                    }
                                    </script>
                            ';
            }
        } else {
            $output .= $this->buttons($this->post['entryid']);
        }

       
	    // check if msg has a child
        $entryid = $this->post['entryid'];
       
        if(isset($this->comments[$entryid])){
			
            $output .= $this->reply($entryid);
        } 
		 

        $output .= '</div>';
        return $output;
    }




    public function reply($parentid){
      
	   $output = '';
	    
        if(isset($this->comments[$parentid])){
			
		 
		   foreach ($this->comments[$parentid] as $answers) {

               if($answers['admin_comment'] == 1){
                   $date = $answers['date'];

                   $output .= '<div class="admincomment">';
                   $output .= '<div class="bbs_profile">';
                   $output .= '<div class="date">' .$date .'</div><div class="bbs_name">';
                   $output .= '<strong>{%moderator%}</strong>';
                   $output .= '</div></div>';

               } else {
                   $output .= '<div class="comment">';
                   $output .= $this->userinfo($answers['entryid']);

               }

                  $output .= $answers['msg'];

                    if(isset($_REQUEST['a']) AND $_REQUEST['a'] == 'comment' AND isset($_REQUEST['id']) AND $_REQUEST['id'] == $answers['entryid']){
                          $output .= $this->commentBox($answers['action_id'], $answers['entryid']);
                          $output .= '<a href="' .$this->baseurl .'" class="cancel">{%cancel%}</a>';
                    } else {
                          $output .= $this->buttons($answers['entryid']);
                    }
			
			      
				   if(isset($this->comments[$answers['entryid']])){
                                 $output .= $this->reply($answers['entryid']);
                    } 
			       
				   
				    $output .= '</div>';
			 }
        }

        return $output;
    }




    /* because query returns comments first, we load them into a comment array */

    public function showList($posts){

        $output = '<div class="commentlist">';
        
		$i=0;
        while($post = each($posts)){
            $post = $post[1];
            $this->post = $post;
           
            // this saves comments to $this->comments,
            // where index key is the parent's key (because we look for it
            // during the parents own function

           if($post['commentparentid'] > 0){
               $this->parentid = $post['commentparentid'];
               $this->entryid = $post['entryid'];
               
			  // $this->comments[$this->parentid] = $post;
			  // added $i because many comments may have the same parentid
			   $this->comments[$this->parentid][$i] = $post;
               $this->comments_byid[$this->entryid] = $post;
			   $i++;
            } else {
               $output .= $this->msg();
            }
			
        }
        
        $output .= '</div>';
		
        return $output;

    }

    public function commentBox($actionid,$entryid){
        $uploadurl = $this->baseurl .'&a=upload&actionid=' .$actionid;
        $getimageurl = $this->baseurl .'&a=getimages&actionid=' .$actionid;

        $model = new BbsModel();

        $formfields['elements'] = array(
            'file' => array('type' => 'file'),

            'msg' => array(
                'type'=>'redactor',
                'class' => 'comment',
                'rows'=>6,
                'options' => array(
                    'buttons' => array('|', 'formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                        'image', 'video', 'file', 'table', 'link', '|',
                        'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule',
                        '|', 'underline', '|', 'alignleft', 'aligncenter', 'alignright', 'justify'),
                    'html' => false,
                    'formatting' => false,
                    'fileUpload' => $uploadurl,
                    'imageUpload' => $uploadurl,
                    'width'=>'100%',
                    'height'=>'250px',
                    'editorOptions' => array('buttons' => array('video'))
                )
            ),
            'a' => array('type' => 'hidden', 'value' => 'savecomment'),
            'parent_id' => array('type' => 'hidden', 'value' => $entryid),
			'a2' => array('type' => 'hidden', 'value' => 'savecomment')
        );

        if($this->admin === true){
            $formfields['elements']['admin_comment'] = array('type' => 'hidden', 'value' => '1');
        }

        if($this->simplified_feed != 1 AND !isset($_SESSION['mobile'])){
            $formfields['buttons'] = array(
                'save' => array('type' => 'htmlSubmit', 'label' => '{%send_reply%}', 'style' => 'color:#' .$this->color_btn .';'),
            );
        }

        $form = TbForm::createForm($formfields,$model,Controller::formParameters(false,$this->baseurl,'well',false));
        $form = $form->render();

        return '<div class="commentbox">' .$form .'</div>';
    }


    public function newpostForm(){
        $uploadurl = $this->baseurl .'&a=upload';
        $getimageurl = $this->baseurl .'&a=getimages';

        $model = new BbsModel();

        $formfields['elements'] = array(
            'file' => array('type' => 'file'),

            'msg' => array('type'=>'redactor',
                'class' => 'span2',
                'rows'=>6,
                'options' => array(
                    'buttons' => array('|', 'formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                        'image', 'video', 'file', 'table', 'link', '|',
                        'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule',
                        '|', 'underline', '|', 'alignleft', 'aligncenter', 'alignright', 'justify'),
                    'html' => false,
                    'formatting' => false,

                    'fileUpload' => $uploadurl,
                    'imageUpload' => $uploadurl,
                    'width'=>'100%',
                    'height'=>'250px',
                    'editorOptions' => array('buttons' => array('video'))
                )
            ),
            'a' => array('type' => 'hidden', 'value' => 'addentry')
			
        );

        if($this->admin === true){
            $formfields['elements']['admin_comment'] = array('type' => 'hidden', 'value' => '1');
        }

        // if button text is defined, we use that
        if(isset($this->actiondata->buttontxt) AND strlen($this->actiondata->buttontxt) > 0){
            $button = $this->actiondata->buttontxt;
        } else {
            $button = '{%add_post%}';
        }

        if(isset($_REQUEST['a']) AND $_REQUEST['a'] == 'comment'){
            $form = '<a href="' .$this->baseurl .'" class="btn">{%new_post%}</a><br><br>';
        } else {
            if($this->simplified_feed != 1 AND !isset($_SESSION['mobile'])){

                $formfields['buttons'] = array('save' => array('type' => 'htmlSubmit', 'label' => $button));
            }
            $form = TbForm::createForm($formfields,$model,Controller::formParameters(false,$this->baseurl,'well',false));
            $form = $form->render();
        }

        if(isset($this->configdata->brief)){ $brief = $this->configdata->brief; } else {$brief = '';}
        if(isset($this->configdata->commentboxtitle)){ $cmbox = $this->configdata->commentboxtitle; } else {$cmbox = '';}

        $output = Yii::app()->mustache->GetRender($this->templatepath .'bbs-main',array(
                'form'=>$form,
                'uploadurl' => $uploadurl,
                'getimageurl' => $getimageurl,
                'brief' => $brief,
                'commentboxtitle' => $cmbox,
                'color_bg' => $this->color_bg,
                'color_text' => $this->color_text,
                'color_btn' => $this->color_btn,
                'color_btn_text' => $this->color_btn_text

            ));

        return $output;
    }

   


}


?>