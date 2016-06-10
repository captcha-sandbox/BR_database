<?php
	
	include "sql_connect.inc";
	include "classes.php";
	include "rule_functions.php";
	include "checking_functions.php";
	include "querygen.php";

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

	$rules = array(); $i=0;
	$statements = getStatement("PA0404");
	print_r($statements);
	foreach ($statements as $rule) {
		$rules[$i] = getRules($rule);
		$i++;
	}

	// print_r($rules);
	$result = prepareChecking("max24_sks");
	// writeReport($result, "BS2A");
	// checkInstance2("max24_sks");

	// $queries = array(); $j=0;
	// foreach ($rules as $rule) {
	// 	$test = collectRules($rule);
	// 	$temp = getRuleBody($rule);
	//  	$queries[$j] = ruleToQuery($test, $rule, $cons);

	// 	$j++;
	// }

	// $test = collectRules("last_nr2");
	// print_r($test);
	// $head = getHead($rules[0]);

	// $queries = ruleToQuery($test, "last_nr2", $cons);
	// $test = getRuleBody($query->getPredicate());

 	// print_r($queries); 

	// $idb = getIDBList("max24_sks");

	// while($i<sizeof($queries)) {
	// 	foreach ($queries as $predicate => $query) {
	// 		createTempTable($query, $predicate);
	// 	}
	// }

	// $i = 0;
	// while($i<sizeof($queries)) {
	// 	foreach ($queries[$i] as $predicate => $query) {
	// 		createTempTable($query, $predicate);
	// 		$i++;
	// 	}
	// }
	// $cons = checkInstance("max24_sks");
	// print_r($cons);

	$conn = null;

?>