<?php
	include "sql_connect.inc";
	
	function buildRule($predicate) { //build rule body from existed data

		$head = getHead($predicate);
		$bodies = getRuleBody($predicate);
		// $expr = getRuleExpr($predicate);
		// print_r($bodies);
		$predicates = array();

		$p_head = mergeHead($head); 
		// print_r($p_head);
		
		$i=0;
		if(is_array($bodies[0])) {
			foreach ($bodies as $body) {
				$predicates[$i] = mergeBody($body);	
				$i++;
			}//print_r($predicates);
		}
		else {
			$predicates[$i] = mergeBody($bodies);
		}
			
		// print_r($expr);

		$rule = array(); $idx=0;
		foreach ($predicates as $body) { //combine rule head and body
			$i=0; 
			$p_body = "";

			while ($i<sizeof($body)) {
				if ($i == 0) { 
					$p_body = $p_body.$body[$i];
				}
				else {
					$p_body = $p_body.", ".$body[$i];	
				}
				$i++;
			}

			$rule[$idx] = $p_head." :- ".$p_body;
			unset($p_body);
			$idx++;	
		}
		
	 	// print_r($rule);
	 	return $rule;
	}

	function identifyRule($rule) { //split rule head and rule body

		$delimiter1 = '/(\s+)[\s:-]+/';
		$delimiter2 = '/[\s,]+/';
		$temp = preg_split($delimiter1, $rule);
		$temp2 = explode(", ", $temp[1]);
		array_unshift($temp2, $temp[0]);
		print_r($temp2);


		$head = identifyHead($temp2);
		$queryhead = insertHead($head);

		$body = identifyBody($temp2);
		$querybody = insertBody($body);

		$expr = identifyExp($temp2);
		$queryexpr = insertExp($expr);

		// print_r($queryhead);
		foreach ($querybody as $query) {
			$i = 0;
			while($i<sizeof($query)) {
				array_push($queryhead, $query[$i]);
				$i++;
			}
		}

		foreach ($queryexpr as $query) {
			$i = 0;
			while($i<sizeof($query)) {
				array_push($queryhead, $query[$i]);
				$i++;
			}
		}
		// insertData($queryhead);
		// print_r($querybody);
		// print_r($queryexpr);
		return $queryhead;
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

		$ids = array(); $i=0; //print_r($rulebody);
		foreach ($rulebody as $body) {
			$predicate = $body->getPredicate();
			$stmt = $conn->prepare("SELECT id_predikat FROM predikat WHERE nama_predikat = '$predicate'");
			$stmt->execute();
			$id = $stmt->fetch();

			$ids[$i] = $id[0];
			$i++;
		}
		//print_r($ids);
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

	function findParent($nested) {
 
		$parent = "";
		foreach ($nested as $node) {
			if($node->getLeft() == 1) {
				$parent = $node->getArg();
			}			
		}

		return $parent;		
	}

	function identifyExp($body) {
		// print_r($body);
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
		$nested = array();
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
		// print_r($temp);
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

		$result = array(); //print_r($tokenlist);
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
			elseif($tokenlist[$i] == '>') {
				if($tokenlist[$i+1] == '=') {
					$result[$idx] = $tokenlist[$i].$tokenlist[$i+1];
					$i++;
				}
				else {
					$result[$idx] = $tokenlist[$i];
				}
			}
			elseif($tokenlist[$i] == '\'') {
				$token = "'";
				$j = $i+1;
				while ($tokenlist[$j] != '\'') {
					//echo $tokenlist[$j]."\n";
					$token = $token.$tokenlist[$j];
					$j++; 
				}
				// $i = $i+$j;
				
				$result[$idx] = $token."'";
				$i = $j+1;
			}
			elseif(is_numeric($tokenlist[$i])) {
				$token = ""; //echo "Masuk \n";
				// $j = $i+1;
				while ($i<sizeof($tokenlist) && (is_numeric($tokenlist[$i]) || $tokenlist[$i] == "." || $tokenlist[$i] == ",")) {
					//if(!ctype_alpha($tokenlist[$i]) && !isComparator($tokenlist[$i])) { 
						$token = $token.$tokenlist[$i];
						$i++;
					//}
					
				}
				$i--;
				$result[$idx] = $token;
			}
			else {
				$result[$idx] = $tokenlist[$i];
			}

			$i++; $idx++;
		}
		// echo "Merge result \n";
		//print_r($result);
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
			if(ctype_alnum($token) || is_numeric($token) || strpos($token, '\'') !== false || strpos($token, '.') !== false || strpos($token, ',') !== false) {
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
			// echo $token."\n";
		}

		while(!empty($opstack)) {
			$elmt = array_pop($opstack);
			array_unshift($prefixlist, $elmt);
		}
		// print_r($tokenlist);
		// print_r($prefixlist); echo "This is prefix \n";
		return $prefixlist;
	}

	function prefixToInfix($expr_arr) {

		$opstack = array();
		$infixlist = array();
		$temp = array();
		// $tokens = str_split($expr); 
		$tokenlist = array_reverse($expr_arr); //print_r($tokenlist);

		$i=0;
		while ($i<sizeof($tokenlist)) {
			if(ctype_alnum($tokenlist[$i]) || strpos($tokenlist[$i], '\'') !== false || strpos($tokenlist[$i], ',') !== false || strpos($tokenlist[$i], '.') !== false) {
				array_push($opstack, $tokenlist[$i]);
			}
			else {
				$elmt1 = array_pop($opstack);
				$elmt2 = array_pop($opstack);

				if($i == sizeof($tokenlist)-1) {
					$args = $elmt1.$tokenlist[$i].$elmt2;
				}
				else {
					$args = "(".$elmt1.$tokenlist[$i].$elmt2.")";
				}
				// echo $tokenlist[$i]."\n";
				// echo "Pop result ".$args." \n";
				array_push($opstack, $args);
				// if(!empty($opstack)) {
				// 	$elmt2 = array_pop($opstack);

				// 	array_unshift($infixlist, ")");
				// 	array_unshift($infixlist, $elmt2);
				// 	array_unshift($infixlist, $token);
				// 	array_unshift($infixlist, $elmt1);
				// 	array_unshift($infixlist, "(");	
				// }
				// else {
				// 	array_unshift($infixlist, $token);
				// 	array_unshift($infixlist, $elmt1);	
				// }			
			}
			// print_r($opstack);
			// print_r($infixlist);
			$i++;
		}
		while(!empty($opstack)) {
			$elmt = array_pop($opstack);
			array_unshift($infixlist, $elmt);
		}

		// print_r($infixlist); 
		$result = mergeOperator(str_split($infixlist[0]));
		return $result;
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