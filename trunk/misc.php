<?php
	// Sort $array object array based on ID
	function sortArray($array) {
		for ($i = 0; $i < count($array); $i++) {
			for ($j = $i; $j < count($array); $j++) {
				if ($array[$i]->ID > $array[$j]->ID) {
					$tmp = $array[$i];
					$array[$i] = $array[$j];
					$array[$j] = $tmp;
				}
			}
		}
	}

	// Sort Expense array by dates
	function sortExpensesDate() {
		global $expenses;

		for ($i = 0; $i < count($expenses); $i++)  {
			for ($j = $i; $j < count($expenses); $j++)  {
				if ($expenses[$i]->expenseDate > $expenses[$j]->expenseDate) {
					$tmp = $expenses[$i];
					$expenses[$i] = $expenses[$j];
					$expenses[$j] = $tmp;
				}
			}
		}
	}

	// Sort Expense array by SpenderID then date
	function sortExpensesSpender() {
		global $expenses;

		for ($i = 0; $i < count($expenses); $i++) {
			for ($j = $i; $j < count($expenses); $j++)  {
				if ($expenses[$i]->spenderID > $expenses[$j]->spenderID) {
					$tmp = $expenses[$i];
					$expenses[$i] = $expenses[$j];
					$expenses[$j] = $tmp;
				} else if ($expenses[$i]->spenderID == $expenses[$j]->spenderID) {
					if ($expenses[$i]->expenseDate > $expenses[$j]->expenseDate) {
						$tmp = $expenses[$i];
						$expenses[$i] = $expenses[$j];
						$expenses[$j] = $tmp;
					}
				}
			}
		}
	}

	// Sort Expense array by Amount then date
	function sortExpensesAmount() {
		global $expenses;

		for ($i = 0; $i < count($expenses); $i++) {
			for ($j = $i; $j < count($expenses); $j++)  {
				if ($expenses[$i]->amount > $expenses[$j]->amount) {
					$tmp = $expenses[$i];
					$expenses[$i] = $expenses[$j];
					$expenses[$j] = $tmp;
				} else if ($expenses[$i]->amount == $expenses[$j]->amount) {
					if ($expenses[$i]->expenseDate > $expenses[$j]->expenseDate) {
						$tmp = $expenses[$i];
						$expenses[$i] = $expenses[$j];
						$expenses[$j] = $tmp;
					}
				}
			}
		}
	}
	// Sort Expense array by Description then date
	function sortExpensesDescription() {
		global $expenses;

		for ($i = 0; $i < count($expenses); $i++) {
			for ($j = $i; $j < count($expenses); $j++)  {
				if (strcmp($expenses[$i]->description, $expenses[$j]->description) > 0) {
					$tmp = $expenses[$i];
					$expenses[$i] = $expenses[$j];
					$expenses[$j] = $tmp;
				} else if (strcmp($expenses[$i]->description, $expenses[$j]->description) == 0) {
					if ($expenses[$i]->expenseDate > $expenses[$j]->expenseDate) {
						$tmp = $expenses[$i];
						$expenses[$i] = $expenses[$j];
						$expenses[$j] = $tmp;
					}
				}
			}
		}
	}

	// Renumber $array object array IDs
	function renumber($array) {
		for ($i = 0; $i < count($array); $i++) {
			$array[$i]->ID = $i + 1;
		}
	}

	// Find a transaction between spender and accountable IDs
	function findTransaction($accID, $spendID) {
		global $transactions;

		for ($i = 0; $i < count($transactions); $i++) {
			if (	($transactions[$i]->fromID == $accID) 
			     && ($transactions[$i]->toID == $spendID)) {
				return $i;
			}
		}
		return -1;
	}

	// Find a transaction where toID is specified ID and type specifies fromID or toID
	function findTransactionByID($id, $type) {
		global $transactions;

		for ($i = 0; $i < count($transactions); $i++) {
			if ($type == "FROM") {
				if ($transactions[$i]->fromID == $id) {
					return $i;
				}
			} else if ($type == "TO") {
				if ($transactions[$i]->toID == $id) {
					return $i;
				}
			}
		}

		return -1;
	}

	// Find an expense ID in $expenses. Return offset if exists or -1.
	function findExpenseID($id) {
		global $expenses;

		for ($i = 0; $i < count($expenses); $i++) {
			if ($expenses[$i]->ID == $id) {
				return $i;
			}
		}

		return -1;
	}

	// Find a person ID in $persons. Return offset if exists or -1.
	function findPersonID($id) {
		global $persons;

		for ($i = 0; $i < count($persons); $i++) {
			if ($persons[$i]->ID == $id) {
				return $i;
			}
		}

		return -1;
	}

	// Find a person Name in $persons. Return offset if exists or -1.
	function findPersonName($name) {
		global $persons;
		
		for ($i = 0; $i < count($persons); $i++) {
			if ($persons[$i]->name == $name) {
				return $i;
			}
		}

		return -1;
	}

	// Return person's name based on ID
	function getPersonName($id) {
		global $persons;

		for ($i = 0; $i < count($persons); $i++) {
			if ($persons[$i]->ID == $id) {
				return $persons[$i]->name;
			}
		}

		return "BAD";
	}

	// Delete an ID in an array by moving it to the end and then using array_pop()
	function deleteArrayID($array, $id) {
		$temp = array();

		for ($i = 0; $i < count($array); $i++) {
			if ($i != $id) {
				array_push($temp, $array[$i]);
			}
		}

		$array = $temp;
	}

	// Return accountable IDs specified as a string of names
	function accountableToString($acc) {
		for ($i = 0; $i < count($acc); $i++) {
			if (($name = getPersonName($acc[$i])) != -1) {
				$str .= $name;
			} else {
				$str .= $acc[$i];
			}

			if ($i != count($acc)-1) {
				$str .= ", ";
			}
		}

		return $str;
	}
?>
