<?php
class DB_library {
	public static $conn;
	private $resultSet;
	public $affected_rows;
	private $queryData;

	public function __construct($conn) {
		$this->_connect($conn);
	}

	private function _connect($conn) {
		try {
			self::$conn=Open_Connection($conn, 1);
		}
		catch (Exception $e){
			echo "ERROR ".$e->getMessage();
		}
	}

	public function disconnect() {
		self::$conn = null;
		$this->queryData = null;
	}

	public function get_data($stmt) {
		try {
			$this->resultSet = $stmt->execute();
		} catch(PDOException $e) {
			echo "Error:".$e->getMessage();
		}
		$count = 0;
		$count = $stmt->rowCount();

		$data = array();
		if($count > 0) {
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$this->queryData['count'] = $count;
			foreach($row as $rows) {
				$this->queryData['data'][]  = $rows;
			}
			$data = $this->queryData;
			$this->disconnect();

			return $data;
		} else {
			return array('count' => $count, 'data' => '');
		}

	}

	public function execute_query($stmt) {
		try {
			$resultSet = $stmt->execute();
			$return_array['last_insert_id'] = self::$conn->lastInsertId();
			$return_array['row_count'] = $stmt->rowCount();
		} catch(PDOException $e) {
			echo "Error:".$e->getMessage();
			echo "<br>\n".$e->getTrace();
			echo "<br>\n".$e->getTraceAsString();
			exit();
		}
		return $return_array;
	}

	// public function ...
}