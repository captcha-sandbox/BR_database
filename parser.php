<?php
	include "sql_connect.inc";
	function identifyRule($rule) { //split rule head and rule body

		$delimiter = '/[\s:-]+/';
		$temp = preg_split($delimiter, $rule);
		//print_r($temp);

		$head = identifyHead($temp);
		insertHead($head);
		identifyBody($temp);
	}

	function identifyHead($head) { //split predicate head and argument head

		$rulehead = parseArgument($head[0]);
		print_r($rulehead);
		return $rulehead;
	}

	function insertHead($rulehead) { //insert rule head into database
		global $conn;

		$predicate = $rulehead->getPredicate();
		$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$id = $stmt->fetch();

		if(empty($id)) {
			echo "Predikat belum terdefinisi \n";
		}
		else {
			headQuery($rulehead, $id[0]);
		}
	}

	function identifyBody($body) { //split predicate body and argument body

		$rulebody = array();
		for($i=1; $i<sizeof($body); $i++) {
			if(!isExpression($body[$i])) {
				$rulebody[$i] = parseArgument($body[$i]);
			}
		}
		// print_r($rulebody);
		return $rulebody;
	}

	function isExpression($arg) { //check whether an argument is an expression or not

		$pattern = '/=|<>|>|<|<=|>=/';
		if(preg_match($pattern, $arg)) {
			return true;
		}
		else {
			return false;
		}
	}

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
		// $key = "argumen_";
		// foreach ($temp as $arg) {
		// 	if(isVariable($arg)) {
		// 		$attr[$key.$i] = $arg;
		// 		$i++;
		// 	}
		// 	else {
		// 		$cond[$key.$i] = $arg;
		// 		$i++;
		// 	}
		// }
		foreach ($temp as $arg) {
			if(isVariable($arg)) {
				$attr[$i] = $arg;
				$i++;
			}
			else {
				$cond[$i] = $arg;
				$i++;
			}
		}

		$q->setAttributes($attr);
		$q->setConditions($cond);
		
		if(preg_match('/~/', $predicate[0])) { //negation checking
			$q->setNegasi("TRUE");
			$q->setPredicate(substr($predicate[0], 1));
		}
		else {
			$q->setNegasi("FALSE");
			$q->setPredicate($predicate[0]);	
		}

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

	function infixToPrefix($expr) {

		//operator precedence
	 	$prec = array();
    $prec["*"] = 3; $prec["/"] = 3; $prec["+"] = 2; $prec["-"] = 2; $prec["="] = 1;
    $prec["<>"] = 1; $prec[">="] = 1; $prec["<="] = 1; $prec["<"] = 1; $prec[">"] = 1;
    $prec[")"] = 0;

		$opstack = array();
		$prefixlist = array();
		$tokenlist = array_reverse(str_split($expr));

		foreach ($tokenlist as $token) {
			if(ctype_alnum($token)) {
				array_unshift($prefixlist, $token);
			}
			elseif($token == ')') {
				array_push($opstack, $token);
			}
			elseif($token == '(') {
				$top = array_pop($opstack);
				while ($top != ')') {
					array_unshift($prefixlist, $top);
					$top = array_pop($opstack);
				}
			}
			else {
				while(!empty($opstack) && ($prec[end($opstack)] >= $prec[$token])) {
					$elmt = array_pop($opstack);
					array_unshift($prefixlist, $elmt);
					
				}
				array_push($opstack, $token);
			}
		}

		while(!empty($opstack)) {
			$elmt = array_pop($opstack);
			array_unshift($prefixlist, $elmt);
		}
		// print_r($tokenlist);
		// print_r($prefixlist);
		return $prefixlist;
	}

	function buildNestedElmt($prefix) {

		$stack = array();
		$nested = array();
		$neighbor = 1; // identification for leaf / node

		$i=0;
		for($j=0; $j<sizeof($prefix); $j++) {
			$elmt = new NestedSet();
			// $neighbor++;
			if(ctype_alnum($prefix[$j])) {
				$elmt->setArg($prefix[$j]);
				$elmt->setLeft($neighbor); $neighbor++;
				$elmt->setRight($neighbor);
				
				$nested[$i] = $elmt;
				$i++;

				if(ctype_alnum($prefix[$j-1])) {
					$neighbor++;
					$op = array_pop($stack);
					$op->setRight($neighbor);
					
					$nested[$i] = $op;
					$i++;
				}
			}
			else {
				$elmt->setArg($prefix[$j]);
				$elmt->setLeft($neighbor);
				array_push($stack, $elmt);
			}
			$neighbor++;
			unset($elmt);
		}
		// echo sizeof($stack);
		while(!empty($stack)) {
			$op = array_pop($stack);
			$op->setRight($neighbor);
			
			$nested[$i] = $op;
			$neighbor++; $i++;
		}
		// print_r($nested);
	}
?>