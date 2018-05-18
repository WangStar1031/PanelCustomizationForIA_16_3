<?php
	header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

	$data = file_get_contents("search_forms_panels.json");
	$arr_datas = json_decode($data);

	$ret = new stdClass();

	$ret->check_date = false;
	$ret->check_currency = false;
	$ret->check_time = false;

	$query_columns = array();

	$query_columns_str = "";
	if(isset($_POST['q'])) $query_columns_str = $_POST['q'];
	if($query_columns_str == "") {
		$ret->panels = $query_columns;
		echo json_encode($ret);
		exit();
	}

	$origin_columns = explode(",", $query_columns_str);
	// var_dump($origin_columns);
	function __check_in_array($__name, $__arr_columns){
		for($i=0; $i<count($__arr_columns); $i++){
			if($__arr_columns[$i] == $__name)
				return false;
		}
		return true;
	}

	function __compare_columns($columns_2, $columns_1){
		for($i=0; $i<count($columns_1); $i++)
		{
			// echo $columns_1[$i]->name . ", ";
			if( $columns_1[$i]->name == "")
				continue;
			if(__check_in_array($columns_1[$i]->name, $columns_2)){
				return false;
			}
		}
		return true;
	}
	function getColumnsForPanel($panel){
		if( count($panel->tabs) == 0)
			return array();
		$tabs = $panel->tabs[0];
		if( !isset($tabs->columns))
			return array();
		return $tabs->columns;
	}
	function getAllColumnsForPanel($panel){
		if( count($panel->tabs) == 0)
			return array();
		$arrRet = array();
		for( $i = 0; $i < count($panel->tabs); $i++){
			$tabs = $panel->tabs[$i];
			if(!isset($tabs->columns))
				return $arrRet;
			for($j = 0; $j < count($tabs->columns); $j ++){
				array_push($arrRet, $tabs->columns[$j]);
			}
		}
		return $arrRet;
	}
	for($i=0; $i<count($arr_datas); $i++){
		$query_columns = array();

		$check_date = false;
		$check_currency = false;
		$check_time = false;

		// var_dump($i);
		$search_form = $arr_datas[$i];
		$columnsMain = getColumnsForPanel($search_form->panels[0]);//->tabs[0]->columns;
		$columnsSide = getColumnsForPanel($search_form->panels[1]);//->tabs[0]->columns;
		$columnsInline=getColumnsForPanel($search_form->panels[2]);//->tabs[0]->columns;
		if(__compare_columns($origin_columns, $columnsMain) && __compare_columns($origin_columns, $columnsSide) && __compare_columns($origin_columns, $columnsInline)){
			for($j=0; $j<count($columnsMain); $j++){
				if($columnsMain[$j]->dataType == "DATE") $check_date = true;
				if($columnsMain[$j]->dataType == "CURRENCY") $check_currency = true;
				if($columnsMain[$j]->dataType == "TIME") $check_time = true;
				if($columnsMain[$j]->dataType == "PERIOD") $check_date = true;
			}
			for($j=0; $j<count($columnsSide); $j++){
				if($columnsSide[$j]->dataType == "DATE") $check_date = true;
				if($columnsSide[$j]->dataType == "CURRENCY") $check_currency = true;
				if($columnsSide[$j]->dataType == "TIME") $check_time = true;
				if($columnsSide[$j]->dataType == "PERIOD") $check_date = true;
			}
			$inlineAllColumns = getAllColumnsForPanel($search_form->panels[2]);
			for($j=0; $j<count($inlineAllColumns); $j++){
				if($inlineAllColumns[$j]->dataType == "DATE") $check_date = true;
				if($inlineAllColumns[$j]->dataType == "CURRENCY") $check_currency = true;
				if($inlineAllColumns[$j]->dataType == "TIME") $check_time = true;
				if($inlineAllColumns[$j]->dataType == "PERIOD") $check_date = true;
			}

			$ret->check_date = $check_date;
			$ret->check_currency = $check_currency;
			$ret->check_time = $check_time;

			$ret->panels = $arr_datas[$i];
			echo json_encode($ret);
			exit();
		}
	}
	$ret->panels = array();
	echo json_encode($ret);
?>