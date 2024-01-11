<?php
	require 'PhpOffice/autoload.php';
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class excel{
	function exel_content(){
		global $DB;
		$array = array();
		$i=0;
		$DB->query("SELECT orders.*, event_time.event_themes_id,event_time.time,event_themes.name FROM orders LEFT JOIN event_time ON event_time.id = orders.event_time_id LEFT JOIN event_themes ON event_themes.id = event_time.event_themes_id");
		while (($row=$DB->fetch_row())!=false) {
					$row['date']=date("d.m.Y",$row['time']);
					$row['time']=date("H:i",$row['time']);
					$row['date_of_birth']=date("d.m.Y",$row['date_of_birth']);
					$array[$i]=array('full_name'=> $row['full_name'],'date_of_birth'=>$row['date_of_birth'],'mail'=>$row['mail'],'date'=>$row['date'],'time'=>$row['time']);
					$i++;
				}
		$this->create($array);
		
	}
	
	
	function create($array=array()){
	$i=1;
		//Создаем экземпляр класса электронной таблицы
$spreadsheet = new Spreadsheet();
//Получаем текущий активный лист
$sheet = $spreadsheet->getActiveSheet();


		foreach($array as $row){
			$sheet->getColumnDimension('A')->setWidth(50);
			$sheet->getColumnDimension('B')->setWidth(50);
			$sheet->getColumnDimension('C')->setWidth(50);
			$sheet->setCellValue('A'.$i, $row['date']);
			
			$i+=3;
		}



$writer = new Xlsx($spreadsheet);
//Сохраняем файл в текущей папке, в которой выполняется скрипт.
//Чтобы указать другую папку для сохранения. 
//Прописываем полный путь до папки и указываем имя файла
$writer->save('hello.xlsx');
	}
}
?>