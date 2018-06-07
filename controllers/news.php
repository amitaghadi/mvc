<?php 

class NewsController  extends BaseController{

	public function __construct($action, $urlValues) {
		parent::__construct($action, $urlValues);
		$this->load = new Load();
		$this->common_library = $this->load->library('Common');
	}
	public function index($domain){
		$this->news_model = $this->load->model("news");	 
		$this->network = $this->load->library('network');
		// $domain = "gmail.com";
		$dns_stat = $this->network->check_dns($domain);
		$dns_record = $this->network->get_dns_record($domain);
		$ip = $this->network->get_host_by_name($domain);// ip address return from nslookup
		$host_list = $this->network->get_host_by_name_list($domain);// ip address return from nslookup	 
		$hostname = $this->network->get_hostname($ip);
		$list_protocall = $this->network->get_list_of_protocall();
		// $this->network->set_sys_log_var 

		//Reads the names of protocols into an array..
		foreach ($GLOBALS['protocall_list'] as $key => $protocall) {
			 
			echo $protocall .":", getprotobyname ($protocall)."<br />";
		}



		foreach ($GLOBALS['services'] as $service) {
		    $port = getservbyname($service, 'tcp');//Get port number associated with an Internet service and protocol
		    echo $service . "---------- " . $port . "<br />\n";
		}

		$http_response = $this->network->http_response_code(); //get and set http_response code 



		 
		echo "<pre>";  
			var_dump($dns_stat);
			var_dump($dns_record);
			var_dump($hostname);
			var_dump($host_list);
			var_dump($list_protocall);
			var_dump($http_response);
		echo "</pre>";




	}
}

?>