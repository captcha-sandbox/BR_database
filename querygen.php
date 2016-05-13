<?php	
	include "sql_connect.inc";
	include "classes.php";

	//Functions
	function parseArgument($arg) {

		$attr = array();
		$cond = array();
		$q = new Query();

		$regex_p = '/^.+?(?=\()/';
		$regex_c = '/\(([^)]+)\)/';
		$delimiter = '/[\s,]+/';

		preg_match($regex_p, $arg, $predicate);
		preg_match($regex_c, $arg, $conditions);
		$temp = preg_split($delimiter, $conditions[1]);

		$i=1;
		$key = "argumen_";
		foreach ($temp as $arg) {
			if(isVariable($arg)) {
				$attr[$key.$i] = $arg;
				$i++;
			}
			else {
				$cond[$key.$i] = $arg;
				$i++;
			}
		}

		$q->setPredicate($predicate[0]);
		$q->setAttributes($attr);
		$q->setConditions($cond);

		//echo($predicate[0]);
		//print_r($temp);
		return $q;
	}

	function parseRule($arg) {

		$attr = array();
		$cond = array();
		$q = new Query();

		$regex_p = '/^.+?(?=\()/';
		$regex_c = '/\(([^)]+)\)/';
		$delimiter = '/[\s,]+/';

		preg_match($regex_p, $arg, $predicate);
		preg_match($regex_c, $arg, $conditions);
		$temp = preg_split($delimiter, $conditions[1]);
		//print_r($temp);
		$i=1;
		$key = "argumen_";
		foreach ($temp as $arg) {
				$cond[$key.$i] = $arg;
				$i++;
		}

		$q->setPredicate($predicate[0]);
		$q->setConditions($cond);

		//echo($predicate[0]);
		//print_r($temp);
		return $q;
	}

	function parseFormula($arg, $substitution) {

		$delimiter = '/[ ]+/';
		$temp = preg_split($delimiter, $arg);

		for($i=0; $i<sizeof($temp); $i++) {
			if(isVariable($temp[$i])) {
				$temp[$i] = $substitution[$temp[$i]];
			}
		}
		// print_r($temp);	
	}

	function isVariable($arg) {
		return ctype_upper($arg);
	}

	function isComparator($arg) {
		if($arg == '=' OR $arg == '<>' OR $arg == '>=' OR $arg == '=<' OR $arg == '>' OR $arg == '<') {
			return true;
		}
		else {
			return false;
		}
	}

	function hasVariant($head) { //check if any rule has more than one statement 
		global $conn;

		//check how many rules that have similar head
		$stmt = $conn->prepare("SELECT COUNT(id_aturan) FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE nama_predikat = '$head'");		
		$stmt->execute();
		$amount = $stmt->fetch();
		//echo $amount[0]."\n";
		if($amount[0] > 1) {
			return true;
		}
		else {
			return false;
		}
	}

	function numVariant($head) { //count how many variations exist for a rule 
		global $conn;

		//count how many rules that have similar head
		$stmt = $conn->prepare("SELECT COUNT(id_aturan) FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE nama_predikat = '$head'");		
		$stmt->execute();
		$amount = $stmt->fetch();

		return $amount[0];
	}

	function isIDB($predicate) { //check whether the predicate is IDB or not
		global $conn;

		$stmt = $conn->prepare("SELECT `kelompok_predikat` FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$result = $stmt->fetch();

		if($result[0] == "IDB") {
			return true;
		}
		else {
			return false;
		}
	}

	function getIDB($rule_id) {
		global $conn;

		$idb = array();
		$stmt = $conn->prepare("SELECT `predikat_edb` FROM `body_idb` b INNER JOIN `predikat` p ON b.predikat_edb = p.nama_predikat WHERE b.id_aturan = $rule_id AND p.kelompok_predikat = 'IDB'");
		$stmt->execute();
		
		$i = 0;
		while($edb = $stmt->fetch()) {
			$idb[$i] = $edb['predikat_edb'];
			$i++;
		}

		return $idb;
	}

	function getIDBList($predicate) {
		global $conn;

		//get head part of idb
		$stmt = $conn->prepare("SELECT id_aturan FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat  WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$rule = $stmt->fetch();
		$rule_id = $rule[0];

		$idb = getIDB($rule_id);
		$list = array();

		array_push($list, $predicate);
		while(!empty($idb)) {
			$elmt = array_pop($idb); //get last element from idb array
			array_push($list, $elmt);
			$stmt = $conn->prepare("SELECT id_aturan FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE nama_predikat = '$elmt'");
			$stmt->execute();
			$id = $stmt->fetch();
			$head = $id[0];
			if(!empty($head)) {// check if array is empty
				$temp = getIDB($head); //get another idb predicate (if any)
				$idb = array_merge($idb, $temp);
			}
		}

		$reverse = array_reverse($list);
		return $reverse;
	}

	function getHead($predicate) {
		global $conn;

		//get head part of idb
		$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$id = $stmt->fetch();
		$head = $id[0];

		//get corresponding id for this head
		$stmt = $conn->prepare("SELECT id_aturan FROM idb WHERE id_predikat = $head");
		$stmt->execute();
		$rule_id = $stmt->fetch(); 
		$rule = $rule_id[0];

		//get head argument(s)
		$res = array();
		$stmt = $conn->prepare("SELECT id_rule, urutan, isi_argumen FROM `argumen_head` WHERE id_rule = $rule");
		$stmt->execute();
	  $i = 0;
		while($head_arg = $stmt->fetch()) {
			$rh = new RuleHead();
			$rh->setPredicate($predicate);
			$rh->setArgOrder($head_arg['urutan']);
			$rh->setContent($head_arg['isi_argumen']);

			$res[$i] = $rh;
			$i++;
		} 

		return $res;
	}

	function getRuleBody($predicate) {
		global $conn;

		//get head part of idb
		$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$id = $stmt->fetch();
		$head = $id[0];

		$stmt = $conn->prepare("SELECT id_aturan FROM idb WHERE id_predikat = $head");
		$stmt->execute();
		
		$res = array();
		if(!hasVariant($predicate)) { //only one rule has this head
			$rule_id = $stmt->fetch(); 
			$rule = $rule_id[0];
			$res = getBody($rule);
			// $test = getIDBList($predicate); echo "IDB List \n"; print_r($test);		
		}
		else { //some rules have similar head
			$i = 0;
			while ($rule_id = $stmt->fetch()) {
				$rules[$i] = $rule_id['id_aturan'];
				$i++;
			}

			$i = 0;
			foreach ($rules as $rule) {
				$res[$i] = getBody($rule);
				$i++;
			}
		}
		
		return $res; 
	}

	function getBody($rule) {
		global $conn;

		$res = array();
		$stmt = $conn->prepare("SELECT predikat_edb, is_negasi, a.urutan_body, urutan_argumen, isi_argumen FROM `body_idb` b INNER JOIN `argumen_body` a ON b.urutan_body = a.urutan_body WHERE a.id_aturan = $rule AND b.id_aturan = $rule");
		$stmt->execute();
		$i = 0;
		while($body = $stmt->fetch()) {
			$rb = new RuleBody();
			$rb->setPredicate($body['predikat_edb']);
			$rb->setNegasi($body['is_negasi']);
			$rb->setBodyOrder($body['urutan_body']);
			$rb->setArgOrder($body['urutan_argumen']);
			$rb->setContent($body['isi_argumen']);

			$res[$i] = $rb;
			$i++;
		}
		return $res;
	}

	function mergeBody($bodies, $cons) {
		global $conn;  //print_r($bodies)."\n";

		$predicate = "";
		$predicates = array();

		$i = 0; $j = 0;
		$obj = "";
		while ($i<sizeof($bodies)) {
			$p = $bodies[$i]->getPredicate();
			$q = "";
			$constant = "";

			if($i<sizeof($bodies)-1) {
				$q = $bodies[$i+1]->getPredicate();
			}

			if(!isComparator($p)) {
				$constant = $cons[$bodies[$i]->getArgOrder()-1];
			}
			else {
				$constant = $bodies[$i]->getContent();
			}

			//if(!isComparator($p)) {
				if(strcmp($obj, $p) != 0) {
					$obj = $p;
					$predicate = $predicate.$obj."(".$constant;
					if(strcmp($p, $q) != 0) {// check if there is only one argument
						$predicate = $predicate.")";
						$predicates[$j] = $predicate;
						$j++;
						$predicate = "";	
					}
				}
				else {
					//echo $p." ".$q."\n";
					$predicate = $predicate.",".$constant;
					if(strcmp($p, $q) != 0) {// check if there is no more argument
						$predicate = $predicate.")";
						$predicates[$j] = $predicate;
						$j++;
						$predicate = "";	
					}
				}
			$i++;
		}
	
		//print_r($predicates);
		return $predicates;
	}

	function sortBody($bodies) {
		for($i=1; $i<sizeof($bodies); $i++) {
			$key = $bodies[$i];
			$j = $i-1;
			while (($j>=0) && ((($key->getBodyOrder() < $bodies[$j]->getBodyOrder()) || (($key->getBodyOrder() <= $bodies[$j]->getBodyOrder()) && ($key->getArgOrder() < $bodies[$j]->getArgOrder()))))) {
				$bodies[$j+1] = $bodies[$j];
				$j--;
			}
			$params[$j+1] = $key;
			//print_r($bodies);
		}
		return $bodies;
	}

	function combineResult($truthval) {

		$bool = "False";
		for ($i=0; $i<sizeof($truthval)-1; $i++) {
			if ($truthval[$i] || $truthval[$i+1]) {
				$bool = "True";
			}
		}
		return $bool;
	}

	function substituteVar($bodies) {
		global $conn;

		$known = array(); //array of variable that is already substituted
		$substitution = array(); //array of constant which substitute associated variable
		$i = 0;
		foreach ($bodies as $body) {
			if(!in_array($body->getContent(), $known) && isVariable($body->getContent())) {
				$known[$i] = $body->getContent();
				$order = $body->getArgOrder();
				$predicate = $body->getPredicate();

				if(isIDB($body->getPredicate())) { //get reference for idb if any
					$stmt = $conn->prepare("SELECT `id_predikat` FROM predikat WHERE nama_predikat = '$predicate'");
					$stmt->execute();
					$res = $stmt->fetch();
					$id = $res[0];

					$stmt = $conn->prepare("SELECT `reference` FROM predicate_ref WHERE id_predikat = $id");
					$stmt->execute();
					$res = $stmt->fetch();
					if(!empty($res[0])) {
						$table = $res[0];
						$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.table_name = '$table' AND a.order = $order");
						$stmt->execute();
						$result = $stmt->fetch();
						
						$substitution[$body->getContent()] = $predicate.".".$result[0]; //get substitution for variable(s)
					}
				}
				else {
					$stmt = $conn->prepare("SELECT `attr_name` FROM `reference` b INNER JOIN `ref_attribute` a ON b.id_ref = a.id_ref WHERE b.table_name = '$predicate' AND a.order = $order");
					$stmt->execute();
					$result = $stmt->fetch();
					
					$substitution[$body->getContent()] = $predicate.".".$result[0]; //get substitution for variable(s)
				}
				$i++;
			}
		}
		print_r($substitution);
		return $substitution;
	}

	function assignConstant($predicate, $substitution, $cons) {
		
		$head = getHead($predicate);
		// print_r($head);
		for($i=0; $i<sizeof($cons); $i++) {
			if(!isVariable($cons[$i])) {
				$substitution[$head[$i]->getContent()] = $cons[$i];
			}
		}
		//echo "After assignment \n";
		//print_r($substitution);
		return $substitution;
	}

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
				$i++;
			}
		}
		return $on;
	}

	function getSelection($bodies) {

		$substitution = substituteVar($bodies); //get variable substitute
		$conditions = array(); //argument(s) for selection
		parseFormula("Y + 1", $substitution);
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

	 	$generate = "SELECT ".$attr." FROM ".$tables." WHERE ".$on." ".$condition." ".$constant;
	 	echo ($generate)."\n";

	 	return $generate;
	}

	function collectRules($predicate) {

		$idb = getIDBList($predicate); //get every idb which involve in corresponding rule
		print_r($idb);
		$test = array();
		for ($i=0; $i<sizeof($idb); $i++) {
			$idx = sizeof($test);
			if(hasVariant($idb[$i])) {
				$temp = getRuleBody($idb[$i]);
				foreach ($temp as $body) {
				 	$test[$idx] = sortBody($body);
				 	$idx++;
				 } 
			}
			else { 
				$test[$idx] = sortBody(getRuleBody($idb[$i])); //rule construction
			}
		}
		// print_r($test);
		return $test;
	}

	function ruleToQuery($bodies, $predicate, $cons) {

		$idb = getIDBList($predicate);
		$idx = 0;
		$queries = array();
		echo "This is bodies array ".sizeof($bodies)."\n";
		$i = 0;
		while($i<sizeof($bodies)) {
			echo $i."iterasi \n";
			if(hasVariant($idb[$idx])) {
				$union = "";
				$predicate = $idb[$idx];
				for($j=$i; $j<=numVariant($predicate)+$i-1; $j++) {
					echo $j." ".numVariant($predicate)."\n";
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

	function createTempTable($query, $predicate) {
		global $conn;

		$stmt = $conn->prepare("CREATE TEMPORARY TABLE IF NOT EXISTS $predicate AS ($query)");
		//$stmt->execute();
		// var_dump($stmt);
		// $stmt = $conn->prepare("SELECT * FROM `last_nr1`");
		// $stmt->execute();
		// $res = $stmt->fetch();
		
		// print_r($res);
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
	$test = collectRules($query->getPredicate());
	$queries = ruleToQuery($test, $query->getPredicate(), $cons);
	//$test = getRuleBody($query->getPredicate());
	//print_r($test);
	
 	
 	print_r($queries); 	
	foreach ($queries as $predicate => $query) {
		createTempTable($query, $predicate);
	}
	
	$conn = null;

?>