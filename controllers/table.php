<?php 

class TableController  extends BaseController{
	public function __construct($action, $urlValues) {
		parent::__construct($action, $urlValues);
		$this->load = new Load();
		$this->table_model = $this->load->model("table");	
	}
	public function index(){
		$genindex_html = $this->view->render_return( 'table/index' );
		echo $genindex_html;
	}
	public function list_table($table_name){
		$input = $_POST;
		$input['table_name'] = $table_name;
		$list = $this->table_model->seelct_table($input);
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Records'] = $list;
		echo $json = json_encode($jTableResult);
	}

	public function insert_into_table($table_name){
		$input['fields'] = $_POST;
		$input['table_name'] = $table_name;
		$list = $this->table_model->insert_into_table($input);
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $list;
		print json_encode($jTableResult);
	}
	 
	public function update_table($table_name){
		$input['fields'] = $_POST;
		$input['where']['PersonId'] = $_POST['PersonId'];
		$input['where_list'] = array('PersonId');
		$input['table_name'] = $table_name;
		$list = $this->table_model->update_table($input);
		$jTableResult = array();
		$jTableResult['Result'] = "OK";		 
		print json_encode($jTableResult);
	}

	public function delete_from($table_name){
		$input['where']['PersonId'] = $_POST['PersonId'];
		$input['table_name'] = $table_name;
		$list = $this->table_model->delete_from($input);
		$jTableResult = array();
		$jTableResult['Result'] = "OK";		 
		print json_encode($jTableResult);

	}

} 

?>