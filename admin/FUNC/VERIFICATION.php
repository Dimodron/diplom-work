<?php
namespace FUNC;
use DateTime;
class VERIFICATION{
	static private $allow_unicode = 1;
	static private $input = array();
  
  /*-------------------------------------------------------------------------*/
    // Makes incoming info "safe"              
    /*-------------------------------------------------------------------------*/

	public static function parse_incoming() {
		$result = array();
		//a correct IP shouldn't be affected by this
		$_SERVER['REMOTE_ADDR'] = htmlspecialchars($_SERVER['REMOTE_ADDR']);
		
		//reset incoming if already used
		reset($_GET);
		reset($_POST);
		reset($_FILES);
		//import GET
		if( is_array($_GET) )	{
			foreach( $_GET as $k=>$v ) {
                		if (strpos($k, "amp;") === 0) $k = substr($k, 4);
		
				if( is_array($_GET[$k]) ) {
					foreach( $_GET[$k] as $k2=>$v2 ) {
						$result[$k][ self::clean_key($k2) ] = self::clean_value($v2);
					}
				} else {
					$result[$k] = self::clean_value($v);
				}
			}
		}
		
		// Overwrite GET data with post data
		if( is_array($_POST) ) {
			foreach( $_POST as $k=>$v ) {
				if ( is_array($_POST[$k]) ) {
					foreach( $_POST[$k] as $k2=>$v2 ) {
						$result[$k][ self::clean_key($k2) ] = self::clean_value($v2);
					}
				} else {
					$result[$k] = self::clean_value($v);
				}
			}
		}
		
		//process _FILES if there is
		if( !empty($_FILES) ) {
			if (is_array($_FILES)) {
				foreach ($_FILES as $k => $v) {
					$_FILES[$k]['name'] = trim(strval($_FILES[$k]['name']));
					//fix some browsers
					if ($_FILES[$k]['name'] == 'http://') $_FILES[$k]['name'] = '';
					$_FILES[$k]['name'] = preg_replace("#/$#", '', $_FILES[$k]['name']);
					$_FILES[$k]['name'] = preg_replace("/[^a-zA-Z0-9\_\-\.]/", '' , $_FILES[$k]['name']);
					$_FILES[$k]['name'] = preg_replace('#\.{1,}#s', '.', $_FILES[$k]['name']);
					$_FILES[$k]['name'] = preg_replace('#\_{2,}#s', '_', $_FILES[$k]['name']);

					$_FILES[$k]['type'] = trim(strval($_FILES[$k]['type']));
					//fix for Opera
					$_FILES[$k]['type'] = preg_replace("/^(.+?);.*$/", "\\1", $_FILES[$k]['type']);

					$_FILES[$k]['tmp_name'] = trim(strval($_FILES[$k]['tmp_name']));

					$_FILES[$k]['size'] = intval($_FILES[$k]['size']);
				}
			} else {
					$_FILES = array(
						'name'     => '',
						'type'     => '',
						'tmp_name' => '',
						'size'     => 0,
					);
			}
		}

        	return $result;
	}

	private static function clean_key($key) {
    
		if ($key == "")	return "";
	    	$key = preg_replace( "/\.\./"           , ""  , $key );
	    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
	    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
	    	return $key;
	}
    
	private static function clean_value($val)  {
	
	    	if ($val == "") return "";
	    	
	    	$val = str_replace( "&#032;", " ", $val );    	
	    	$val = str_replace( "&"            , "&amp;"         , $val );
	    	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
	    	$val = str_replace( "-->"          , "--&#62;"       , $val );
	    	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
	    	$val = str_replace( ">"            , "&gt;"          , $val );
	    	$val = str_replace( "<"            , "&lt;"          , $val );
	    	$val = str_replace( "\""           , "&quot;"        , $val );
	    	$val = preg_replace( "/\n/"        , "<br>"          , $val ); // Convert literal newlines
	    	$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
	    	$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
	    	$val = str_replace( "!"            , "&#33;"         , $val );
	    	$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
	    	
	    	// Ensure unicode chars are OK
	    	
	    	if ( self::$allow_unicode )
			{
				$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
			}
			

	    	// Swop user inputted backslashes
	    	
	    	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
	
	    	return $val;
	}
	public static function validateDate($date, $format = 'Y-m-d H:i:s'){
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}

}	
?>				