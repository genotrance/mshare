<?php
	// Function to add a person
	function addPerson($name) {
		global $persons;

		$person = new Person(count($persons)+1, $name);

		array_push($persons, $person);

		echo "Added person '$name'.\n\n";
	}

	// Add an expense
	function addExpense($spender, $accountable, $date, $amount, $desc) {
		global $expenses;
		global $persons;

		$acc = "";

		// Convert accountable Names to "ID1:ID2.."
		for ($i = 0; $i < count($accountable); $i++) {
			$id = $persons[findPersonName($accountable[$i])]->ID;
			$acc .= "$id";

			if ($i != count($accountable)-1) {
				$acc .= ":";
			}
		}

		if ($date[0] == -1 || $date[1] == -1 || $date[2] == -1) {
			echo "Invalid date mm=$date[0], dd=$date[1], yyyy=$date[2]\n";
			exit;
		}
		$expdate = mktime(0, 0, 0, $date[0], $date[1], $date[2]);

		if (!$amount) {
			echo "Please specify an amount.\n";
			exit;
		}

		$expense = new Expense(count($expenses)+1, $spender, $acc, $expdate, $amount, $desc, 0);

		array_push($expenses, $expense);

		echo "Added expense with ID ".count($expense).".\n\n";
	}

	// Delete an expense based on Expense ID
	function deleteExpense($expID, $perm) {
		global $expenses;

		$eid = findExpenseID($expID);
		if ($eid != -1) {
			if ($perm) deleteArrayID(&$expenses, $eid);
			else $expenses[$eid]->deleted = 1;
		} else {
			echo "Invalid expense ID ".$expID;
			exit;
		}

		echo "Expense with ID $expID deleted";
		if ($perm) echo " permanently.\n";
		else echo ".\n\n";
	}

	// Delete a person
	// Delete all related expenses as well
	function deletePerson($name) {
		global $persons;
		global $expenses;

		$delexpense = array(); // array of expense IDs to delete

		if (($pid = findPersonName($name)) != -1) {
			// Delete all expenses made by this person
			for ($i = 0; $i < count($persons[$pid]->expenseList); $i++) {
				deleteExpense($persons[$pid]->expenseList[$i]->ID, 1);
			}

			// Remove person from all accountable expenses
			for ($i = 0; $i < count($expenses); $i++) {
				for ($j = 0; $j < count($expenses[$i]->accountableIDs); $j++) {
					if ($expenses[$i]->accountableIDs[$j] == $persons[$pid]->ID) {
						deleteArrayID(&$expenses[$i]->accountableIDs, $j);
						if (count($expenses[$i]->accountableIDs) == 0) {
							array_push($delexpense, $expenses[$i]->ID);
						} else sortArray(&$expenses[$i]->accountableIDs);
						break;
					}
				}
			}

			// Remove all marked expenses
			for ($i = 0; $i < count($delexpense); $i++) {
				deleteExpense($delexpense[$i], 1);
			}
		} else {
			echo "Invalid person ".$name;
			exit;
		}

		deleteArrayID(&$persons, $pid);
		sortArray(&$persons);
		echo "Person '$name' deleted.\n\n";
	}

	// Function to rename a person
	function editPerson() {
		global $persons;
		global $person;
		global $newperson;
		global $list;

		if ($newperson == "") {
			echo "Please specify new name for person $person.\n";
			displayEditPerson();
			$list = "NONE";
			return;
		}

		$list = "PERSONS";

		$id = findPersonName($person);

		if ($id == -1) {
			echo "Person $person does not exist!\n";
			return;
		}

		$persons[$id]->name = $newperson;
		commitChanges();
	}

	// Function to modify an expense
	function editExpense($exp, $spender, $accountable, $date, $amount, $description) {
		global $expenses;
		global $persons;
		global $expense;

		if (findPersonID($spender) == -1) {
			echo "Invalid spender ID $spender\n";
			exit;
		}

		if (count($accountable) == 0) {
			echo "No accountable IDs specified\n";
			exit;
		}

		if ($amount == 0) {
			echo "No amount specified\n";
			exit;
		}

		for ($i = 0; $i < count($accountable); $i++) {
			$id = findPersonName($accountable[$i]);

			if ($id == -1) {
				echo "Invalid name $accountable[$i]\n";
				exit;
			}

			$accountable[$i] = $persons[$id]->ID;
		}

		$expenses[$exp]->spenderID = $spender;
		$expenses[$exp]->accountableIDs = $accountable;
		$expenses[$exp]->expenseDate = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
		$expenses[$exp]->amount = $amount;
		$expenses[$exp]->description = $description;

		commitChanges();
	}

	// Function to change password
	function changePass() {
		global $HTTP_SERVER_VARS;
		global $_POST;
		
		if (isset($_POST['login']) && 
			isset($_POST['password']) &&
			isset($HTTP_SERVER_VARS['PHP_AUTH_USER'])) {

			$login = new HttpAuthPlus; 
			$login->setAuthType('file'); 
			$login->setAuthEncrypt('crypt');
			$login->setAuthFile('data/.mshare.db');
			$login->RmUser($HTTP_SERVER_VARS['PHP_AUTH_USER']);
			$login->AddUser($_POST['login'], $_POST['password'], '');
			
			echo "Password changed successfully.";
		} else {
			displayChangePass();
		}
	}
?>
