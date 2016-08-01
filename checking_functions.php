<?php

	function getStatement($policy) { //get all business statements for corresponding policy
		global $conn;

		$stmt = $conn->prepare("SELECT `id_statement` FROM br_statement br INNER JOIN policy p ON br.id_policy = p.id_policy WHERE p.id_policy = '$policy'");
		$stmt->execute();

		$statements = array();
		$i = 0;
		while ($res = $stmt->fetch()) {
			$statements[$i] = $res['id_statement'];
			$i++;
		}

		return $statements;
	}
	
	function getRules($bs) { //get all rules for corresponding business statement
		global $conn;

		$stmt = $conn->prepare("SELECT nama_predikat FROM predikat p INNER JOIN br_statement br ON br.predikat = p.id_predikat WHERE br.id_statement = '$bs'");
		$stmt->execute();

		$rule = "";
		$i = 0;
		while ($res = $stmt->fetch()) {
			$rule = $res['nama_predikat'];
		}

		return $rule;
	}

	function getTarget($bs) {
		global $conn;

		$stmt = $conn->prepare("SELECT nama_predikat FROM predikat p INNER JOIN br_statement br ON br.target = p.id_predikat WHERE br.id_statement = '$bs'");
		$stmt->execute();

		$rule = "";

		while ($res = $stmt->fetch()) {
			$rule = $res['nama_predikat'];
		}

		// print_r($rule);
		return $rule;
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
		// echo "Masuk \n";
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

	function getCurrentRow($cons) {
		global $conn;

		$values = "";
		if(!empty($cons)) { //only executed when constant is provided
			foreach ($cons as $idx => $arg) {
				if(!isVariable($idx)) {
					$values =  $values.$arg." ";
				}
			}
		}
		// print_r($values);
		return $values;
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
		$stmt = $conn->prepare("SELECT `nama_predikat` FROM `predikat` WHERE id_predikat = (SELECT `target` FROM predikat p INNER JOIN br_statement br ON p.id_predikat = br.predikat WHERE p.nama_predikat = '$predicate')");
		$stmt->execute();
		$res = $stmt->fetch();
		$reference = $res[0];
		// echo $reference."\n";

		$bodies = collectRules($reference);
		$queries = ruleToQuery($bodies, $reference, $cons);
		// print_r($bodies);
		// print_r($queries);

		foreach ($queries as $name => $query) {
			// echo "Masuk \n";
			createTempTable($query, $name);
		}

		//get variable to be compared
		$stmt = $conn->prepare("SELECT isi_argumen FROM predikat p, idb i, argumen_head arg WHERE p.id_predikat = i.id_predikat AND arg.id_rule = i.id_aturan AND p.nama_predikat = '$predicate'");
		$stmt->execute();
		
		$arg = array(); $i=0;
		while($res = $stmt->fetch()) {
			$arg[$i] = $res['isi_argumen'];
			$i++;
		}
		print_r($arg);
		
		// echo $predicate."\n";
		$check = checkingQuery($reference, $predicate, $arg);

		$stmt = $conn->prepare($check);
		$stmt->execute();
		$res = $stmt->fetchAll();
		// print_r($res);

		//reporting using log file(s)
		// while ($res = $stmt->fetch()) {
		// 	echo "Writing to log... \n";
		// 	writeReport($res["nim"]);
		// }

		// $stmt = $conn->prepare("SELECT * FROM last_nr");
		// $stmt->execute();
		// $last = $stmt->fetchAll();
		// print_r($last);

		// $stmt = $conn->prepare("SELECT * FROM last_nr");
		// $stmt->execute();
		// $res = $stmt->fetchAll();
		// print_r($res);
		return $res;
	}

	function generateTarget($predicate) {
		global $conn;

		//get reference table for instance checking
		$stmt = $conn->prepare("SELECT `nama_predikat` FROM `predikat` WHERE id_predikat = (SELECT `target` FROM predikat p INNER JOIN br_statement br ON p.id_predikat = br.predikat WHERE p.nama_predikat = '$predicate')");
		$stmt->execute();
		$res = $stmt->fetch();
		$reference = $res[0];

		$bodies = collectRules($reference);
		generateRef($bodies);
		$queries = ruleToQuery($bodies, $reference, $cons);

		// print_r($queries);
		return $queries;
	}

	function prepareChecking($predicate) { //prepare all tables(s) needed for comparation
		global $conn;

		#generate constant to be checked
		$ref = generateTarget($predicate);
		echo "Reference \n"; print_r($ref);
		foreach ($ref as $name => $query) {
			createView($query, $name);
		}

		$stmt = $conn->prepare("SELECT * FROM $name"); 
		$stmt->execute();
		$cons = $stmt->fetchAll();
		echo "Constant \n"; print_r($cons); 

		#generate table based on rule and constant
		$queries = array(); $j=0;
		foreach ($cons as $value) { 
			$test = collectRules($predicate); //print_r($test);
			generateRef($test); 
			$queries[$j] = ruleToQuery($test, $predicate, $value); print_r($queries[$j]);
			$j++;
		}
		// echo "Queries \n";
		// print_r($queries);


		#generate query for checking
		$check = array(); 
		$instance = array();
		$i=0;
		foreach ($cons as $constant) {
			$check[$i] = countMatch($predicate, $constant);
			$instance[$i] = getCurrentRow($constant);
			$i++;
		}

		#instance checking
		$idx = 0; $j=0;
		$result = array(); $x=0;
		while ($idx<sizeof($queries)) {
			foreach ($queries[$idx] as $table => $query) { //echo $query."\n";
				createView($query, $table);
			}

			$stmt = $conn->prepare($check[$j]); // get instance to be tested
			$stmt->execute();
			$res = $stmt->fetch(); echo $instance[$j]."\n";
			echo "Result \n";
			if($res[0] == 0) {
				$result[$x] = "Instance ".$instance[$j]."violated business rule \n";
				$x++;
			} print_r($res);

			$j++;
			$idx++;
		}
		return $result;
	}

	function writeReport($result, $br) {

		$date = date("F j, Y, g:i a")."\n";
		$subject = "Instance checking for business rule ".$br."\n\n";

		$i=0; $log = "";
		if(sizeof($result)>0) {
			while ($i<sizeof($result)) {
				$log = $log.$result[$i]."\n";
				$i++;
			}
		}
		else {
			$log = "No instance violated business rule \n";
		}

		$put = $date.$subject.$log;

		//file_put_contents('log/log_'.date("j.n.Y").'.txt', $put, FILE_APPEND);
		file_put_contents('log/log_'.date("j.n.Y").'.txt', $put);
	}
?>