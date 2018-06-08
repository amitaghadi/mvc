<?php
ob_start();
session_start();
if(isset($_REQUEST['show']) && $_REQUEST['show'] =='errors'){
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}else{
	error_reporting(0);
	ini_set("display_errors", 0);
}
 

require("classes/basecontroller.php");  
require("classes/basemodel.php");
require("classes/view.php");
require("classes/viewmodel.php");
require("classes/loader.php");
require("classes/load.php");

require_once("config/config.php");
require_once("config/constants.php");
require_once("include/mysql_con.php");

 
// secondary config include start
// secondary config file should be on name of controller like controllerName_constants.php
// and should be located in config folder.
$q=$_GET['q'];
$q_arr=explode("/",$q);
$controller=$q_arr[0];
$configfile="config/".$controller."_constants.php";
// echo $configfile;
if(file_exists($configfile)){
	require_once($configfile);
}
// end secondary config include



/* if (!file_exists("controllers/" . $_GET['controller'] . ".php") && $_GET['controller']!=""){
 $_GET['param1']=$_GET['controller'];
 $_GET['controller']="home";
 $_GET['action']="category";
 }*/
 
// $cookieObj = new Cookie;
 $loader = new Loader(); //create the loader object
$controller = $loader->createController(); //creates the requested controller object based on the 'controller' URL value
$controller->load=new Load();
$controller->executeAction(); //execute the requested controller's requested method based on the 'action' URL value. Controller methods output a View.

?>