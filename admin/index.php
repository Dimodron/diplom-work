<?php	
	namespace FUNC;
	error_reporting(E_ALL & ~E_NOTICE);//скрывать notice
	date_default_timezone_set('Europe/Moscow');
	require  "autoload.php";
	
	use FUNC\FUNC;
	use FUNC\DB;
	use FUNC\VERIFICATION;
	
	class GENERAL{
		public static $input;
		public static $settings = array();
		function __construct(){
			self::$input = VERIFICATION::parse_incoming();	
			DB::$obj=array( "sql_database"   => "museum_record"         ,
                       "sql_user"       => "root"     ,
                       "sql_pass"       => ""         ,
                       "sql_host"       => "localhost");
			DB::connect();	
			
			DB::query("SELECT * FROM `settings`");
			while (($row=DB::fetch_row())!=false) {
				self::$settings[$row['name']]= $row['value'];
			}
		}
	}

	$start = new GENERAL();

	switch(GENERAL::$input['act']){
		case 'event_themes':
			FUNC::event_themes();
		break;
		case 'event_time':
			FUNC::event_time();
		break;
		case 'orders':
			FUNC::orders();
		break;
		default:
			FUNC::event_themes();
		break;
	}
?>