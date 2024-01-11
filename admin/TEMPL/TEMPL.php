<?php
	namespace TEMPL;
	
	class TEMPL{
		
		static public function head(){
		return <<<EOF
    <html>
	<head>
        <title>
      
        </title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <meta content="no-cache" http-equiv="Pragma"/>
      <meta content="no-cache" http-equiv="no-cache"/>
      <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
      <link rel="stylesheet" href="style/style.css">
      </head>
        <body>
EOF;
		}	
		static public function footer(){
		return <<<EOF
	</div>
      <div style="clear:both"></div>
      <div class="site_basement"></div>
      <script src="script/script.js"></script>
    </body>
  </html>
EOF;
  }
  
  
  
  
  
  
	public static function menu($name_color=''){
	$color_themes='';$color_time='';$color_orders='';
		switch($name_color){
			case'event_themes':
				$color_themes="style='color:#ff8d00;'";
			break;
			case'event_time':
				$color_time="style='color:#ff8d00;'";
			break;
			case'orders':
				$color_orders="style='color:#ff8d00;'";
			break;
			case'settings':
				$color_settings="style='color:#ff8d00;'";
			break;
		}
			return <<<EOF
		<div class="menu">
			<ul>
				<li><a {$color_themes} href="index.php?act=event_themes">Мероприятия</a></li>
				<li><a {$color_time} href="index.php?act=event_time">Расписание</a></li>
				<li><a {$color_orders} href="index.php?act=orders">Заказы</a></li>
				<li><a {$color_settings} href="index.php?act=settings">Настройки</a></li>
			</ul>
		</div>
		<div class="content">
EOF;
	}
	function settings(){
	
		$settings['itemsperpage']=(isset($settings['itemsperpage']))?$settings['itemsperpage']:'50';
		return<<<EOF
		<ul>
			<li>
				<div>Колличество элементов в заказчиках: {$settings['orders_count']}</br>
				Колличество элементов в мероприятиях: {$settings['themes_count']}</br>
				Колличество элементов в расписании: {$settings['time_count']}</div>
				<form action="index.php?act=setting_recount"  method="post" enctype="multipart/form-data">
					<input type='submit' value='Пересчитать столбики'>
				</form>
			</li>
			<li>
				<div>Колличество данных на странице</div>
				<form action="index.php?act=setting_itemsperpage" name='itemsperpage' method="post" enctype="multipart/form-data">
					<select name='itemsperpage'><option value='5'>5</option><option value='10'>10</option><option value='50'>50</option><select>
					<input type='submit' value='сохранить' >
				</form>
			</li>
		</ul>
EOF;
	}
		public static function option ($text='',$val='',$sel_val=''){
		return<<<EOF
		<option {$sel_val} value="{$val}">{$text}</option>
EOF;
		}
		public static function event_themes_row($row=array()){
			$row['activity'] = ($row['activity']=='1')? "checked":"";
			 return <<<EOF
			 <tr><td>{$row['name']}</td><td>{$row['type']}</td><td>{$row['age']}</td><td>{$row['duration']} мин</td><td>{$row['number_of_seats']}</td><td><input class='activ' name = "event_themes" type="checkbox" value='{$row['id']}' {$row['activity']}/></td><td><a href="index.php?act=event_time&id={$row['id']}">Посмотреть в расписании</a></td></tr>
EOF;
}
		function event_themes_add($content='',$row=array(),$do='',$post=array(),$error=''){
		$row['activity'] = ($row['activity'])? "checked":"";
		$error=($error!='')?"<div class='error'>Проверьте правильность полей:</br>{$error}</div>":"";
		$row['days_to_show']=(isset($row['days_to_show']))?$row['days_to_show']:"7";
		return <<<EOF
			<form action="index.php?act={$do}"  method="post" enctype="multipart/form-data">
				<ul class='event_themes_add'>
					{$error}
					<input name="id" value="{$row['id']}" type="hidden"/>
					<li><img style='display:none;' src='{$row['picture']}' id='image'></li>
					<li><img style='display:block;' src='not_img.png' id='no_image'></li>
					<li>Название мероприятия </br><input required value="{$row['name']}" name="name"/></li>
					<li>Тип мероприятия </br><select name="type_id"><option value="0">Тип мероприятия</option>{$content}</select></li>
					<li>Минимальный возраст</br><input required type="number" value="{$row['age']}" name="age"/></li>
					<li>Продолжительность мероприятия</br><input required type="number" value="{$row['duration']}" name="duration"/> мин</li>
					<li>Колличество мест</br><input required value="{$row['number_of_seats']}" type="number" name="number_of_seats"/></li>
					<li>Ссылка на фотографию</br><input required id='picture' value="{$row['picture']}" type="url" name="picture"/></li>
					<li>Открывать запись за</br><input required value="{$row['days_to_show']}" type="number" name="days_to_show"/> дней</li>
					<li>Активность<input type="checkbox" value="1" name="activity" {$row['activity']}/></li>
					<li><textarea required placeholder='Описание' name="description">{$row['description']}</textarea></li>
					<input type="submit"/>
					
				
				</ul>
			</form>
EOF;
		}
		public static function event_themes($data =array(),$return_val=array(),$page=array(),$sort=array()){
			
		return <<<EOF
		<div class="opt_line">
			<form name="search" action="index.php?act=event_themes"  method="post" enctype="multipart/form-data">
				<input  autocomplete="off" name='search_name' value='{$return_val['search_name']}' class='search' type='text' placeholder='Введите название' />
				<select name='type_even_id' class='search'><option value='0'>Выберите тип</option>{$data['type']}</select>
				<input  type='hidden' name='orderby' value='{$sort['orderby']}'/>
				<input  type='hidden' name='page' value=''/>
				<input  type='hidden' name='ordertype' value='{$sort['ordertype']}'/>
				<input class='search' value='Поиск' type="submit"/>
				<a href='index.php?act=event_themes'>Сбросить<a/>
			</form>
			<a href="index.php?act=event_themes&do=add">Добавить мероприятие</a>
		</div>		
		 <div style="clear:both"></div>
		<table border='1' class="output_table">
		<thead>
		<tr>
		<th class='sort' name='{$sort['name']}' id='name'>Название мероприятия {$sort['name_arrow']}</th>
		<th class='sort' name='{$sort['type']}'id='type'> Тип мероприятия {$sort['type_arrow']}</th>
		<th class='sort' name='{$sort['age']}'id='age'> Минимальный возраст {$sort['age_arrow']}</th>
		<th class='sort' name='{$sort['duration']}'id='duration'> Продолжительность {$sort['duration_arrow']}</th>
		<th class='sort' name='{$sort['number_of_seats']}'id='number_of_seats' > Колличество мест {$sort['number_of_seats_arrow']}</th>
		<th>Активность</th>
		<th>Посмотреть в расписании</th>
		</tr>
		</thead>
		<tbody>
		{$data['content']}
		</tbody>
		</table>
		<div> <button class = "page {$page{'arrow_left'}}" value="{$page['previous_page']}">&#60</button> {$page['this_page']} из {$page['all_pages']} <button class = "page {$page{'arrow_right'}}"  value="{$page['next_page']}">&#62</button> </div>
EOF;
		}
		public static function event_time_table($row=array()){
			$row['activity'] = ($row['activity']=='1')? "checked":"";
			
			 return <<<EOF
			 <tr><td>{$row['name']}</td><td>{$row['date']}</td><td>{$row['time']}</td><td><input class="activ" name = "event_time" type="checkbox" value='{$row['id']}'{$row['activity']}/></td></tr>
EOF;
		}
		function event_time_add($content='',$post=array(),$error=''){
		$error=($error!='')?"<div class='error'>Проверьте правильность полей:</br>{$error}</div>":"";
		return <<<EOF
			<form action="index.php?act=event_time_insert"  method="post" enctype="multipart/form-data">
				<ul class='event_time_add'>
					{$error}
					<li>Тема мероприятия</br><select autocomplete="off" class='prov' name="event_themes_id"><option value='0'>Выберите тему</option>{$content}</select></li>
					<li>Дата</br><input required autocomplete="off" maxlength='10' value='{$post['date']}' type="date" name="date"/></li>
					<li>Начало мероприятия</br><input required autocomplete="off" maxlength='10' value='{$post['time']}' type="time" name="time"/></li>
					<li>Активность</br><input autocomplete="off" type="checkbox" value='1' name="activity"/></li>
					<li><input type="submit"/></li>
				</ul>
			</form>
EOF;
		}
		public static function event_time($data=array(),$return_val=array(),$page=array(),$sort=array()){
		
		return <<<EOF
			<div class="opt_line">
				<form action="index.php?act=event_time"  method="post" >
					<input  autocomplete="off" name='search_name' value='{$return_val['search_name']}' class='search' type='text' placeholder='Введите название' />
					<input  autocomplete="off" name='search_date' value='{$return_val['search_date']}' class='search'  type='date' placeholder='Введите дату' />
					<input  type='hidden' name='orderby' value='{$sort['orderby']}'/>
					<input  type='hidden' name='page' value=''/>
					<input  type='hidden' name='ordertype' value='{$sort['ordertype']}'/>
					<input class='search' value='Поиск' type="submit"/>
					<a href='index.php?act=event_time'>Сбросить<a/>
				</form>
				<a href="index.php?act=event_time_add">Добавить время мероприятия</a>
			</div>
		<table border='1' class='output_table'>
		<thead>
		<tr>
		<th class='sort' name='{$sort['name']}' id='name'>Название мероприятия {$sort['name_arrow']}</th>
		<th class='sort' name='{$sort['time']}' id='time'>Дата мероприятия {$sort['time_arrow']}</th>
		<th>Время мероприятия</th>
		<th>Активность</th>
		</tr>
		</thead>
		<tbody>
		{$data['content']}
		</tbody>
		</table>
		<div> <button class = "page {$page{'arrow_left'}}" value="{$page['previous_page']}">&#60</button> {$page['this_page']} из {$page['all_pages']} <button class = "page {$page{'arrow_right'}}"  value="{$page['next_page']}">&#62</button> </div>
EOF;
		}
		public static function orders_table($row=array()){
			$row['activity'] = ($row['activity']=='1')? "checked":"";
			$row['visit'] = ($row['visit']=='1')? "checked":"";
			 return <<<EOF
			 <tr><td>{$row['name']}</td><td>{$row['date']}</td><td>{$row['time']}</td><td>{$row['full_name']}</td><td>{$row['date_of_birth']}</td><td>{$row['telephone_number']}</td><td>{$row['mail']}</td><td><input class="activ" name = "orders" type="checkbox" value='{$row['id']}' {$row['activity']}/></td><td><input class="visit" name="orders" type="checkbox" value='{$row['id']}' {$row['visit']}/></td></tr>
EOF;
		}
		function orders_add($content='',$row=array(),$do=''){
		$row['activity'] = ($row['activity'])? "checked":"";
		return <<<EOF
			<form name="form_add" action="index.php?act={$do}"  method="post" enctype="multipart/form-data">
				<ul class='orders_add'>
					<li><input name="id" value="{$row['id']}" type="hidden"/></li>
					<li><select class='event'><option value='0'>Выберите тему</option>{$content}</select></li>
					 <div style="clear:both"></div>
					<input name='event_time_id' value='' type='hidden'>
					<li>Представьтесь</br><input required autocomplete="off" class='prov' name="full_name" value='{$row['full_name']}' placeholder='Фамилия Имя Отчество'/></li>
					<li>Дата рождения</br><input required maxlength='10' autocomplete="off" type="date" name="date_of_birth" value='{$row['date_of_birth']}' /></li>
					<li>Телефонный номер</br><input name='telephone_number' required autocomplete="off" type="text" class='tel' placeholder="+7(999)999-9999" value='{$row['telephone_number']}'/></li>
					<li>Mail<input required autocomplete="off" type="email" name="mail" value='{$row['mail']}'placeholder='mail@mail.ru'/></li>
					<li>Активность</br><input type="checkbox" value="1" name="activity" {$row['activity']}/></li>
					<input type="submit"/>
				</ul>
			</form>
EOF;
		}
		public static function orders($data=array(),$return_val=array(),$page=array(),$sort=array()){
			
			return <<<EOF
			<div class="opt_line">
				<form name="search" action="index.php?act=orders"  method="post" enctype="multipart/form-data">
					<th><input  autocomplete="off" name='search_name' value='{$return_val['search_name']}' class='search' type='text' placeholder='Введите название' /></th>
					<th><input  autocomplete="off" name='search_date'  value='{$return_val['search_date']}'  class='search' type='date' placeholder='Введите дату' /></th>
					<th><input  autocomplete="off" name='search_full_name' value='{$return_val['search_full_name']}' class='search' type='text' placeholder='ФИО' /></th>
					<input  type='hidden' name='orderby' value='{$sort['orderby']}'/>
					<input  type='hidden' name='page' value=''/>
					<input  type='hidden' name='ordertype' value='{$sort['ordertype']}'/>
					<th><input class='search' value='Поиск' type="submit"/></th>
					<th><a href='index.php?act=orders'>Сбросить<a/></th>
				</form>
				<a href="index.php?act=orders_add">Добавить заказ</a>
			</div>
			<table border='1' class='output_table'>
			<thead>
			<tr>
			<th class='sort' name='{$sort['name']}' id='name'>Название мероприятия{$sort['name_arrow']}</th>
			<th class='sort' name='{$sort['time']}' id='time'>Дата мероприятия{$sort['time_arrow']}</th>
			<th >Время мероприятия </th>
			<th class='sort' name='{$sort['full_name']}' id='full_name'>Фамилия Имя Отчество{$sort['full_name_arrow']}</th>
			<th class='sort' name='{$sort['date_of_birth']}' id='date_of_birth'>Дата рождения{$sort['date_of_birth_arrow']}</th>
			<th >Телефонный номер</th>
			<th >Mail</th>
			<th>Подтверждение зааказа</th>
			<th>Проход</th>
			</tr>
			</thead>
			<tbody>
			{$data['content']}
			</tbody>
			</table>
			<div> <button class = "page {$page{'arrow_left'}}" value="{$page['previous_page']}">&#60</button> {$page['this_page']} из {$page['all_pages']} <button class = "page {$page{'arrow_right'}}"  value="{$page['next_page']}">&#62</button> </div>
EOF;
		}
}

?>