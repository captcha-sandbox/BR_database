<?php
	
	function getRules($bs) { //get all rules for corresponding business statement
		global $conn;

		$stmt = $conn->prepare("SELECT nama_predikat FROM predikat WHERE br_statement = '$bs'");
		$stmt->execute();

		$rules = array();
		$i = 0;
		while ($res = $stmt->fetch()) {
			$rules[$i] = $res['nama_predikat'];
			$i++;
		}

		return $rules;
	}

	function checkInstance($predicate) {
		global $conn;

		//get reference table for instance checking
		$stmt = $conn->prepare("SELECT `reference` FROM rule_ref r, idb i, predikat p WHERE r.id_rule = i.id_aturan AND i.id_predikat = p.id_predikat AND p.nama_predikat = '$predicate'");
		$stmt->execute();
		$res = $stmt->fetch();
		$reference = $res[0];

		//get attribute to be compared with
		$stmt = $conn->prepare("SELECT target_attr FROM idb i INNER JOIN predikat p ON i.id_predikat = p.id_predikat WHERE p.nama_predikat = '$predicate'");
		$stmt->execute();
		$res = $stmt->fetch();
		$target = $res[0];

		$check = checkingQuery($reference, $predicate, $target);

		$stmt = $conn->prepare($check);
		$stmt->execute();
		$res = $stmt->fetchAll();
		print_r($res);

		$stmt = $conn->prepare("SELECT * FROM max24_sks");
		$stmt->execute();
		$res = $stmt->fetchAll();
		print_r($res);


		$stmt = $conn->prepare("SELECT * FROM last_nr");
		$stmt->execute();
		$res = $stmt->fetchAll();
		print_r($res);
	}

?>