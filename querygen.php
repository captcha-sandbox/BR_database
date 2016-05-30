<?php	
	include "sql_connect.inc";
	include "classes.php";
	include "rule_functions.php";
	include "checking_functions.php";

	//Functions
	function getTableRef($bodies) {
		$ref = array();

		$i = 0;
		foreach ($bodies as $body) {
			$current = $body->getPredicate();
			if(!in_array($current, $ref) && !isOperator($current)) { //check if element is already exist in array or a comparator
				$ref[$i] = $body->getPredicate();
				$i++;
			}
		}

		return $ref;
	}

	function getProjection($head, $sub) {
		global $conn;

		$id = $head[0]->getRuleId();
		$res = array();
		$stmt = $conn->prepare("SELECT `isi_argumen` FROM `argumen_head` WHERE id_rule = $id");
		$stmt->execute();

		$i = 0;
		while($body = $stmt->fetch()) {
			$attr = substr($sub[$body['isi_argumen']], strpos($sub[$body['isi_argumen']], ".") + 1); //get text after "."
			$res[$i] = $sub[$body['isi_argumen']]." AS ".$body['isi_argumen'];
			$i++;
		}

		return $res;
	}

	// needed for performance optimization 
	function getOnProperties($bodies) {
		global $conn;
		$predicate = $bodies[0]->getPredicate();
		$args = array();
		$i = 0;
		# get main predicate to be compared
		foreach ($bodies as $body) {
			if($body->getPredicate() == $predicate) {
				$args[$i] = $body->getContent();
				$i++;
			}
		}
		//print_r($args);
		echo $predicate."\n";
		$res = array();
		$i = 0;
		# get attribute name associated to argument(s) name
		$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.id_ref = '$predicate'");
		$stmt->execute();
		while($body = $stmt->fetch()) {

			$res[$args[$i]] = $body['attr_name'];
			$i++;
		}
		$on = array();
		# get another predicate for comparator
		$i = 0;
		if(hasPrevious($bodies, $args)) {
			$prev = getPrevious($res, $bodies, $args);
			$val = getPreviousVal($bodies, $args);
			foreach ($bodies as $body) {
				if(($body->getPredicate() != $predicate) && (in_array($body->getContent(), $args)) && !isOperator($body->getPredicate())) {
						if(!isIDB($predicate)) {
						//echo $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()]."\n";
						$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$prev[$body->getContent()];
						//echo $on[$i]."\n";
						}
						else {
						$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$val[$body->getContent()];
						}
					$i++;
				}
			}
		}
		else {
			foreach ($bodies as $body) {
				if(($body->getPredicate() != $predicate) && (in_array($body->getContent(), $args)) && !isOperator($body->getPredicate())) {
						if(!isIDB($predicate)) {
						//echo $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()]."\n";
						$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()];
						//echo $on[$i]."\n";
						}
						else {
						$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$body->getContent();
						}
					$i++;
				}
			}
		}
		
		// print_r($on);
		return $on;
	}

	function getSelection($bodies) {

		$substitution = substituteVar($bodies); //get variable substitute
		$conditions = array(); //argument(s) for selection
		$i = 0; $idx = 0;
		
		while($i < sizeof($bodies)) {
			if(isComparator($bodies[$i]->getPredicate())) {
				if(isVariable($bodies[$i+1]->getContent())) {
					$conditions[$idx] = $substitution[$bodies[$i]->getContent()]." ".$bodies[$i]->getPredicate()." ".$substitution[$bodies[$i+1]->getContent()];
					}
				else {
					$conditions[$idx] = $substitution[$bodies[$i]->getContent()]." ".$bodies[$i]->getPredicate()." ".$bodies[$i+1]->getContent();
				}
				$i++; $idx++;
			}
			$i++;
		}
		// echo "This is condition \n";
		print_r($conditions);
		return $conditions;
	}

	function getQueryVal($predicate, $bodies, $cons) {

		$substitution = substituteVar($bodies);
		$value = assignConstant($predicate, $substitution, $cons); //get value from user's input
		$values =array(); //condition from user input
		
		$idx = 0;
		foreach($value as $var => $arg) {
			if($arg != $substitution[$var]) {
				$values[$idx] = $substitution[$var]." = ".$arg;
				$idx++;
			}
		}
		//print_r($values);
		return $values;
	}

	function generateQuery($predicate, $bodies, $cons) {

		//get head argument
		$head = getHead($predicate);
		$sub = substituteVar($bodies);

		#query generator
	 	$from = getTableRef($bodies);
	 	$select = getProjection($head, $sub);
	 	$join = getOnProperties($bodies);
	 	$where = getSelection($bodies);
	 	$value = getQueryVal($predicate, $bodies, $cons);
	 	// print_r($join);
	 	// print_r($select);

	 	$tables = ""; //combine reference table(s)
	 	for($i=0; $i<sizeof($from); $i++) {
	 		if($i<1) {
	 			$tables = $tables.$from[$i];
	 		}
	 		else {
	 			$tables = $tables.", ".$from[$i];
	 		}
	 	}

	 	$attr = ""; //combine projection attribute
		for($i=0; $i<sizeof($select); $i++) {
	 		if($i<1) {
	 			$attr = $attr.$select[$i];
	 		}
	 		else {
	 			$attr = $attr.", ".$select[$i];
	 		}
	 	}

	 	$on = ""; //combine join attribute
		for($i=0; $i<sizeof($join); $i++) {
			
		 		if($i<1) {
		 			$on = $on.$join[$i];
		 		}
		 		else {
		 			$on = $on." AND ".$join[$i];
		 		}
	 	}

	 	$condition = ""; //combine selection condition
	 	for($i=0; $i<sizeof($where); $i++) {
	 		if(empty($join)) {
	 			$condition = $condition." ".$where[$i];	
	 		}
	 		else {
	 			$condition = $condition." AND ".$where[$i];
	 		}
	 	}

	 	$constant = ""; //combine input from user
	 	for($i=0; $i<sizeof($value); $i++) {
	 		$constant = $constant." AND ".$value[$i];
	 	}

 		// $generate = "SELECT ".$attr." FROM ".$tables." WHERE ".$on." ".$condition." ".$constant;
	 	$generate = "SELECT ".$attr." FROM ".$tables." WHERE ".$on." ".$condition;
	 	// echo ($generate)."\n";
	 	
	 	
	 	return $generate;
	}

	function ruleToQuery($bodies, $predicate, $cons) {

		$idb = getIDBList($predicate);
		$idx = 0;
		$queries = array();

		$i = 0;
		while($i<sizeof($bodies)) {
			if(hasVariant($idb[$idx])) {
				$union = "";
				$predicate = $idb[$idx];
				for($j=$i; $j<=numVariant($predicate)+$i-1; $j++) {
					//print_r($bodies[$j]);
					if($j == $i) {
						$union = $union.generateQuery($predicate, $bodies[$j], $cons);	
					}
					else {
						$union = $union." UNION ".generateQuery($predicate, $bodies[$j], $cons);	
					}
				}
				$queries[$predicate] = $union;			
				$i = $j-1;
			}
			else {
				//print_r($bodies);
				$queries[$idb[$idx]] = generateQuery($idb[$idx], $bodies[$i], $cons);
			}
			$idx++;
			$i++;
		}
		return $queries;
	}

	function refToQuery($reference) { //create query for predicate reference

		$database = $reference->getDatabase();
		$table = $reference->getTablename();
		
		$attributes = ""; // get projection attribute
		$attr = $reference->getAttributes();
		for($i=0; $i<sizeof($attr); $i++) {
			if($i == 0) {
				$attributes = $attributes.$attr[$i];
			}
			else {
				$attributes = $attributes.", ".$attr[$i];
			}
		}

		$query = "SELECT ".$attributes." FROM ".$database.".".$table;

		return $query;
	}

	function createTempTable($query, $predicate) {
		global $conn;

		$stmt = $conn->prepare("CREATE TEMPORARY TABLE IF NOT EXISTS $predicate AS $query");
		$stmt->execute();
		// var_dump($stmt);
	}

	function createView($query, $predicate) {
		global $conn;

		$stmt = $conn->prepare("CREATE OR REPLACE VIEW $predicate AS $query");
		$stmt->execute();
		// var_dump($stmt);
	}

	function checkingQuery($idb, $facts, $target, $arg) {

		$i = 0;
		$condition = "";
		while ($i<sizeof($target)) {
			if($i == 0) {
				$condition = $condition.$idb.".".$target[$i]." = ".$facts.".".$arg[$i];
			}
			else {
				$condition = $condition." AND ".$idb.".".$target[$i]." = ".$facts.".".$arg[$i];		
			}
			$i++;
		}
		
		$source = "SELECT * FROM $idb";
		$target = "(SELECT * FROM $facts WHERE $condition)";
		// $target = "SELECT * FROM $facts";
		$check = $source." WHERE NOT EXISTS ".$target;
		echo $check."\n";
		return $check;

	}

	//End of functions

	//Main program
	// $query = new Query();
	//$query = parseArgument("orangtua(John,Michael)");

	#condition example 
	// $query = parseRule("max24_sks(13512075,3)");
	$cons = array();
	// $var = $query->getConditions();
	// for ($i=0; $i<sizeof($var); $i++) {
	// 	$j = $i+1; 
	// 	$cons[$i] = $var['argumen_'.$j];
	// }
	// print_r($cons);

	$rules = getRules("BS2A");
	print_r($rules);
	$test = collectRules($rules[0]);
	$head = getHead($rules[0]);
	//print_r($head);
	$queries = ruleToQuery($test, $rules[0], $cons);
	// $test = getRuleBody($query->getPredicate());
	// print_r($test);
	
 	$ref = generateRef($test);
 	getCurrentData("max24_sks");
 	print_r($queries); 
 		
	foreach ($queries as $predicate => $query) {
			createTempTable($query, $predicate);
	}
	checkInstance($rules[0]);

	$conn = null;

?>