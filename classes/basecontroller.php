<?php
abstract class BaseController {
    protected $urlValues;
    protected $action;
    protected $model;
    protected $view;
    public function __construct($action, $urlValues) {
        $this->action = $action;
        $this->url_segments = $urlValues;
        $this->view = new View(get_class($this), $action);
		$this->view->set_data("url_params",$this->url_segments);
    }
    public function executeAction() {
		//error_reporting(E_ALL);
				return call_user_func_array(array($this,$this->action),$this->url_segments['remaining']);
        // return $this->{$this->action}();
    }
	protected function ReturnView($viewmodel,$fullview){
		$viewloc='views/'.get_class($this).'/'.$this->action.'.php';
			if($fullview){
				require('views/maintemplate.php');
			}
			else {
				require($viewloc);
			}
	}
}

?>
