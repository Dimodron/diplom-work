<?php
namespace FUNC;
use TEMPL\TEMPL;
use FUNC\DB;
use FUNC\VERIFICATION;

class FUNC{
		function __construct(){
			
		}
		private static  function print_page($content=""){		
			$body =TEMPL::head();
			$body.=self::menu();
			$body.= $content;
			$body.= TEMPL::footer();
			print $body;
		}
		private static  function menu(){
			$name_color=(isset($_GET['act']))?$_GET['act']:"";
			return TEMPL::menu($name_color);
		}
		
		private static function pages($sql=''){
			$page = array();
			$page['all_pages']=mysqli_fetch_row(DB::query($sql));
			$page['all_pages'] = ($page['all_pages'][0]%GENERAL::$settings['itemsperpage']>0)?intdiv($page['all_pages'][0],GENERAL::$settings['itemsperpage'])+1 : $page['all_pages'][0]/GENERAL::$settings['itemsperpage'];
			$page['this_page']=(GENERAL::$input['page']>0)?intval(GENERAL::$input['page']):"1";
			$page['start_limit']=($page['this_page']-1)*GENERAL::$settings['itemsperpage'];
			$page['limit']='LIMIT '.$page['start_limit'].','.GENERAL::$settings['itemsperpage'];
				
			if($page['this_page']>=$page['all_pages']){
				$page['arrow_right']='disable';
			}
			if($page['this_page']<=1){
				$page['arrow_left']='disable';
			}
			
			$page['previous_page'] =$page['this_page']-1;
			$page['next_page']=$page['this_page']+1;
		
			return $page;
		}
		
		private static function sorting ($names=array(),$def_column=''){
			
			$sort=array();
			
			if (in_array(GENERAL::$input['orderby'], $names)){
				$sort['orderby']=GENERAL::$input['orderby'];
			}else{
				$sort['orderby']=$def_column;
			}
			
			if(in_array(GENERAL::$input['ordertype'], array('ASC','DESC'))){
				$sort[$sort['orderby']]=GENERAL::$input['ordertype'];
				$sort['ordertype'] = GENERAL::$input['ordertype'];
			}else{$sort[$sort['orderby']]='DESC';$sort['ordertype'] = 'DESC';}
			
			$sort[$sort['orderby'].'_arrow']=($sort['ordertype']=='DESC')?"&#11015":"&#11014";
			
			return $sort;
		}
		
		public static  function event_themes(){
			switch(GENERAL::$input['do']){
				case'add':
				
				exit;
				break;
				case'':
				break;
				case'':
				break;
				
			}
			
			$select= array();
			$return_val=array();
			$data=array();
			$page = array();
			$sort=array();
			
			if(GENERAL::$input['search_name']>0){
				
			}
			
			if(GENERAL::$input['search_name']!=""){
				$return_val['search_name']=GENERAL::$input['search_name'];
				$select[]=" name LIKE '%".GENERAL::$input['search_name']."%'";
			}
			
			if(GENERAL::$input['type_even_id']>0){
				$return_val['type_even_id']=GENERAL::$input['type_even_id'];
				$select[]=" type_id = ".GENERAL::$input['type_even_id'];
			}
			if(count($select)>0){
				$data['search']="WHERE";
				$data['search'].=implode($select,' AND');
			}
			
			$page=self::pages("SELECT COUNT(id) FROM `event_themes` {$data['search']}");
			$sort=self::sorting(array('name','type','age','duration','number_of_seats'),'name');
				
				DB::query("SELECT event_themes.*, type_of_events.type FROM event_themes LEFT JOIN type_of_events ON type_of_events.id = event_themes.type_id {$data['search']} ORDER BY {$sort['orderby']} {$sort['ordertype']} {$page['limit']}");
				while (($row=DB::fetch_row())!=false) {
					$data['content'].= TEMPL::event_themes_row($row);
				}
				DB::query("SELECT * FROM type_of_events WHERE activity=1");
				while (($row=DB::fetch_row())!=false) {
					$sel_val=($row['id']==$return_val['type_even_id'])?"selected":"";
					$data['type'].=TEMPL::option($row['type'],$row['id'],$sel_val);
				}
			self::print_page(TEMPL::event_themes($data,$return_val,$page,$sort));
		}
		
		function event_themes_(){
		
			$content = '';
			$data=array();
			$sel_val='';
			$id=(isset($_GET['id']) and intval($_GET['id']))?$_GET['id']:"";
			$do='event_themes_update';
			$this->DB->query("SELECT event_themes.*, type_of_events.type FROM event_themes LEFT JOIN type_of_events ON type_of_events.id = event_themes.type_id WHERE event_themes.id ='{$id}'");
			$data=$this->DB->fetch_row();
			$this->DB->query("SELECT id,type FROM type_of_events");
			while (($row=$this->DB->fetch_row())!=false) {
				$sel_val=($data['type_id']==$row['id'])?"selected":"";
				$content.=$this->templ->option($row['type'],$row['id'],$sel_val);
			}
			$this->print_page($this->templ->event_themes_add($content,$data,$do));
		}
		
		function event_themes_add(){
		
			$data=array();
			$sel_val='';
			
			$this->DB->query("SELECT event_themes.*, type_of_events.type FROM event_themes LEFT JOIN type_of_events ON type_of_events.id = event_themes.type_id WHERE event_themes.id ='{$id}'");
			$data=$this->DB->fetch_row();
			
			$this->DB->query("SELECT id,type FROM type_of_events");
			
			while (($row=$this->DB->fetch_row())!=false) {
				$sel_val=($data['type_id']==$row['id'])?"selected":"";
				$content.=$this->templ->option($row['type'],$row['id'],$sel_val);
			}
			$this->print_page($this->templ->event_themes_add($content,$data,$do));
		}
		
		
		public static function event_time(){
			$select= array();
			$return_val=array();
			$data=array();
			$page = array();
			$sort=array();
			
			if(VERIFICATION::validateDate(GENERAL::$input['search_date'], 'Y-m-d')){
				$return_val['search_date']=GENERAL::$input['search_date'];
				$select[]=" time > ".strtotime(GENERAL::$input['search_date'])." AND time < ".(strtotime(GENERAL::$input['search_date'])+86400);
			}
			
			if(GENERAL::$input['search_name']!=""){
				$return_val['search_name']=GENERAL::$input['search_name'];
				$select[]=" name LIKE '%".GENERAL::$input['search_name']."%'";
			}
			
			if(count($select)>0){
				$data['search']="WHERE";
				$data['search'].=implode($select,' AND ');
			}
			
			$page=self::pages("SELECT COUNT(event_time.id)FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id  {$data['search']}");
			$sort=self::sorting(array('name','time'),'time');
			
			DB::query("SELECT event_time.*, event_themes.name FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id {$data['search']} ORDER BY {$sort['orderby']} {$sort['ordertype']} {$page['limit']}");
			while (($row=DB::fetch_row())!=false) {
				$row['date']=date("d.m.Y",$row['time']);
				$row['time']=date("H:i",$row['time']); 
				$data['content'].= TEMPL::event_time_table($row);
			}
			self::print_page(TEMPL::event_time($data,$return_val,$page,$sort));
		}
		
		public static function orders($data=""){
		
			$select= array();
			$return_val=array();
			$data=array();
			
			if(VERIFICATION::validateDate(GENERAL::$input['search_date'], 'Y-m-d')){
				$return_val['search_date']=GENERAL::$input['search_date'];
				$select[]=" time > ".strtotime(GENERAL::$input['search_date'])." AND time < ".(strtotime(GENERAL::$input['search_date'])+86400);
			}
			
			if(GENERAL::$input['search_name']!=""){
				$return_val['search_name']=GENERAL::$input['search_name'];
				$select[]=" name LIKE '%".GENERAL::$input['search_name']."%'";
			}
			
			if(GENERAL::$input['search_full_name']!=""){
				$return_val['search_full_name']=GENERAL::$input['search_full_name'];
				$select[]=" full_name LIKE '%".GENERAL::$input['search_full_name']."%'";
			}
			
			if(count($select)>0){
				$data['search']="WHERE";
				$data['search'].=implode($select,' AND ');
			}
			
			
			$page=self::pages("SELECT COUNT(orders.id) FROM orders LEFT JOIN event_time ON event_time.id = orders.event_time_id LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id {$data['search']}");
			$sort=self::sorting(array('name','time','full_name','date_of_birth'),'time');
			
			DB::query("SELECT orders.*, event_time.event_themes_id,event_time.time,event_themes.name FROM orders LEFT JOIN event_time ON event_time.id = orders.event_time_id LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id {$data['search']} ORDER BY {$sort['orderby']} {$sort['ordertype']} {$page['limit']}");
				
				while (($row=DB::fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$row['time']=date("H:i",$row['time']);
					$row['date_of_birth']=date("d.m.Y",$row['date_of_birth']);
					$data['content'].= TEMPL::orders_table($row);
				}
			self::print_page(TEMPL::orders($data,$return_val,$page,$sort));
		}
		/* function activ(){	
			$id=intval($_POST['id']);
			if($_POST['column']=='activ'){
				$active_status=array('activity'=>intval($_POST['activity']));
			}else{
				$active_status=array('visit'=>intval($_POST['activity']));
			}
			$table = $_POST['table_name'];
			$this->DB->do_update($table,$active_status,"id=$id");
			ob_clean();
		}
		function setting_itemsperpage(){
			$_POST['itemsperpage']=intval($_POST['itemsperpage']);
			$this->DB->do_update('settings',array('value'	=> $_POST['itemsperpage']),"name='itemsperpage'");
			header("location:index.php?act=settings");
			exit;
		}
		function setting_recount(){
				$count_themes=mysqli_fetch_row($this->DB->query("SELECT  COUNT(*) FROM event_themes"));
				$count_time=mysqli_fetch_row($this->DB->query("SELECT  COUNT(*) FROM event_time"));
				$count_orders=mysqli_fetch_row($this->DB->query("SELECT  COUNT(*) FROM orders"));
				$this->DB->query("UPDATE settings SET value ='{$count_themes[0]}' WHERE name ='themes_count'");
				$this->DB->query("UPDATE settings SET value ='{$count_time[0]}' WHERE name ='time_count'");
				$this->DB->query("UPDATE settings SET value ='{$count_orders[0]}' WHERE name ='orders_count'");
				header("location:index.php?act=settings");
				exit;
		}
		function settings(){
			
				$this->print_page($this->templ->settings());
		} 
		
		function event_time_insert(){
			
			$error='';	
			if($_POST['activity']!='1'){
				$_POST['activity']='0';
			}
			if(intval($_POST['event_themes_id'])!=0){
				$this->DB->query("SELECT * FROM `event_themes` WHERE `id` = {$_POST['event_themes_id']} AND activity = 1");
				if($this->DB->get_num_rows()==0){
					$error.='Тема мероприятия</br>';
				}			
			}
			if(isset($_POST['date']) and $_POST['date']!=''){
				if(!$this->validateDate($_POST['date'], 'Y-m-d')){
					$error.='Дата мероприятия</br>';
				}
			}
			if(isset($_POST['time']) and $_POST['time']!=''){
				if(!$this->validateDate($_POST['time'], 'H:i')){
					$error.='Время мероприятия</br>';
				}
			}
			if($error!=''){
				$this->event_time_add($_POST,$error);
				exit;
			}
			$_POST['time']=strtotime($_POST['time']." ".$_POST['date']);
			$this->DB->do_insert('event_time', array('time'				=> $_POST['time'],
												'event_themes_id'	=> $_POST['event_themes_id'],
												'activity'			=> $_POST['activity']));
			$this->count_update('time_count','+1');
			header("location:index.php?act=event_time");
			exit;
		}
		
		function event_time(){
			switch($this->verif->input['do']){
				case'insert':
				
				break;
				case'update':
				
				break;
				case'ChangeOrAddForm':
				
				break;
			}
	
		$ordertype = "";
		$select= array();
		$return_val=array();	
		$id=(intval($this->verif->input['id'])!=0)?"WHERE event_time.event_themes_id='{$this->verif->input['id']}'":"";
		
		
		if(isset($this->verif->input['date'])){
			if($this->verif->validateDate($this->verif->input['date'], 'Y-m-d')){
					$return_val['date']=$this->verif->input['date'];
					$next_day=strtotime($this->verif->input['date'])+86400;
					$select[]=" time > {$this->verif->input['date']} AND time < {$next_day}";
				}else{$return_val['date']='';}
			}
			
			if(isset($this->verif->input['name'])!=''){
				$return_val['name']=$this->verif->input['name'];
				$select[]=" name LIKE '%{$this->verif->input['name']}%'";
			}else{$return_val['name']='';}
			
			if(count($select)>0){
				$search="WHERE";
			}
			
			$search.=implode($select,' AND');
			$content = '';
			$arr=array();
			$ordertype = "";
			$orderby = "";
			
			$orderby =(in_array($this->verif->input['orderby'], array('name','time')))?$this->verif->input['orderby']:"time";
			$ordertype=(in_array($this->verif->input['ordertype'], array('ASC','DESC')))?$this->verif->input['ordertype']:"DESC";
			
			$this->DB->query("SELECT COUNT(*) as total_records FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id  {$search}");
			$count = $this->DB->fetch_row();
			$page['this_page']=(intval($this->verif->input['page'])>0)?$this->verif->input['page']:"0";
			$page['page_count'] = intdiv($count['total_records'],$this->settings['itemsperpage']);
			$page['this_page'] = ($page['this_page']<=$page['page_count'])?$page['this_page']:$page['page_count'];
			$limit='LIMIT '.($page['this_page'])*$this->settings['itemsperpage'].','.$this->settings['itemsperpage'];

			$this->DB->query("SELECT event_time.*, event_themes.name FROM event_time LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id {$id} {$search} ORDER BY {$orderby} {$ordertype} {$limit}
			");
			while (($row=$this->DB->fetch_row())!=false) {
				$row['date']=date("d.m.Y",$row['time']);
				$row['time']=date("H:i",$row['time']); 
				$table.= $this->templ->event_time_table($row);
			}
			
			$this->print_page($this->templ->event_time($table,$ordertype,$orderby,$return_val,$page));
		}
		
		function event_time_add($post=array(),$error=''){
		
			$content ="";
			$this->DB->query("SELECT id,name FROM event_themes WHERE activity='1'");
			while (($row=$this->DB->fetch_row())!=false) {
				$content.=$this->templ->option($row['name'],$row['id']);
			}
			$this->print_page($this->templ->event_time_add($content,$post,$error));
		}
		function event_themes_update(){
			
			$id=(isset($_POST['id']) and intval($_POST['id']))?$_POST['id']:"";
			$this->DB->do_update('event_themes', array('name'				=> $_POST['name'],
												'type_id'			=> $_POST['type_id'],
												'age'				=> $_POST['age'],
												'duration'			=> $_POST['duration'],
												'number_of_seats'	=> $_POST['number_of_seats'],
												'picture'			=> $_POST['picture'],
												'days_to_show'			=> $_POST['days_to_show'],
												'activity'			=> $_POST['activity']),"id={$id}");
			header("location:index.php?act=event_themes");
			exit;
		}
		function event_themes_insert(){
			
			$error='';
			
			if($_POST['activity']!='1'){
				$_POST['activity']='0';
			}
			if(intval($_POST['type_id'])!=0){
				$this->DB->query("SELECT * FROM `type_of_events` WHERE `id` = {$_POST['type_id']} AND activity = 1");
				if($this->DB->get_num_rows()==0){
					$error.='Тема мероприятия</br>';
				}			
			}
			if(intval($_POST['age'])>0 and intval($_POST['age'])<100  ){
				$error.='Возраст</br>';
			}
			if(intval($_POST['duration'])!=0){
				$error.='Продолжительность</br>';
			}
			if(intval($_POST['number_of_seats'])!=0){
				$error.='Колличество мест</br>';
			}
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$_POST['picture'])) {
					$error.= 'Ссылка на фото';
			}
			$_POST['name']=trim($_POST['name']);
					$_POST['name'] = str_replace("	", " ", $_POST['name']);
					while( strpos($_POST['name'],"  ")!==false){
						$_POST['name'] = str_replace("  ", " ", $_POST['name']);
					}
					$_POST['name']=htmlspecialchars ($_POST['name']);
					$_POST['name']=addslashes($_POST['name']);
			
			$_POST['description']=trim($_POST['description']);
					$_POST['description'] = str_replace("	", " ", $_POST['description']);
					while( strpos($_POST['description'],"  ")!==false){
						$_POST['description'] = str_replace("  ", " ", $_POST['description']);
					}
					$_POST['description']=htmlspecialchars ($_POST['description']);
					$_POST['description']=addslashes($_POST['description']);
			if($error!=''){
				$this->event_themes_add($_POST,$error);
				exit;
			}
			$this->DB->do_insert('event_themes', array(	'name'				=> $_POST['name'],
												'type_id'			=> $_POST['type_id'],
												'age'				=> $_POST['age'],
												'duration'			=> $_POST['duration'],
												'description'		=> $_POST['description'],
												'number_of_seats'	=> $_POST['number_of_seats'],
												'picture'			=> $_POST['picture'],
												'days_to_show'			=> $_POST['days_to_show'],
												'activity'			=> $_POST['activity']));
			header("location:index.php?act=event_themes");
			exit;
		}
		function event_themes_change(){
		
			$content = '';
			$data=array();
			$sel_val='';
			$id=(isset($_GET['id']) and intval($_GET['id']))?$_GET['id']:"";
			$do='event_themes_update';
			$this->DB->query("SELECT event_themes.*, type_of_events.type FROM event_themes LEFT JOIN type_of_events ON type_of_events.id = event_themes.type_id WHERE event_themes.id ='{$id}'");
			$data=$this->DB->fetch_row();
			$this->DB->query("SELECT id,type FROM type_of_events");
			while (($row=$this->DB->fetch_row())!=false) {
				$sel_val=($data['type_id']==$row['id'])?"selected":"";
				$content.=$this->templ->option($row['type'],$row['id'],$sel_val);
			}
			$this->print_page($this->templ->event_themes_add($content,$data,$do));
		}
		function event_themes(){
		
			$ev_id='';$select= array();$sel_val='';$date='';$return_val=array();$content = '';$orderby = "name";$type='';$ordertype = "DESC";
			if(isset($_POST['name']) and $_POST['name']!=""){
				$return_val['name']=$_POST['name'];
				$select[]=" name LIKE '%{$_POST['name']}%'";
			}
			if(isset($_POST['type_id']) and $_POST['type_id']!="" and $_POST['type_id']!=0){
				$ev_id=$_POST['type_id'];
				$select[]=" type_id = {$_POST['type_id']}";
			}
			if(count($select)>0){
				$search="WHERE";
			}
			$search.=implode($select,' AND');
			if (isset($_POST['orderby']) and in_array($_POST['orderby'], array('name','type','age','duration','number_of_seats'))) $orderby = $_POST['orderby'];
			if (isset($_POST['ordertype']) and $_POST['ordertype']!="DESC"){$ordertype = "DESC";}else{$ordertype = "ASC";}
			
			$page=(isset($_GET['page']))?intval($_GET['page']):"0";
			$start_limit=$page*$settings['itemsperpage'];
			$limit='LIMIT '.$start_limit.','.$settings['itemsperpage'];
			
				$this->DB->query("SELECT event_themes.*, type_of_events.type FROM event_themes LEFT JOIN type_of_events ON type_of_events.id = event_themes.type_id {$search} ORDER BY {$orderby} {$ordertype} {$limit}");
				while (($row=$this->DB->fetch_row())!=false) {
					$content.= $this->templ->event_themes_table($row);
				}
				$this->DB->query("SELECT * FROM type_of_events WHERE activity=1");
				while (($row=$this->DB->fetch_row())!=false) {
					$sel_val=($ev_id==$row['id'])?"selected":"";
					$type.=$this->templ->option($row['type'],$row['id'],$sel_val);
				}
			$this->print_page($this->templ->event_themes($content,$ordertype,$orderby,$return_val,$type,$page));
		}
		function event_themes_add($post=array(),$error=''){
			
			$content = '';
			$sel_val='';
			$data=array();
			$do='event_themes_insert';
				$this->DB->query("SELECT id,type FROM type_of_events WHERE activity=1");
				while (($row=$this->DB->fetch_row())!=false) {
					$content.=$this->templ->option($row['type'],$row['id'],$sel_val);
				}
				
			$this->print_page($this->templ->event_themes_add($content,$data,$do,$post,$error));
		}
		function orders_update(){
			
			$id=(isset($_POST['id']) and intval($_POST['id']))?$_POST['id']:"";
			$this->DB->do_update('orders', array('event_time_id'				=> $_POST['event_time_id'],
												'full_name'				=> $_POST['full_name'],
												'date_of_birth'			=> $_POST['date_of_birth'],
												'telephone_number'		=> $_POST['telephone_number'],
												'mail'					=> $_POST['mail'],
												'activity'				=> $_POST['activity']),"id={$id}");
			header("location:index.php?act=orders");
			exit;
		}
		function orders_insert(){
			
			$this->DB->do_insert('orders', array(	'event_time_id'				=> $_POST['event_time_id'],
												'full_name'				=> $_POST['full_name'],
												'date_of_birth'			=> $_POST['date_of_birth'],
												'telephone_number'		=> $_POST['telephone_number'],
												'mail'					=> $_POST['mail'],
												'activity'				=> $_POST['activity']));
			header("location:index.php?act=orders");
			exit;
		}
		function orders_select(){
		
			$content = "<select class='date' ><option value='0'>Выберите дату</option>";
			$time = "";
			$id=(isset($_POST['id']) and intval($_POST['id']))?$_POST['id']:"";
			$time_array = array();
			$date_array = array();
			$this->DB->query("SELECT * FROM event_time WHERE event_themes_id = '{$id}' AND activity='1' ORDER BY time DESC");
				while (($row=$this->DB->fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$time_array[$row['date']][]=$row;
					if (!in_array( $row['date'], $date_array) ) {
						$date_array[] = $row['date'];
						$content.="<option>{$row['date']}</option>";
					}
				}
				foreach($time_array as $key => $value) {
					$time.="<select class='time' id='{$key}'><option value='0'>Выберите время</option>";
					foreach($time_array[$key] as $timerow) {
						$time.="<option value='".$timerow['id']."'>".date("H:i",$timerow['time'])."</option>";
					}
					$time.="</select>";
				}
			$content.="</select>";
			ob_clean();
			die($content.$time);
		}
		function orders_change(){
			
			$content = '';
			$data=array();
			$sel_val='';
			$id=(isset($_GET['id']) and intval($_GET['id']))?$_GET['id']:"";
			$do='orders_update';
				$this->DB->query("SELECT orders.*, event_time.event_themes_id,event_time.time,event_themes.name FROM orders LEFT JOIN event_time ON event_time.id = orders.event_time_id LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id WHERE orders.id ='{$id}'");	
				$data=$this->DB->fetch_row();
				$data['date_of_birth']=date("Y-m-d",$data['date_of_birth']);
				$this->DB->query("SELECT event_themes.id,event_themes.name FROM event_themes WHERE event_themes.activity = '1'");
				while (($row=$this->DB->fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$row['time']=date("H:i",$row['time']);
					$content.=$this->templ->option($row['name'],$row['id'],$sel_val);
				}
			$this->print_page($this->templ->orders_add($content,$data,$do));
		}
		function orders_add($data=""){
		
			$content = '';
			$sel_val='';
			$data=array();
			$do='orders_insert';
				$this->DB->query("SELECT event_themes.id,event_themes.name FROM event_themes WHERE event_themes.activity = '1'");
				while (($row=$this->DB->fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$row['time']=date("H:i",$row['time']);
					$content.=$this->templ->option($row['name'],$row['id'],$sel_val);
				}
			$this->print_page($this->templ->orders_add($content,$data,$do));				
		}
		function orders($data=""){
		
		
			$select= array();
			$date='';
			$return_val=array();
			if(isset($_POST['date']) and $_POST['date']!=""){
				$return_val['date']=$_POST['date'];
				$_POST['date']=strtotime($_POST['date']);
				$date=$_POST['date']+86400;
				$select[]=" time > {$_POST['date']} AND time < {$date}";
			}
			if(isset($_POST['name']) and $_POST['name']!=""){
				$return_val['name']=$_POST['name'];
				$select[]=" name LIKE '%{$_POST['name']}%'";
			}
			
			if(isset($_POST['full_name']) and $_POST['full_name']!=""){
				$return_val['full_name']=$_POST['full_name'];
				$select[]=" full_name LIKE '%{$_POST['full_name']}%'";
			}
			if(count($select)>0){
				$search="WHERE";
			}
			
			$search.=implode($select,' AND');
			$orderby = "time";
			$content = '';
			$ordertype = "DESC";
			
			if (isset($_POST['orderby']) and in_array($_POST['orderby'], array('name','time','full_name','telephone_number','mail'))) $orderby = $_POST['orderby'];
			if (isset($_POST['ordertype']) and $_POST['ordertype']!="DESC"){$ordertype = "DESC";}else{$ordertype = "ASC";}
			
			$page=(isset($_GET['page']))?intval($_GET['page']):"0";
			$start_limit=$page*$settings['itemsperpage'];
			$limit='LIMIT '.$start_limit.','.$settings['itemsperpage'];
			
			$this->DB->query("SELECT orders.*, event_time.event_themes_id,event_time.time,event_themes.name FROM orders LEFT JOIN event_time ON event_time.id = orders.event_time_id LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id {$search} ORDER BY {$orderby} {$ordertype} {$limit}");
				
				while (($row=$this->DB->fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$row['time']=date("H:i",$row['time']);
					$row['date_of_birth']=date("d.m.Y",$row['date_of_birth']);
					$content.= $this->templ->orders_table($row);
				}
			$this->print_page($this->templ->orders($content,$ordertype,$orderby,$return_val,$page));
		}*/
	}	
?>				