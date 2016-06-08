<?php	


	//Functions
	function getTableRef($bodies) {
		$ref = array();

		$i = 0;
		foreach ($bodies as $body) {
			$current = $body->getPredicate();
			if(!in_array($current, $ref) && !isOperator($current)) { //check if element is already exist in array or a comparator
				if(!isNegation($body)) {
					$ref[$i] = $body->getPredicate();
					$i++;
				}
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
		// $substitution = substituteVar($bodies);

		// foreach ($substitution as $key => $data) {
		// 	$substitution[$key] = substr($data, strpos($data, ".") + 1);
		// }    

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
		// echo $predicate."\n";
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
							if(!isIDB($body->getPredicate())) {
								$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$prev[$body->getContent()];
								//echo $on[$i]."\n";
							}
							else {
								$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$val[$body->getContent()];
							}
						}
						else {
							if(!$isIDB($body->getPredicate())) {	
								$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$prev[$body->getContent()];
							}
							else {
								$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$val[$body->getContent()];
							}
						}
					$i++;
				}
			}
		}
		else {
			foreach ($bodies as $body) {
				if(($body->getPredicate() != $predicate) && (in_array($body->getContent(), $args)) && !isOperator($body->getPredicate())) {
						if(!isIDB($predicate)) {
							if(!isIDB($body->getPredicate())) {
									$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()];
									//echo $on[$i]."\n";
								}
								else {
									$on[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$body->getContent();
								}
							}
						else {
							if(!isIDB($body->getPredicate())) {
									// print_r($substitution);
									$substitution = getEDBAttributes($body->getPredicate(), $bodies);
									$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$substitution[$body->getContent()];
									// echo $res[$body->getContent()]."\n";
								}
								else {
									$on[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$body->getContent();
								}
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
		// print_r($conditions);
		return $conditions;
	}

	function getQueryVal($head, $sub, $cons) {
		global $conn;

		$id = $head[0]->getRuleId();
		$res = array();
		$stmt = $conn->prepare("SELECT `isi_argumen` FROM `argumen_head` WHERE id_rule = $id");
		$stmt->execute();
		
		while($body = $stmt->fetch()) {
			$arg = $body['isi_argumen'];
			$res[$arg] = $sub[$arg];
		}

		$values = array(); $i=0;
		if(!empty($cons)) { //only executed when constant is provided
			foreach ($res as $arg => $attr) {
				if(!empty($cons[$arg])) {
				$values[$i] = $attr." = ".$cons[$arg];
				$i++;
			}
			}
		}
		// print_r($values);
		return $values;
	}

	function getConstantVal($head, $sub, $cons) {
		global $conn;

		$id = $head[0]->getRuleId();
		$res = array();
		$stmt = $conn->prepare("SELECT `isi_argumen` FROM `argumen_head` WHERE id_rule = $id");
		$stmt->execute();
		
		while($body = $stmt->fetch()) {
			$arg = $body['isi_argumen'];
			$res[$arg] = $sub[$arg];
		}

		$values = array(); $i=0;
		if(!empty($cons)) { //only executed when constant is provided
			foreach ($res as $arg => $attr) {
				if(!empty($cons[$arg])) {
				$values[$i] = $arg." = ".$cons[$arg];
				$i++;
			}
			}
		}
		// print_r($values);
		return $values;

	}

	function getNegation($bodies) {
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

		$res = array();
		$i = 0;
		# get attribute name associated to argument(s) name
		$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.id_ref = '$predicate'");
		$stmt->execute();
		while($body = $stmt->fetch()) {

			$res[$args[$i]] = $body['attr_name'];
			$i++;
		}

		$negation = array(); $i=0;
		$neg_arg = array();

		foreach ($bodies as $body) {
			if(($body->getPredicate() != $predicate) && (in_array($body->getContent(), $args)) && !isOperator($body->getPredicate()) && isNegation($body)) {

				$neg_arg[$i] = $body->getPredicate(); //get negated predicate name
				if(!isIDB($predicate)) {
					if(!isIDB($body->getPredicate())) {
							$negation[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$res[$body->getContent()];
							//echo $on[$i]."\n";
						}
						else {
							$negation[$i] = $predicate.".".$res[$body->getContent()]." = ".$body->getPredicate().".".$body->getContent();
						}
					}
				else {
					if(!isIDB($body->getPredicate())) {
							// print_r($substitution);
							$substitution = getEDBAttributes($body->getPredicate(), $bodies);
							$negation[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$substitution[$body->getContent()];
							// echo $res[$body->getContent()]."\n";
						}
						else {
							$negation[$i] = $predicate.".".$body->getContent()." = ".$body->getPredicate().".".$body->getContent();
						}
					}
				$i++;
			}
		}

		$args = mergeNegation($neg_arg, $negation);
		$query = negativeQuery($args);
		return $query;
	}

	function mergeNegation($predicate, $arg) { //merge conditon for negated predicate

		$negation = array();
		$i = 0;

		while($i<sizeof($arg)) {
			$temp = array();
			$neg_predicate = $predicate[$i];
			while ($neg_predicate == $predicate[$i]) {
				array_push($temp, $arg[$i]);
				$i++;
			}
			$negation[$predicate[$i-1]] = $temp;
		}

		// print_r($negation);
		return $negation;
	}

	function negativeQuery($negation) { // generate negative query from argument(s)

		$neg_query = array(); $j=0;
	 	foreach ($negation as $predicate => $arg) {
	 		$i = 0;
	 		$neg_cond = "";
	 		while ($i<sizeof($arg)) {
	 			if($i<1) {
		 			$neg_cond = $neg_cond.$arg[$i];
		 		}
		 		else {
		 			$neg_cond = $neg_cond." AND ".$arg[$i];
		 		}
		 		$i++;
	 		}

	 		$neg_query[$j] = "NOT EXISTS (SELECT * FROM ".$predicate." WHERE ".$neg_cond.")";
	 		$j++;
	 	}
	 	return $neg_query;
	} 

	function generateQuery($predicate, $bodies, $cons) {

		//get head argument
		$head = getHead($predicate);
		$sub = substituteVar($bodies); 

		#query generator
	 	$from = getTableRef($bodies);
	 	$select = getProjection($head, $sub); //print_r($select);
	 	$join = getOnProperties($bodies);
	 	$where = getSelection($bodies);
	 	$negation = getNegation($bodies);
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
	 	if(!empty($cons)) { //only executed when constant is defined
	 		$value = getQueryVal($head, $sub, $cons);
	 		for($i=0; $i<sizeof($value); $i++) {
	 			$constant = $constant." AND ".$value[$i];
	 		}
	 	}
	 	// echo $constant."\n"

	 	$neg_query = ""; 
	 	for($i=0; $i<sizeof($negation); $i++) {
	 		if($i<1) {
	 			$neg_query = $neg_query.$negation[$i];
	 		}
	 		else {
	 			$neg_query = $neg_query." AND ".$negation[$i];
	 		}
	 	}

// WHERE NOT EXISTS (SELECT * FROM nr_lengkap WHERE nr.nim = nr_lengkap.X AND nr.semester = nr_lengkap.Y)
	 		$generate = "SELECT ".$attr." FROM ".$tables." WHERE ".$neg_query." ".$on." ".$condition." ".$constant;	

	 	
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

		$stmt = $conn->prepare("DROP TEMPORARY TABLE IF EXISTS $predicate");
		$stmt->execute();

		$stmt = $conn->prepare("CREATE TEMPORARY TABLE $predicate AS $query");
		$stmt->execute();
		// var_dump($stmt);
	}

	function createView($query, $predicate) {
		global $conn;

		$stmt = $conn->prepare("CREATE OR REPLACE VIEW $predicate AS $query");
		$stmt->execute();
		// var_dump($stmt);
	}

	function checkingQuery($idb, $facts, $arg) {

		$i = 0;
		$condition = "";
		while ($i<sizeof($arg)) {
			if($i == 0) {
				$condition = $condition.$idb.".".$arg[$i]." = ".$facts.".".$arg[$i];
			}
			else {
				$condition = $condition." AND ".$idb.".".$arg[$i]." = ".$facts.".".$arg[$i];		
			}
			$i++;
		}
		
		$source = "SELECT * FROM $idb";
		$target = "(SELECT * FROM $facts WHERE $condition)";
		// $target = "SELECT * FROM $facts";
		$check = $source." WHERE NOT EXISTS ".$target;
		// echo $check."\n";
		return $check;

	}

	function countMatch($idb, $cons) {

		$bodies = collectRules($idb);
		$head = getHead($idb);
		$sub = substituteVar(end($bodies));

		$constant = "";
		$value = getConstantVal($head, $sub, $cons);
 		for($i=0; $i<sizeof($value); $i++) {
 			$constant = $constant." AND ".$value[$i];
 		}

 		$constant = substr($constant, 4);
		$query = "SELECT COUNT(*) FROM ".$idb." WHERE ".$constant;
		echo $query."\n";
		return $query;
	}

	//End of functions

	

?>