<?php
	
	function getRules($bs) { //get all rules for corresponding business statement
		global $conn;

		$stmt = $conn->prepare("SELECT nama_predikat FROM predikat p INNER JOIN br_statement br ON br.predikat = p.id_predikat WHERE br.id_statement = '$bs'");
		$stmt->execute();

		$rules = array();
		$i = 0;
		while ($res = $stmt->fetch()) {
			$rules[$i] = $res['nama_predikat'];
			$i++;
		}

		return $rules;
	}

	function getReference($predicate) { //get reference table for a predicate
		global $conn;

		$ref = new Reference();
		$stmt = $conn->prepare("SELECT `id_ref`, `table_name`, `db_name` FROM `reference` WHERE id_ref = '$predicate'");
		$stmt->execute();

		$id_ref = "";
		while ($res = $stmt->fetch()) {
			$id_ref = $res['id_ref'];
			$ref->setDatabase($res['db_name']);
			$ref->setTablename($res['table_name']);
		}

		$attr = array();
		$stmt = $conn->prepare("SELECT `attr_name` FROM `ref_attribute` WHERE id_ref = '$id_ref' ORDER BY `order` ASC");
		$stmt->execute();

		$i=0;
		while ($res = $stmt->fetch()) {
			$attr[$i] = $res['attr_name'];
			$i++;
		}
		$ref->setAttributes($attr);

		// print_r($ref);
		return $ref;
	}

	function generateRef($bodies) { //prepare reference for predicate

		$queries = array(); $i=0;
		while($i<sizeof($bodies)) {
			foreach ($bodies[$i] as $body) {
				$predicate = $body->getPredicate();

				if(!isOperator($predicate) && !isIDB($predicate)) {
					$ref = getReference($predicate);
					$queries[$predicate] = refToQuery($ref);
				}
			}
			$i++;
		}
		
		foreach ($queries as $name => $query) {
			createView($query, $name);
		}
	}

	function getCurrentData($idb) { //get snapshot of data which will be checked
		global $conn;

		//get rule id
		$stmt = $conn->prepare("SELECT id_aturan FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE p.nama_predikat = '$idb'");
		$stmt->execute();
		$res = $stmt->fetch();
		$id = $res[0];

		$ref = new Reference();
		//get reference table for rule
		$stmt = $conn->prepare("SELECT db_name, table_name FROM rule_ref WHERE id_rule = $id");
		$stmt->execute();
		while ($res = $stmt->fetch()) {
			$ref->setDatabase($res['db_name']);
			$ref->setTablename($res['table_name']);
		}

		$stmt = $conn->prepare("SELECT attr_name FROM rule_ref ref INNER JOIN rule_attribute attr ON ref.reference = attr.id_ref WHERE ref.id_rule = $id ORDER BY `urutan` ASC");
		$stmt->execute();
		
		$data = array(); $i = 0;
		while ($res = $stmt->fetch()) {
			$data[$i] = $res['attr_name'];
			$i++;
		}
		$ref->setAttributes($data);

		// print_r($ref);
		return $ref;
	}

	function generateData($idb) {

		//get rule id
		$stmt = $conn->prepare("SELECT id_aturan FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE p.nama_predikat = '$idb'");
		$stmt->execute();
		$res = $stmt->fetch();
		$id = $res[0];

		$ref = new Reference();
		//get reference table for rule
		$stmt = $conn->prepare("SELECT `reference` FROM rule_ref WHERE id_rule = $id");
		$stmt->execute();
		$res = $stmt->fetch();
		$name = $res[0];

		$data = getCurrentData($idb);
		$query = refToQuery($data); 
		//SELECT [attributes] FROM [database].[table_name]
		
		createTempTable($query, $name);
	}

	function checkInstance($predicate) {
		global $conn;

		//get reference table for instance checking
		$stmt = $conn->prepare("SELECT `reference` FROM rule_ref r, idb i, predikat p WHERE r.id_rule = i.id_aturan AND i.id_predikat = p.id_predikat AND p.nama_predikat = '$predicate'");
		$stmt->execute();
		$res = $stmt->fetch();
		$reference = $res[0];

		//get attribute to be compared with
		$stmt = $conn->prepare("SELECT attr_name FROM rule_attribute WHERE id_ref = '$reference'");
		$stmt->execute();
		
		$target = array(); $i=0;
		while($res = $stmt->fetch()) {
			$target[$i] = $res['attr_name'];
			$i++;
		}

		//get variable to be compared
		$stmt = $conn->prepare("SELECT isi_argumen FROM predikat p, idb i, argumen_head arg WHERE p.id_predikat = i.id_predikat AND arg.id_rule = i.id_aturan AND p.nama_predikat = '$predicate'");
		$stmt->execute();
		
		$arg = array(); $i=0;
		while($res = $stmt->fetch()) {
			$arg[$i] = $res['isi_argumen'];
			$i++;
		}
		
		$check = checkingQuery($reference, $predicate, $target, $arg);

		$stmt = $conn->prepare($check);
		$stmt->execute();
		$res = $stmt->fetchAll();
		print_r($res);

		//reporting using log file(s)
		// while ($res = $stmt->fetch()) {
		// 	echo "Writing to log... \n";
		// 	writeReport($res["nim"]);
		// }

		// $stmt = $conn->prepare("SELECT * FROM max24_sks");
		// $stmt->execute();
		// $res = $stmt->fetchAll();
		// print_r($res);


		// $stmt = $conn->prepare("SELECT * FROM last_nr");
		// $stmt->execute();
		// $res = $stmt->fetchAll();
		// print_r($res);
	}

	function writeReport($result) {

		$date = date("F j, Y, g:i a");
		$log = $result." violates business rule \n";

		$put = $date." ".$log;

		file_put_contents('log/log_'.date("j.n.Y").'.txt', $put, FILE_APPEND);
	}
?>