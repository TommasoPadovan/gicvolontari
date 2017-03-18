<?php
class DbConnection {
	private $pdo;

	public function __construct() {
		$this->pdo = new PDO('mysql:host=127.0.0.1;dbname=liltvolontari;charset=utf8mb4', 'root', '');
	}

	//public function getPDO() {return $this->pdo;}

	public function deleteRows($table, $arrayWhere = NULL) {
		if ($arrayWhere != NULL)
			return $this->simpleQuery('DELETE', $table, $arrayWhere);
		else
			return $this->queryAllTable('DELETE', $table);
	}

	public function select($table, $arrayWhere = NULL) {
		if ($arrayWhere != NULL)
			return $this->simpleQuery('SELECT *', $table, $arrayWhere);
		else
			return $this->queryAllTable('SELECT *',$table);
	}

	public function update($table, $arrayWhat, $arrayWhere) {
		$query = "UPDATE $table SET ";
		foreach ($arrayWhat as $columnName => $value)
			$query.= "$columnName = :$columnName, ";
		$query = substr($query, 0, -2);
		$query.= " WHERE ";
		foreach ($arrayWhere as $columnName => $value)
			$query.= "$columnName = :$columnName AND ";
		$query = substr($query, 0, -5);


		$statement = $this->pdo->prepare($query);

//		$colonArray = array();
//		foreach ($arrayWhat as $columnName => $value)
//			$colonArray[':'.$columnName] = $value;
//		foreach ($arrayWhere as $columnName => $value)
//			$colonArray[':'.$columnName] = $value;

		$statement->execute(array_merge($arrayWhat, $arrayWhere));

		return $statement->rowCount();

	}


	public function insert ($table, $arrayWhat) {
		$query = "INSERT INTO $table(";
		foreach ($arrayWhat as $columnName => $value)
			$query.="$columnName, ";
		$query = substr($query, 0, -2);
		$query.=") VALUES(";
		foreach ($arrayWhat as $columnName => $value)
			$query.=":$columnName, ";
		$query = substr($query, 0, -2);
		$query.=")";


		$statement = $this->pdo->prepare($query);
		$colonArray = array();
		foreach ($arrayWhat as $columnName => $value)
			$colonArray['.'.$columnName] = $value;
		$statement->execute($arrayWhat);

		//ritorna il numero di righe affette
		return $statement->rowCount();
	}


	public function query($query) {
		return $this->pdo->query($query);
	}

	public function prepare($query) {
		return $this->pdo->prepare($query);
	}


	public function getUserName($id) {
		$statement = $this->pdo->prepare("SELECT * FROM Users WHERE id=:id");
		$statement->execute(array(':id' => $id));
		$user = $statement->fetchAll(PDO::FETCH_ASSOC);
		$user = $user[0];
		return ($user['firstname'].' '.$user['lastname']);
	}

	public function getUser($id) {
		$statement = $this->pdo->prepare("SELECT * FROM Users WHERE id=:id");
		$statement->execute(array(':id' => $id));
		$user = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $user[0];
	}













	private function queryAllTable($action, $table) {
		$query = "$action FROM $table";
		$statement = $this->pdo->prepare($query);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	private function simpleQuery($action, $table, $arrayWhere) {
		$query = "$action FROM $table WHERE ";
		foreach ($arrayWhere as $columnName => $value) {
			$query.="$columnName = :$columnName AND ";
		}
		$query = substr($query, 0, -5);

		$statement = $this->pdo->prepare($query);
		$colonArray = array();
		foreach ($arrayWhere as $columnName => $value)
			$colonArray['.'.$columnName] = $value;
		$statement->execute($arrayWhere);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}




/*
function connect() {
	//$c=mysql_connect("127.0.0.1","root","");
	//mysql_select_db("liltvolontari");
	//return $c;
	return new PDO('mysql:host=127.0.0.1;dbname=liltvolontari;charset=utf8mb4', 'root', '');
}
*/

/*
function queryThis($query, $conn) {
	$result=mysql_query($query,$conn)
		or die("Query Fallita ".mysql_error($conn));
	return $result;
}

function getUserName($id, $conn) {
	$result=queryThis("SELECT * FROM Users WHERE id='$id'",$conn);
	$row=mysql_fetch_assoc($result);
	return ($row['firstname'].' '.$row['lastname']);
}
*/
?>