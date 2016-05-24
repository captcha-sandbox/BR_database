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
			if(!in_array($current, $ref) && !isComparator($current)) { //check if element is already exist in array or a comparator
				$ref[$i] = $body->getPredicate();
				$i++;
			}
		}

		return $ref;
	}

	function getProjection($bodies) {
		global $conn;

		$predicate = $bodies[0]->getPredicate();
		$res = array();
		$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.table_name = '$predicate'");
		$stmt->execute();
		$i = 0;
		while($body = $stmt->fetch()) {

			$res[$i] = $predicate.".".$body['attr_name']." AS ".$body['attr_name'];
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

		$res = array();
		$i = 0;
		# get attribute name associated to argument(s) name
		$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.table_name = '$predicate'");
		$stmt->execute();
		while($body = $stmt->fetch()) {

			$res[$args[$i]] = $body['attr_name'];
			$i++;
		}
		
		$on = array();
		# get another predicate for comparator
		$i = 0;
		foreach ($bodies as $body) {
			if(($body->getPredicate() != $predicate) && (in_array($body->getContent(), $args))) {
				//echo $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()]."\n";
				$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()];
				//echo $on[$i]."\n";
				$i++;
			}
		}
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
		// print_r($conditions);
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

		#query generator
	 	$from = getTableRef($bodies);
	 	$select = getProjection($bodies);
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
	 		$condition = $condition." AND ".$where[$i];
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
		// $stmt = $conn->prepare("SELECT * FROM `last_nr1`");
		// $stmt->execute();
		// $res = $stmt->fetch();
		
		// print_r($res);
	}

	function createView() {

	}

	function checkingQuery($idb, $facts, $target) {

		$condition = $idb.".".$target." = ".$facts.".".$target;
		$source = "SELECT * FROM $idb";
		$target = "(SELECT * FROM $facts WHERE $condition)";
		$check = $source." WHERE NOT EXISTS ".$target;

		return $check;

	}

	//End of functions

	//Main program
	$query = new Query();
	//$query = parseArgument("orangtua(John,Michael)"); 
	$query = parseRule("max24_sks(13512075,3)");
	$cons = array();
	$var = $query->getConditions();
	for ($i=0; $i<sizeof($var); $i++) {
		$j = $i+1; 
		$cons[$i] = $var['argumen_'.$j];
	}
	print_r($cons);
	/*
	//echo $query->getPredicate()."\n";
	//echo categorizeQuery($query);
	evalType3($query);
	evalFact($query); */
	$rules = getRules("BS2A");
	print_r($rules);
	$test = collectRules($rules[0]);
	$head = getHead($rules[0]);
	//print_r($head);
	$queries = ruleToQuery($test, $rules[0], $cons);
	//$test = getRuleBody($query->getPredicate());
	//print_r($test);
	
 	$ref = refToQuery(getReference("nr"));
 	getCurrentData("max24_sks");
 	print_r($queries); 
 		
	foreach ($queries as $predicate => $query) {
		createTempTable($query, $predicate);
	}
	checkInstance($rules[0]);
	
	$conn = null;

?>