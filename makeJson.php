<?php
	
	$g_dir = "C:/xampp/htdocs/panel_custom/work";

	$pattern = "resultmaster.resource";

	$arr_search_forms = [];

	function proc_file($dir_name, $search_file) {
		var_dump($dir_name);
		global $arr_search_forms;

		$data = @file_get_contents($search_file);
		$data_obj = json_decode($data);

		$search_name = $data_obj->searchName;
		$search_name = str_replace("-", " ", $search_name);
		$search_name = str_replace(" ", "_", $search_name);
		$search_name = str_replace(".", "_", $search_name);
		$search_name = str_replace("___", "_", $search_name);
		$search_name = str_replace("__", "_", $search_name);

		$search_obj = new \stdClass;
		$search_obj->dir_name = $dir_name;
		$search_obj->search_name = $search_name;

		$arr_panels = [];
		$panels = $data_obj->panels;
		foreach ($panels as $panel) {
			$panel_obj = new \stdClass;
			$arr_tabs = [];
			$tabs = $panel->tabs;
			foreach ($tabs as $tab) {
				$tab_obj = new \stdClass;
				$arr_columns = [];
				$columns = $tab->columns;
				foreach ($columns as $column) {
					$column_obj = new \stdClass;
					$column_obj->name = $column->name;
					$column_obj->label = $column->label;
					$column_obj->hidden = $column->hidden;
					$column_obj->dataType = $column->dataType;
					$arr_columns[] = $column_obj;
				}
				$tab_obj->tabName = $tab->name;
				$tab_obj->title = $tab->title;
				$tab_obj->columns = $arr_columns;
				$arr_tabs[] = $tab_obj;
			}
			$panel_obj->panelName = $panel->name;
			$panel_obj->tabs = $arr_tabs;
			$arr_panels[] = $panel_obj;
		}
		$search_obj->panels = $arr_panels;
		for($i=0;$i<count($arr_search_forms); $i++){
			if($arr_search_forms[$i]->search_name == $search_obj->search_name) return false;
		}
		$arr_search_forms[] = $search_obj;
	}

	function check_dir($dir) { 
		global $check_date, $pattern, $g_dir;

	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $file) { 
	     	if ($file != "." && $file != ".." && $file != "logs") { 
	          if (is_dir($dir."/".$file)){
				//echo "<br>$dir/$file is directory";
				check_dir($dir."/".$file);
	          } else if(is_file($dir."/".$file)) {
	          	if(strlen($file) >= strlen($pattern)){
		          	if(substr($file, 0, strlen($pattern)) == $pattern){
		          		proc_file(substr($dir, strlen($g_dir)+1), $dir."/".$file);
		          	}
		          }
			  }
			}
	     }
	     //check_dir($dir); 
	   } 
	 }

	 check_dir($g_dir);
	 $str_data = json_encode($arr_search_forms);
	 $str_data = str_replace('{"dir_name"', "\n\r".'{"dir_name"', $str_data);
	 $str_data = str_replace('{"search_name"', "\n\r".'{"search_name"', $str_data);
	 $str_data = str_replace('"panels"', "\n\r\t".'"panels"', $str_data);
	 $str_data = str_replace('"panelName"', "\n\r\t\t".'"panelName"', $str_data);
	 $str_data = str_replace('"tabs"', "\n\r\t\t\t".'"tabs"', $str_data);
	 $str_data = str_replace('"tabName"', "\n\r\t\t\t".'"tabName"', $str_data);
	 $str_data = str_replace('"columns"', "\n\r\t\t\t\t".'"columns"', $str_data);
	 $str_data = str_replace('{"name"', "\n\r\t\t\t\t\t".'{"name"', $str_data);
	 file_put_contents("search_forms_panels.json", $str_data);
	 echo "End";
?>