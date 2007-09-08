<?php
	class Expense {
		var $ID;

		var $spenderID;

		// array of IDs that have to bear expense excluding spenderID. 
		// 0 is a special ID and is treated as all IDs defined.
		// Received as : delimited list to constructor
		var $accountableIDs = array(); 

		var $expenseDate;
		var $amount;
		var $description;

		var $deleted;

		function Expense($id, $sid, $aid, $expdate, $amt, $desc, $del) {
			// Check duplicate expense ID
			if (findExpenseID($id) != -1) {
				echo "Expense ID ".$id." already instantiated!";
				exit;
			}

			// Check for non-existant spenderID
			if (findPersonID($sid) == -1) {
				echo "Expense ID ".$id." for a non existant spender ID ".$sid;
				exit;
			}

			$this->ID = $id;
			$this->spenderID = $sid;

			$tok = strtok($aid, ":");
			while($tok) {
				array_push($this->accountableIDs, $tok);
				$tok = strtok(":");
			}

			if (count($this->accountableIDs) == 0) {
				echo "No accountable IDs specified.\n";
				exit;
			}

			// Check for non-existant accountableIDs
			for ($i = 0; $i < count($this->accountableIDs); $i++) {
				if (findPersonID($this->accountableIDs[$i]) == -1) {
					echo "Expense ID ".$id." for a non-existant accountable ID ".$this->accountableIDs[$i];
					exit;
				}
			}

			$this->expenseDate = $expdate;
			$this->amount = $amt;
			$this->description = $desc;
			$this->deleted = $del;
		}
	}

	// An exchange between two IDs
	class Transaction {
		var $ID;

		var $fromID;
		var $toID;

		var $expenseList;

		var $amount;

		function Transaction($id, $from, $to) {
			$this->ID = $id;

			$this->fromID = $from;
			$this->toID = $to;

			$this->expenseList = array();

			$this->amount = 0.0;
		}

		// If amount is less than 0 then flip the from and to IDs since the
		// transaction has essentially reversed.
		function flip() {
			if ($amount < 0) {
				$tmp = $this->fromID;
				$this->fromID = $this->toID;
				$this->toID = $tmp;
			}
		}

		// Add the specified Expense object details t this transaction
		function addExpense($id, $amount) {
			$this->amount += $amount;

			array_push($this->expenseList, $id);
		}
	}

	class Person {
		var $ID;
		var $name;

		// Array of expense IDs where this person was the spender
		var $expenseList = array();

		var $totalExpenses;

		function Person($id, $name) {
			if (findPersonID($id) != -1) {
				echo "Person ".$name." with ID ".$id." has duplicate ID!";
				exit;
			}

			$this->ID = $id;
			$this->name = $name;

			$this->totalExpenses = 0.0;
		}

		// Map to expenses that were made by current person
		function mapExpenses() {
			global $expenses;

			for ($i = 0; $i < count($expenses); $i++) {
				if (!$expenses[$i]->deleted)
					if ($expenses[$i]->spenderID == $this->ID) {
						array_push($this->expenseList, &$expenses[$i]);
					}
			}
		}

		// Calculate total expenses
		function calcTotalExpenses() {
			$this->totalExpenses = 0;

			for ($i = 0; $i < count($this->expenseList); $i++) {
				$this->totalExpenses += $this->expenseList[$i]->amount;
			}
		}
	}
?>
