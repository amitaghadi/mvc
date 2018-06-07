<?php
  error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."classes/DB_library.php");
class Messages{
	private $db_obj;
	private $stmt;
	public function __construct(){
			// default constructor
	}
	function insert_msg_detail($input){
		if($input['f_thread_id'] == ""){
			$login = $input['f_username'];
			if(!$login) return false;
			$f_topicid = $input['f_topicid'];
			$f_topicname = $input['f_topicname'];
			$m_type = $input['m_type'];
			$new_heading = $input['new_heading'];
			$m_price = $input['m_price'];
			$m_rply_id = $input['m_rply_id'];
			$m_replyto_user = $input['m_replyto_user'];
			$f_categoryid = $input['f_categoryid'];
			$msg_wordcount = $input['msg_wordcount'];
			$ip_id = $input['ip_id'];
			$user_points = $input['user_points'];
			$user_level = $input['user_level'];
			$m_flag = "APR";
			$user_points = 0;
			$user_level = '';
			$msg_count = 0;
			if($m_type=='')
				$m_type = "Message";
			if($login <> 'guest'){
				$sql = "select user_level, nick_name,user_points,msg_count from user_master where user_id = ':login'";	
				$this->db_obj_read=new DB_library("mmbRD");
				$this->stmt=DB_library::$conn->prepare($msg_count);
				$this->stmt->bindParam(":login",$login,PDO::PARAM_STR);
				$res=$this->db_obj_read->get_data($this->stmt);
				$this->db_obj_read->disconnect();
				if($res['count']>0){
					$row=$res['data'][0];
					if($row["nick_name"] == ""  || $nickname == "null" || $nickname == "NULL")	{
						$login = 'guest';
					}
					$user_points = (int)$row["user_points"];
					$user_level = (int)$row["user_level"];
					$msg_count = (int)$row["msg_count"];
				}
			}
			$moderatorOnline = false;
			if($msg_count < 20) {
				$m_flag = "NA";
			}
			else {
				if($login=='guest' || $user_points<50){
					$fname_line = "http://origin.moneycontrol.com/india/messageboard/common/mod_online.txt";
					$moderator_flag = file_get_contents($fname_line);
					if(trim($moderator_flag)=='1'){
						$m_flag = "APR";
						$moderatorOnline = true;
					}
					else{
						$m_flag = "NA";
					}
				}
			}
			if($login == 'guest') {	$m_flag = "NA";}
			if($user_level <= -1){	$m_flag = "NA";}
			if(strtolower($login) == 'guest'){
				header("Location:login.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
				exit;
			}
			$sql = "insert into msg_detail (msg_id, user_id, topicid, topic, flag, `type`, checked, average, rating_no, heading, subcategory1, subcategory2, price, rply_id, ent_date, update_date, rply_count, rank, path, backend_flag, replyto_user, read_flag, read_count, moderator, moderation_date, category_id, thread_id,word_count,user_ip) values ('','".$login."','".$f_topicid."','".$f_topicname."','".$m_flag."','".$m_type."','2',0,0,'".$f_heading."','','','".$m_price."','".$m_rply_id."',now(),null,0,0,'',0,'".$m_replyto_user."',0,0,'', null,'".$f_categoryid."', '".$input['f_thread_id']."', '".$msg_wordcount."','".$ip_id."')"; 
			$this->db_obj_write=new DB_library("mmbWR");
			$this->stmt=DB_library::$conn->prepare($sql);
			$res=$this->db_obj_write->execute_query($this->stmt);
			$this->db_obj_write->disconnect();
			$m_affected =$res['row_count'];
			if($m_affected == 1 ){
				$new_msg_id = $res['last_insert_id'];
				 $sql = "insert into msg_data (msg_id, message) values (" . $new_msg_id . ", '" . $input['f_message'] . "')";
				 $this->db_obj_write=new DB_library("mmbWR");
				 $this->stmt=DB_library::$conn->prepare($sql);
				 $res=$this->db_obj_write->execute_query($this->stmt);
				 $this->update_thread_id($new_msg_id);
				$message = "Thank you for your views.";
				}
		}
		return $message;
	}
	public function update_thread_id($new_msg_id){
		if($new_msg_id > 1800000){
            $range = 1800000;
            $thread_new = $new_msg_id - $range;
			$this->db_obj_write=new DB_library("mmbWR");
			$sql_ud = "update msg_detail set thread_id='$thread_new' where msg_id = " . $new_msg_id;
			//echo $sql_ud."<br>";
			$this->stmt=DB_library::$conn->prepare($sql_ud);
			$res=$this->db_obj_write->execute_query($this->stmt);
			$this->db_obj_write->disconnect();
            
			// $rec_ud = mysql_query($sql_ud, $conn_write);
        }
	}
	
	public function get_topic_messages($topic_id,$nocache=false){
		$data = getMemcache($topic_id.'_latestOP');
		if(strtotime(date('Y-m-d H:i:s')) - strtotime($data['cache_date']) > 180) // 3 mins
            {
               $data= $this->set_topic_messages($topic_id);
            }
			if($nocache)
			$data=$this->set_topic_messages($topic_id);
		return $data;
	}
	public function set_topic_messages($topic_id){
		// error_reporting(E_ALL);
		$limit = 3;
		$this->db_obj=new DB_library("mmb_read");
		$sql = " select repost_date,repost_flag,user_id, heading,thread_id, category_id, topic, topicid, ent_date, msg_id,checked,subcategory1 from msg_detail where topicid = :topic_id and flag='APR'  order by repost_date desc limit :limit " ;
		$this->stmt=DB_library::$conn->prepare($sql);
		$this->stmt->bindParam(":topic_id",$topic_id,PDO::PARAM_INT);
		$this->stmt->bindParam(":limit",$limit,PDO::PARAM_INT);
		$result=$this->db_obj->get_data($this->stmt);
		
		
		if($result['count']>0){
			$cntr=0;
			foreach($result['data'] as $key=>$row){
				$user_idT = $row['user_id'];
				$msg_idT = $row['msg_id'];
				$headingT = $row['heading'];
				$user_idT = $row['user_id'];
				$msg_idT = $row['msg_id'];
				$thread_idT = $row['thread_id'];
				$topicT = urldecode($row['topic']);
				$topic_idT = $row['topicid'];	
				$topic_url=$this->get_topic_url($topic_idT,$topicT);
				$topic_name = $row['topic'];
				$ent_dateT = $row['ent_date'];
				$time1 = explode(" ",$ent_dateT);
				$time_posted=$this->get_time_posted($ent_dateT);
				$msgchecked = $row['checked'];
				$reps_flag = $row["repost_flag"];
				$category_id = $row["category_id"];
				$reco=$row['subcategory1'];
				$reco1="";
				$recocls="";
				if($reco=="tips-B"){ $reco1="BUY"; $recocls="grncol";}
				if($reco=="tips-S"){ $reco1="SELL"; $recocls="redcol";}
				if($reco=="tips-H"){ $reco1="HOLD"; $recocls="";}
				$meassageT=$this->get_message_data($msg_idT);
				$nick_NT=$user_idT;
				$user_points_NT =0;
				$boarder_url="javascript:void(0)";
				if(strtolower($user_idT)!='guest' ){
					$user_details=$this->get_user_details($user_idT);
					$nick_NT=$user_details['nick_name'];
					$user_points_NT=$user_details['user_points'];
					$user_img=$user_details['img'];
					$user_class=$user_details['user_class'];
					$boarder_url=$this->get_boarder_url($user_idT);
				}
				$thread_url=$this->get_thread_url($thread_idT,$msg_idT);

				
				$Reply_CountT=$this->get_reply_count($thread_idT);
				
				$retdata ['data'][$cntr] = array(
									'user_id'=>$user_idT,
									'msg_id'=>$msg_idT,
									'heading'=>$headingT,
									'thread_id'=>$thread_idT,
									'topic'=>$topicT,
									'ent_date'=>$ent_dateT,
									'checked'=>$msgchecked,
									'repost_flag'=>$reps_flag,
									'cat_id'=>$category_id,
									'rcnt'=>$Reply_CountT,
									'nick'=>$nick_NT,
									'msg'=>$meassageT,
									'pts'=>$user_points_NT ,
									'user_img'=>$user_img ,
									'user_class'=>$user_class ,
									'time_posted'=>$time_posted ,
									'boarder_url'=>$boarder_url ,
									'topic_url'=>$topic_url ,
									'thread_url'=>$thread_url ,
									'reco'=>$reco1 ,
									'recocls'=>$recocls ,
									);	 $cntr++;
			}
			$retdata ['cache_date'] = date('Y-m-d H:i:s');
			
			setMemcache($topic_id.'_latestOP',$retdata);
		} 
		return $retdata; 
	}
	public function get_message_data($msg_id){
		$sq_msg="select message from msg_data where msg_id=:msg_id";
		$this->db_obj= new DB_library("mmb_read");
		$this->stmt=DB_library::$conn->prepare($sq_msg);
		$this->stmt->bindParam(":msg_id",$msg_id,PDO::PARAM_INT);
		$res=$this->db_obj->get_data($this->stmt);
		if($res['data'][0]['message']){
			$message=$res['data'][0]['message'];
			if(strlen($message)>150)
				$meassage = substr($message,0,150).'...';
			
		}
		$this->db_obj->disconnect();
		return $message;
	}
	public function get_user_details($user_id){
		$sql = "select * from user_master where user_id=:user_id";
		$this->db_obj=new DB_library("mmb_read");
		$this->stmt=DB_library::$conn->prepare($sql);
		$this->stmt->bindParam(":user_id",$user_id,PDO::PARAM_STR);
		$result=$this->db_obj->get_data($this->stmt);
		
		if($result['count']>0){
			$row=$result['data'][0];
			
			$user_points_NT = $row['user_points'];	 
			$file_n=$row['user_photo'];
			$photo_flag=$row['photo_flag'];
			$img=$this->get_user_picture($file_n,$photo_flag," class='FL MR10'");
			if(strlen($row['nick_name'])>0){
					$nick_NT = $row['nick_name'];
					}
					else{
					$nick_NT = $user_id;
					}
		}else{
			$nick_NT = $user_id;
		}
		if($user_points_NT>=500)$user_level="platinum";	
		else if ($user_points_NT>=200 && $user_points_NT<500)$user_level="gold";
		else if ($user_points_NT>=50 && $user_points_NT<200)$user_level="silver";
		
		$this->db_obj->disconnect();
		return array("nick_name"=>$nick_NT,"user_points"=>$user_points_NT,"img"=>$img,"user_class"=>$user_level);
	}
	public function get_reply_count($thread_id){
		$sql = "select count(1) as replycnt from msg_detail where thread_id=:thread_id and flag='APR'";
		$this->db_obj=new DB_library("mmb_read");
		$this->stmt=DB_library::$conn->prepare($sql);
		$this->stmt->bindParam(":thread_id",$thread_id,PDO::PARAM_INT);
		$result=$this->db_obj->get_data($this->stmt);
		return $result['data'][0]['replycnt'];
	}
	function get_user_picture($file_name,$photo_flag,$class="",$width=35,$height=35){
		//print $file_name."<br>".$photo_flag."<br>#$%^";
		$host = get_mcstatic_domain();
		
		if($height==0)
			$height='';
		else
			$height="height='".$height."px'";

		$width="width='".$width."px'";	

		if(!is_array($user_img))
			$user_img = array('home/bull','home/bear','home/bee','home/boy','home/man','home/women','home/msg@','home/msg_arrow','home/smiley','talk_bubble');

		if($photo_flag==1){	
			if($this->strpos_arr(' '.$file_name,$user_img)>0){
				$file_name = substr($file_name,5,strlen($file_name));
				$img="<img src='$host/images/messageboard/user/$file_name' ".$width." ".$height." style='border:1px #E8EBF0 solid' ".$class." >";
			}
			else if($file_name)
			{
				//$img1=join("",file("https://img-d01.moneycontrol.co.in/msgboard_image_files/$file_name"));

				if(strlen($file_name)<=0 || $file_name=='')
					$img="<img src='$host/images/messageboard/home/img.jpg' ".$width." ".$height." style='border:1px #E8EBF0 solid' ".$class." >";
				else
					$img="<img src='$host/msgboard_image_files/$file_name' ".$width." ".$height." style='border:1px #E8EBF0 solid' ".$class." >";
			}
		}
		else
			$img="<img src='$host/images/messageboard/home/img.jpg' ".$width." ".$height." style='border:1px #E8EBF0 solid' ".$class.">";


		
		return $img;
	}
	function strpos_arr($haystack, $needle) {
		if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) {		
			if(($pos = strpos($haystack, $what))!==false) return $pos;
		}
		return false;
	}
	function get_time_posted($ent_dateT){
		//$ent_dateT=$row_re["ent_date"];
		$msg_dat=substr($ent_dateT,0,10);
		$msg_yr=substr($ent_dateT,0,4);
		$curr_dat=date("Y-m-d");
		$curr_yr=date("Y");
		if($curr_dat!=$msg_dat){
		$date_all  = explode(" ",$ent_dateT);
		$time_xpld = explode(":",$date_all[1]);
		$date_xpld = explode("-",$date_all[0]);
		if($curr_yr==$msg_yr)
		$date_str  =  date("g.i A M jS", mktime($time_xpld[0], $time_xpld[1], 0, $date_xpld[1], $date_xpld[2], $date_xpld[0]));	
		else
		$date_str  =  date("g.i A M jS Y", mktime($time_xpld[0], $time_xpld[1], 0, $date_xpld[1], $date_xpld[2], $date_xpld[0]));	
		}
		else               
		$date_str ="". $this->dateTimeDiff($ent_dateT)." ago";
		return $date_str;
	}
	function dateTimeDiff($dateTimeBegin) {
	  $dateTimeEnd = date('Y-m-d H:i:s');
	  $dateTimeBegin =strtotime($dateTimeBegin);
	  $dateTimeEnd  =strtotime($dateTimeEnd);
	  if($dateTimeEnd === -1 || $dateTimeBegin === -1) {
	   return false;
	  }
	  $diff = $dateTimeEnd - $dateTimeBegin;
	  if ($diff < 0) {
	   return false;
	  }
	  $weeks = $days = $hours = $minutes = $seconds = 0; # initialize vars
	  if($diff % 604800 > 0) {
	   $rest1  = $diff % 604800;
	   $weeks  = ($diff - $rest1) / 604800; # seconds a week
	   if($rest1 % 86400 > 0) {
		 $rest2 = ($rest1 % 86400);
		 $days  = ($rest1 - $rest2) / 86400; # seconds a day
		 if( $rest2 % 3600 > 0 ) {
		   $rest3 = ($rest2 % 3600);
		   $hours = ($rest2 - $rest3) / 3600; # seconds an hour
		   if( $rest3 % 60 > 0 ) {
			 $seconds = ($rest3 % 60);
			 $minutes = ($rest3 - $seconds) / 60;  # seconds a minute
		   } else {
			 $minutes = $rest3 / 60;
		   }
		 } else {
		   $hours = $rest2 / 3600;
		 }
	   } else {
		 $days = $rest1/ 86400;
	   }
	  }else {
	   $weeks = $diff / 604800;
	  }
	  $string = array();
	  if($weeks > 1) {
		$weeks1 = $weeks*7;		
	  } elseif ($weeks == 1) {
		$weeks1 = 7;
	  }else{
		  $weeks1 ='0';
	  }
	  if($days > 1) {
		  if($weeks1!=0){
			  $days = $days + $weeks1;
			 $string[] = "$days days";
		  }else{
			$string[] = "$days days";
		  }
	  } elseif($days == 1) {
		  if($weeks1>=7){
			  $days = $days + $weeks1;
			 $string[] = "$days days";
		  }else{
		   $string[] = "1 day";
		  }   
	  }else{
		 if($weeks1!=0){
			$string[] = "$weeks1 days";
		 }else{
			//$string[] = "$weeks1 days";
		 }
	  }
	  if($hours > 1) {
	   $string[] = "$hours hrs";
	  } elseif ($hours == 1) {
	   $string[] = "1 hr";
	  }
	  if($minutes > 1) {
	   $string[] = "$minutes min";
	  } elseif ($minutes == 1) {
	   $string[] = "1 min";
	  }
	  if($seconds > 1) {
	   $string[] = "$seconds sec";
	  } elseif($seconds == 1) {
	   $string[] = "1 sec";
	  }
	  $text  = join(' ', array_slice($string,0,sizeof($string)-1)) . " ";
	  $text .= array_pop($string);  
	  return $text;
	}
	function get_boarder_url($user_id){
		if(!$user_id) return false;
		
			return WAP_SITE_LINK."mmb/boarder_detail.php?b_id=".strhex($user_id);
	}
	function strhex($string){
		$hexstr = unpack('H*', $string);
		return array_shift($hexstr);
	}
	function hexstr($hexstr){
		$hexstr = str_replace(' ', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}
	function get_topic_url($topic_id,$topic_name){
		return WAP_SITE_LINK.'stock-message-forum/'.cleanNewsCategoryName($topic_name).'/comments/'.$topic_id;
	}
	function get_thread_url($thread_id,$msg_id){
		return WAP_SITE_LINK."india/messageboardblog/message_thread/".$thread_id."/".$msg_id."#m".$msg_id;
	}
}