<?php
class templ{
//*********************************************************************************************************//
//head*****************************************************************************************************//
//*********************************************************************************************************//
		function head_temp(){
		return <<<EOF
    <html>
      <head>
        <title>
      
        </title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <meta content="no-cache" http-equiv="Pragma"/>
      <meta content="no-cache" http-equiv="no-cache"/>
      <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
      <link rel="stylesheet" href="style.css">
      </head>
        <body>
EOF;
		}	
//*********************************************************************************************************//
//footer***************************************************************************************************//
//*********************************************************************************************************//
		function footer_temp(){
		return <<<EOF
	</div>
      <div style="clear:both"></div>
      <div class="site_basement"></div>
      <script src="script.js"></script>
    </body>
  </html>
EOF;
  }
//*********************************************************************************************************//
//visitor**************************************************************************************************//
//*********************************************************************************************************//
		function visitor_table($row=array()){
			$row['picture']=(isset($row['picture']) and $row['picture']!='')?$row['picture']:"not_img.png";
			 return <<<EOF
			 <tr><td><img src='{$row['picture']}'></td><td><a href='index.php?act=visitor_add&id={$row['id']}'>{$row['name']}</a></td><td>{$row['date']}</td><td>{$row['time']}</td></tr>
EOF;
		}
		function visitor_temp($content='',$error=''){
			$error=($error==1)?"<div class='error'>Была выбрана некорректная экскурсия</div>":"";
		return <<<EOF
		<ul class='menu'></ul>
		{$error}
		<table border='1' class='output_table'>
		<thead>
		<tr>
		<th></th>
		<th>Название мероприятия</th>
		<th>Дата мероприятия</th>
		<th>Время мероприятия</th>
		</tr>
		</thead>
		<tbody>
		{$content}
		</tbody>
		</table>
EOF;
		}
		function visitor_add($data=array(),$unix_time='',$post=array(),$error=''){
		$error=($error!='')?"<div class='error'>Проверьте правильность полей:</br>{$error}</div>":"";
		return <<<EOF
			<ul class='menu'><li><a href='index.php?act=visitor'>&larr;</a></li><li><span>{$data['name']}</span></li><li><span>Дата</span> {$data['date']}</li><li><span>Время</span> {$data['time']}</li></ul>
			{$error}
			<form name="form_visitor" action="index.php?act=visitor_insert"  method="post" enctype="multipart/form-data">
				<ul class='visitor_add'>
					<li><input name="time" value="{$unix_time}" type="hidden"/></li>
					<li><input name='event_time_id' value='{$data['id']}' type='hidden'></li>
					 <div style="clear:both"></div>
					<li>Представьтесь</br><input required autocomplete="off" class='prov' name="full_name" value='{$post['full_name']}' placeholder='Фамилия Имя Отчество'/></li>
					<li>Дата рождения</br><input required maxlength='10' autocomplete="off" id='mydata' value='{$post['date_of_birth']}' name="date_of_birth" placeholder="дд.мм.гг" value='{$row['date_of_birth']}' /></li>
					<li>Телефонный номер</br><input name='telephone_number' required autocomplete="off" type="text" value='{$post['telephone_number']}' class='tel' placeholder="+7(999)999-9999" /></li>
					<li>Адрес электронной почты</br><input required autocomplete="off" type="email" value='{$post['mail']}' name="mail" placeholder='mail@mail.ru'/></li>
					<li><input type="submit"/></li>
				</ul>
			</form>
EOF;
		}
		function success(){
			return <<<EOF
			<ul class='menu'></ul>
			<div class='success'> Вы успешно записались на мероприятие </br><a href='https://mus-col.com'>Перейти на сайт музея</a></div>
EOF;
		}
}
?>