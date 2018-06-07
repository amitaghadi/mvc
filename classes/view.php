<?php
class View {    
    protected $viewFile;
	private $data=array();
    public function __construct($controllerClass, $action) {
        $controllerName = str_replace("Controller", "", $controllerClass);
        $this->viewFile = "views/" . $controllerName . "/" . $action . ".php";
    }
    public function output($viewModel, $template = "maintemplate") {
        $templateFile = "views/".$template.".php";
        if (file_exists($this->viewFile)) {
            if ($template) {
                //include the full template
                if (file_exists($templateFile)) {
                    require($templateFile);
                } else {
                    require("views/error/badtemplate.php");
                }
            } else {
                require($this->viewFile);
            }
        } else {
            require("views/error/badview.php");
        }
    }
	public function render($viewfile,$maintemplate=''){
		if(file_exists("views/".$viewfile.".php")){
		$data=$this->data;
			require "views/".$viewfile.".php";
		}
	}
	public function render_return($viewfile,$outside_view=''){
		if($outside_view!=""){
			if(file_exists($viewfile)){
				$data=$this->data;
				ob_start();
				require $viewfile;
				$html=ob_get_contents();
				ob_end_clean();
				return $html;	
			}
			
		}
		else if(file_exists("views/".$viewfile.".php")){
			ob_start();
			$data=$this->data;
			require "views/".$viewfile.".php";
			$html=ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	 public function render_html($viewfile){
		if(file_exists($viewfile)){
		$content=file_get_contents($viewfile);
			echo $content;
		}
	 }
	
	public function set_data($key,$value){
		$this->data[$key]=$value;
	}
	private function get_data(){
		return $this->data;
	}
}

?>