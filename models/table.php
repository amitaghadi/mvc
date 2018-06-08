<?php 
class Table_model extends BaseModel{
		public function __construct(){
			parent::__construct();
			$this->load = new Load();
		}

	public function seelct_table($input){
		$sql = "SELECT * FROM ".$input['table_name'];
		$this->database=$this->load->library("DB","loacal_host");
		$stmt=DB_library::$conn->prepare($sql);		 
		$data=$this->database->get_data($stmt);
		return $data['data'];
	}

	public function insert_into_table($input){
		$sql = "INSERT INTO ".$input['table_name']." set "; 
		$instr=''; 
		foreach ($input['fields'] as $key => $value) {
			if($value=='now'){
				$instr .= $key."=now(),";
			}else{
				$instr .= $key."='".$value."',";
			}
		}
		$instr = trim($instr,',');
		$sql = $sql.$instr;
		$this->database=$this->load->library("DB","loacal_host");
		$stmt=DB_library::$conn->prepare($sql);		 
		$data=$this->database->execute_query( $stmt );

		//Get last inserted record (to return to jTable)
		 $sql1 = "SELECT * FROM ".$input['table_name']." WHERE PersonId ='".$data['last_insert_id']."'";
		$this->database=$this->load->library("DB","loacal_host");
		$stmt1=DB_library::$conn->prepare($sql1);		 
		$data1=$this->database->get_data($stmt1);		 
		return $data1['data'];		
	}

	public function update_table($input){

		$sql = "UPDATE ".$input['table_name']." set "; 
		$instr=''; 
		foreach ($input['fields'] as $key => $value) {
			if($value=='now'){
				$instr .= $key."=now(),";
			}else if(!in_array($key,$input['where_list'])){
				$instr .= $key."='".$value."',";
			}
		}
		$instr = trim($instr,',');
		$where = " WHERE PersonId = '". $input['where']['PersonId']."'";
 		$sql = $sql.$instr.$where; 
		$this->database=$this->load->library("DB","loacal_host");
		$stmt=DB_library::$conn->prepare($sql);		 
		$data=$this->database->execute_query( $stmt );
		// echo $data['last_insert_id'];
		return $data['last_insert_id'];	
	}

	public function delete_from($input){
		$sql = "DELETE FROM ".$input['table_name']." WHERE PersonId = '" .$input['where']["PersonId"] . "'";
		$this->database=$this->load->library("DB","loacal_host");
		$stmt=DB_library::$conn->prepare($sql);		 
		$data=$this->database->execute_query( $stmt );
	}

}
?>