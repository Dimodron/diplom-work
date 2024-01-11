<?php
class func{
		function validateDate($date, $format = 'Y-m-d H:i:s'){
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}
		function visitor_insert(){
			global $DB;
			$error='';	
			if(intval($_POST['event_time_id'])!=0){
				$DB->query("SELECT * FROM `event_time` WHERE `id` = {$_POST['event_time_id']} AND activity = 1");
				if($DB->get_num_rows()==0){
					$this->visitor('1');
				}else{
					if(!preg_match("/^\+7\s\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/", $_POST['telephone_number'])){
						$error.='Телефонный номер</br>';
					}

					if(isset($_POST['date_of_birth']) and $_POST['date_of_birth']!=''){
						if(!$this->validateDate($_POST['date_of_birth'], 'd.m.Y')){
							$error.='Дата рождения</br>';
						}
					}
			
					if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
						$error.='Адрес электронной почты</br>';
					}
			
					$_POST['full_name']=trim($_POST['full_name']);
					$_POST['full_name'] = str_replace("	", " ", $_POST['full_name']);
					while( strpos($_POST['full_name'],"  ")!==false){
						$_POST['full_name'] = str_replace("  ", " ", $_POST['full_name']);
					}
					$_POST['full_name']=htmlspecialchars ($_POST['full_name']);
					$_POST['full_name']=addslashes($_POST['full_name']);
			
					if($error!=''){
						$this->visitor_add($_POST,$error);
						exit;
					}
					$DB->do_insert('orders', array(		'event_time_id'			=> $_POST['event_time_id'],
														'full_name'				=> $_POST['full_name'],
														'date_of_birth'			=> $_POST['date_of_birth'],
														'telephone_number'		=> $_POST['telephone_number'],
														'mail'					=> $_POST['mail']));
					header("location:index.php?act=success");
					exit;
				}
			}
		}
		
		function visitor_add($post=array(),$error=''){
			global $templ,$DB;
				$id=intval($_GET['id']);
				if(isset($post['event_time_id'])){$id=$post['event_time_id'];}
				$unix_time='';
				$DB->query("SELECT event_time.*, event_themes.name FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id WHERE event_time.id = {$id}{$postid}");
				if($DB->get_num_rows()!=0){
					$data=$DB->fetch_row();
					$unix_time=$data['time'];
					$data['date']=date("d.m.Y",$data['time']);
					$data['time']=date("H:i",$data['time']); 
					$this->print_page($templ->visitor_add($data,$unix_time,$post,$error));
				}else{
					header('location:index.php?act=visitor');
					exit;
				}
								
		}
		function visitor($error=''){
			global $templ,$DB;
			$content = '';
			$show_time=time()+86400;
			$DB->query("SELECT event_time.*, event_themes.name,event_themes.picture FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id WHERE event_time.activity = 1 AND event_time.time > UNIX_TIMESTAMP() AND event_time.time < UNIX_TIMESTAMP()+86400*event_themes.days_to_show ORDER BY time DESC");
			while (($row=$DB->fetch_row())!=false) {
				$row['date']=date("d.m.Y",$row['time']);
				$row['time']=date("H:i",$row['time']); 
				$content.= $templ->visitor_table($row);
			}
			$this->print_page($templ->visitor_temp($content,$error));			
		}
		function success(){
		global $templ,$DB;
			$this->print_page($templ->success());
		}
		function print_page($content=""){
			global $templ;
			$body=$templ->head_temp();
			$body.= $content;
			$body.= $templ->footer_temp();
			print $body;
		}
	}	
?>				