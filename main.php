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

	function categorizeQuery($query) {
		$variable = sizeof($query->getAttributes());
		$constant = sizeof($query->getConditions());
		$type = 1;

		if(($constant>0) && ($variable==0)) {
			$type = 2;	
		}
		elseif(($constant==0) && ($variable>0)) {
			$type = 3;
		}

		return $type;
	}

	function evalFact($query) {
		global $conn;
		//determine query type
		$category = categorizeQuery($query);
		$retrieve = "";
		if (!isComparator($query->getPredicate())) { //check whether the predicate is comparoator or not
			if($category == 1) {
				$retrieve = evalType1($query);
			}
			else if($category == 2) {
				$retrieve = evalType2($query);
			}
			else {
				$retrieve = evalType3($query);
			}
		
			//evaluate query
			$args = $query->getAttributes();
			$stmt = $conn->prepare($retrieve);
			//echo $retrieve."\n";
			$stmt->execute();
			//print_r($args);
			while ($result = $stmt->fetch()) {
				if(sizeof($args) == 0) {
					if($result[0] > 0) {
						return "True";
					}
					else {
						return "False";
					}
				}
				else {
					// foreach ($args as $key) {
					// 	echo $result[$key]." ";
					// }
					return $result[0];
					echo "\n";
				}
			}
		}
	}

	function evalRule($query) {
		global $conn;

		//determine query type
		$category = categorizeQuery($query);
		$retrieve = "";
	}

	function evalType1($query) {
		//select argument_n as variable from predikat where argument_n = konstanta
		$select = ""; $i=1;
		foreach ($query->getAttributes() as $key => $value) {
			if(sizeof($query->getAttributes())>1) {
				if ($i == sizeof($query->getAttributes())) {
					$select = $select.$key." ".$value;
				}
				else {
					$select = $select.$key." ".$value.", ";
				}
				$i++;
			}
			else {
				$select = $select.$key." ".$value;	
			}
		}

		$from = $query->getPredicate();

		$where = ""; $j=1;
		foreach ($query->getConditions() as $key => $value) {
			if(sizeof($query->getConditions())>1) {
				if ($j == sizeof($query->getConditions())) {
					$where = $where." ".$key." = '".$value."'";
				}
				else {
					$where = $where." ".$key." = '".$value."' AND";
				}
				$j++;
			}
			else {
				$where = $where.$key." = '".$value."'";	
			}
		}

		$retrieve = "SELECT ".$select." FROM ".$from." WHERE ".$where;
		//echo $retrieve;
		return $retrieve;
	}

	function evalType2($query) {
		//select count(*) from predicate where argument = constant
		$from = $query->getPredicate();

		$where = ""; $j=1;
		foreach ($query->getConditions() as $key => $value) {
			if(sizeof($query->getConditions())>1) {
				if ($j == sizeof($query->getConditions())) {
					$where = $where." ".$key." = '".$value."'";
				}
				else {
					$where = $where." ".$key." = '".$value."' AND";
				}
				$j++;
			}
			else {
				$where = $where.$key." = '".$value."'";	
			}
		}

		$retrieve = "SELECT COUNT(*) FROM ".$from." WHERE ".$where;
		//echo $retrieve;
		return $retrieve;
	}

	function evalType3($query) {
		//select argumen_n as variable from predicate
		$select = ""; $i=1;
		foreach ($query->getAttributes() as $key => $value) {
			if(sizeof($query->getAttributes())>1) {
				if ($i == sizeof($query->getAttributes())) {
					$select = $select.$key." ".$value;
				}
				else {
					$select = $select.$key." ".$value.", ";
				}
				$i++;
			}
			else {
				$select = $select.$key." ".$value;	
			}
		}

		$from = $query->getPredicate();
		
		$retrieve = "SELECT ".$select." FROM ".$from;
		//echo $retrieve;
		return $retrieve;	
	}

	function fetchValue($query) {
		global $conn;
		$retrieve = getPredicateVal($query);

		//evaluate query
		$stmt = $conn->prepare($retrieve);
		$stmt->execute();
		//print_r($args);
		while ($result = $stmt->fetch()) {
			echo $result[0]."\n";
		}
	}

	function getRuleBody($predicate) {
		global $conn;

		$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$id = $stmt->fetch();
		$head = $id[0];

		$stmt = $conn->prepare("SELECT id_aturan FROM idb WHERE id_predikat = $head");
		$stmt->execute();
		$rule_id = $stmt->fetch();
		$rule = $rule_id[0];

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

			// if($bodies[$i]->getContent() == 'X') {
			// 	$constant = $cons[0];
			// }
			// else {
			// 	$constant = $cons[1];
			// }
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
				}
				else {
					//echo $p." ".$q."\n";
					$predicate = $predicate.",".$constant;
					if(strcmp($p, $q) != 0) {
						$predicate = $predicate.")";
						$predicates[$j] = $predicate;
						$j++;
						$predicate = "";	
					}
				}
			//}
			// else {
				// if(strcmp($obj, $p) != 0) {
				// 	$obj = $p;
				// 	$predicate = $constant." ".$obj;
				// }
				// else {
				// 	//echo $p." ".$q."\n";
				// 	$predicate = $predicate." ".$constant;
				// 	if(strcmp($p, $q) != 0) {
				// 		$predicates[$j] = $predicate;
				// 		$j++;
				// 		$predicate = "";	
				// 	}
			//	}
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
	//End of functions

	//Main program
	$query = new Query();
	//$query = parseArgument("orangtua(John,Michael)"); 
	$query = parseRule("max24_sks(13512075,2,Z)");
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

	$test = sortBody(getRuleBody($query->getPredicate()));
	//print_r($test);
	
 	#deductive
	$rulebody = mergeBody($test, $cons);
	print_r($rulebody);
	$values = array();
	$idx = 0;
	foreach($rulebody as $rule) {
		$args = parseArgument($rule);
		$decision = evalFact($args);

		//echo $bool;
		if($decision == "True") {
			$values[$idx] = true;
		}
		else {
			echo $decision."\n";
			$values[$idx] = false;	
		}
		$idx++;

	}

	//print_r($values);
	$check = combineResult($values);
	echo $check."\n";

	//$query = parseArgument("nr(13512075,1,Z)");
	
	$conn = null;

?>