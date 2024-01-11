<?php
	class protect{
		function inject(){
			if(isset($_GET)){
				foreach ($_GET as $key => $val) {
					if(substr_count($val,"SELECT") OR substr_count($val,"FROM") OR substr_count($val,"DELET") OR substr_count($val,"LIKE")OR substr_count($val,"WHERE") OR substr_count($val,"DROP") OR substr_count($val,"TABLE")){
						header("location:index.php");
						exit;
					}
				}	
			}
			if(isset($_POST)){
				foreach ($_POST as $key => $val) {
					if(substr_count($val,"SELECT") OR substr_count($val,"FROM") OR substr_count($val,"DELET") OR substr_count($val,"LIKE")OR substr_count($val,"WHERE")OR substr_count($val,"DROP") OR substr_count($val,"TABLE")){
						$val='';
					}
				}	
			}
		}
	}		
?>