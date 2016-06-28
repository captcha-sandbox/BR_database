<?php
	include "sql_connect.inc";
	function identifyRule($rule) { //split rule head and rule body

		$delimiter = '/[\s:-]+/';
		$temp = preg_split($delimiter, $rule);
		// print_r($temp);

		$head = identifyHead($temp);
		$queryhead = insertHead($head);

		$body = identifyBody($temp);
		$querybody = insertBody($body);

		$expr = identifyExp($temp);
		$queryexpr = insertExp($expr);

		print_r($queryhead);
		insertData($queryhead);
		print_r($querybody);
		print_r($queryexpr);
	}

	function identifyHead($head) { //split predicate head and argument head

		$rulehead = parseArgument($head[0]);
		// print_r($rulehead);
		return $rulehead;
	}

	function insertHead($rulehead) { //insert rule head into database
		global $conn;

		$predicate = $rulehead->getPredicate();
		$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
		$stmt->execute();
		$id = $stmt->fetch();

		$query = "";
		if(empty($id)) {
			echo "Predikat belum terdefinisi \n";
		}
		else {
			$query = headQuery($rulehead, $id[0]);
		}

		// print_r($query);
		return $query;
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

	function insertBody($rulebody) { //insert rule body into database
		global $conn;

		$ids = array(); $i=0;
		foreach ($rulebody as $body) {
			$predicate = $body->getPredicate();
			$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
			$stmt->execute();
			$id = $stmt->fetch();

			$ids[$i] = $id[0];
			$i++;
		}

		$queries = array(); $idx=0;
		for($j=0; $j<sizeof($ids); $j++) {
			if(empty($ids[$j])) {
				echo "Predikat belum terdefinisi \n";
			}
			else {
				$order = $j+1;
				$queries[$idx] = bodyQuery($rulebody[$order], $ids[$j], $order);
				$idx++;
			}	
		}

		// print_r($queries);
		return $queries;
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

	function identifyExp($body) {

		$expression = array();
		for($i=1; $i<sizeof($body); $i++) {
			if(isExpression($body[$i])) {
				
				if(preg_match('/,/', $body[$i])) { //ommit comma behind expression
					$fix = str_replace(",", "", $body[$i]);
					$expression[$i] = infixToPrefix($fix);
				}
				else {
					$expression[$i] = infixToPrefix($body[$i]);
				}
				
			}
		}
		// print_r($expression);
		return $expression;
	}

	function insertExp($expr) { //insert expression into database

		$i=0;
		foreach ($expr as $arg) {
			$nested[$i] = buildNestedElmt($arg);
			$i++;
		}

		$queries = array(); $idx=0;
		$order = key($expr);
		for($j=0; $j<sizeof($nested); $j++) {
			$queries[$idx] = exprQuery($nested[$j], $order);
			$idx++; $order++;
		}	
		
		// print_r($queries);
		return $queries;
	}

	function insertData($queries) {
		global $conn;

		foreach ($queries as $query) {
			$stmt = $conn->prepare($query);
			$stmt->execute();
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

	function mergeOperator($tokenlist) {

		$result = array();
		$i=0; $idx = 0;
		while ($i<sizeof($tokenlist)) {
			if($tokenlist[$i] == '<') {
				if($tokenlist[$i+1] == '>' || $tokenlist[$i+1] == '=') {
					$result[$idx] = $tokenlist[$i].$tokenlist[$i+1];
					$i++;
				}
				else {
					$result[$idx] = $tokenlist[$i];
				}
			}
			elseif ($tokenlist[$i] == '>') {
				if($tokenlist[$i+1] == '=') {
					$result[$idx] = $tokenlist[$i].$tokenlist[$i+1];
					$i++;
				}
				else {
					$result[$idx] = $tokenlist[$i];
				}
			}
			else {
				$result[$idx] = $tokenlist[$i];
			}

			$i++; $idx++;
		}
		// echo "Merge result \n";
		// print_r($result);
		return $result;
	}

	function infixToPrefix($expr) {

		//operator precedence
	 	$prec = array();
    $prec["*"] = 3; $prec["/"] = 3; $prec["+"] = 2; $prec["-"] = 2; $prec["="] = 1;
    $prec["<>"] = 1; $prec[">="] = 1; $prec["<="] = 1; $prec["<"] = 1; $prec[">"] = 1;
    $prec[")"] = 0;

		$opstack = array();
		$prefixlist = array();
		$tokens = str_split($expr);
		$tokenlist = array_reverse(mergeOperator($tokens));
		
		// $tokenlist = array_reverse(explode(" ", $expr));

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
		print_r($tokenlist);
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
		return $nested;
	}
?>