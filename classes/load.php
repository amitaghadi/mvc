<?php
class Load{
	function model($name){
		$model="models/".$name.".php";
		if(file_exists($model)){
			require_once($model);
			$obj=$name."_model";
			return new $obj;
		}
	}
	function library($name,$params=array()){
		$library="libraries/".$name.".php";
		if(file_exists($library)){
			require_once($library);
			$obj=$name."_library";
			
			return new $obj ($params);
		}
	}
	function config($name){
		$config="config/".$name.".php";
		if(file_exists($config)){
			require_once($config);
		}
	}
}
