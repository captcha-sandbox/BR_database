<?php
	
	/**
	* 
	*/
	class Query
	{
		var $predicate;
		var $attributes;
		var $conditions;

		function getPredicate() {
			return $this->predicate;
		}

		function setPredicate($arg) {
			$this->predicate = $arg;
		}

		function getAttribute($index) {
			return $this->attributes[$index];
		}

		function getAttributes() {
			return $this->attributes;
		}

		function setAttributes($attr) {
			$this->attributes = $attr;
		}

		function getCondition($index) {
			return $this->conditions[$index];
		}

		function getConditions() {
			return $this->conditions;
		}

		function setConditions($cond) {
			$this->conditions = $cond;	
		}
	}

	/**
	* 
	*/
	class Reference
	{
		var $database;
		var $tablename;
		var $attributes;

		function getDatabase() {
			return $this->database;
		}

		function getTablename() {
			return $this->tablename;
		}

		function getAttributes() {
			return $this->attributes;
		}

		function setDatabase($db) {
			$this->database = $db;
		}

		function setTablename($name) {
			$this->tablename = $name;
		}

		function setAttributes($attr) {
			$this->attributes = $attr;
		}
	}

	/**
	* 
	*/
	class RuleHead
	{
		
		var $predicate;
		var $arg_order;
		var $content;

		function getPredicate() {
			return $this->predicate;
		}

		function setPredicate($pred) {
			$this->predicate = $pred;
		}

		function getArgOrder() {
			return $this->arg_order;
		}

		function setArgOrder($order) {
			$this->arg_order = $order;
		}

		function getContent() {
			return $this->content;
		}

		function setContent($content) {
			$this->content = $content;
		}
	}
	/**
	* 
	*/
	class RuleBody
	{
		var $predicate;
		var $negasi;
		var $body_order;
		var $arg_order;
		var $content;

		function getPredicate() {
			return $this->predicate;
		}

		function setPredicate($pred) {
			$this->predicate = $pred;
		}

		function isNegasi() {
			return $this->negasi;
		}

		function setNegasi($bool) {
			$this->negasi = $bool;
		}

		function getBodyOrder() {
			return $this->body_order;
		}

		function setBodyOrder($order) {
			$this->body_order = $order;
		}

		function getArgOrder() {
			return $this->arg_order;
		}

		function setArgOrder($order) {
			$this->arg_order = $order;
		}

		function getContent() {
			return $this->content;
		}

		function setContent($content) {
			$this->content = $content;
		}
	}
?>