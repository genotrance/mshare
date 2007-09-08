<?php
	// Associate persons with expenses and calculate 
	// individual and total expenses
	function scrubDB() {
		global $persons;
		global $totalSpent;

		for ($i = 0; $i < count($persons); $i++) {
			$persons[$i]->mapExpenses();
			$persons[$i]->calcTotalExpenses();

			$totalSpent += $persons[$i]->totalExpenses;
		}
	}

	// Add a transaction to $transactions
	function addTransaction($accountableID, $spenderID, $numAcc, $expID, $amount) {
		global $expenses;
		global $transactions;

		$tid = -1;

		if (($tid = findTransaction($accountableID, $spenderID)) == -1) {
			$transaction = new Transaction(
				count($transactions)+1, $accountableID, $spenderID);

			$transaction->addExpense($expID, $amount/$numAcc);
			array_push($transactions, $transaction);
		} else {
			$transactions[$tid]->addExpense($expID, $amount/$numAcc);
		}
	}

	// Delete a transaction based on ID
	function deleteTransaction($id) {
		global $transactions;

		for ($i = 0; $i < count($transactions); $i++) {
			if ($transactions[$i]->ID == $id) {
				deleteArrayID(&$transactions, $i);
				//renumber(&$transactions);
				break;
			}
		}
	}

	// Merge source transaction ID to destination transaction ID
	// Type specifies to ADD or SUB amount
	function mergeTransactions($destID, $sourceID, $type) {
		global $transactions;

		// Subtract amount and zero out
		if ($type == "ADD") { $transactions[$destID]->amount += $transactions[$sourceID]->amount; }
		else if ($type == "SUB") { $transactions[$destID]->amount -= $transactions[$sourceID]->amount; }

		// Merge expenseList arrays
		$merge = array_merge($transactions[$destID]->expenseList, $transactions[$sourceID]->expenseList);
		$transactions[$destID]->expenseList = $merge;
	}

	// Minimize transactions between two people
	function minTransactions() {
		global $transactions;

		$brk = 0;

		renumber(&$transactions);

		// Minimize A -> B and B -> A transactions
		for ($i = 0; $i < count($transactions); $i++) {
			$tid = -1;

			if (($tid = findTransaction($transactions[$i]->toID, $transactions[$i]->fromID)) != -1) {
				if ($transactions[$tid]->amount > $transactions[$i]->amount) {
					mergeTransactions($tid, $i, "SUB");
					deleteTransaction($transactions[$i]->ID);
					minTransactions();
					$brk = 1;
					break;
				} else if ($transactions[$tid]->amount == $transactions[$i]->amount) {
					// Delete both transactions since they balance each other out
					$ttid = $transactions[$tid]->ID;

					deleteTransaction($transactions[$i]->ID);
					deleteTransaction($ttid);
					minTransactions();
					$brk = 1;
					break;
				}
			}
		}

		if ($brk) return;

		// Minimize A -> B -> C and A -> C transactions (Remove B -> C)
		// If no A -> C and amount(A -> B) = amount(B -> C), reduce to A -> C
		for ($i = 0; $i < count($transactions); $i++) {
			$tid1 = -1;
			$tid2 = -1;

			if (($tid1 = findTransactionByID($transactions[$i]->toID, "FROM")) != -1) {
				if (($tid2 = findTransaction($transactions[$i]->fromID, $transactions[$tid1]->toID)) != -1) {
					if ($transactions[$tid1]->amount > $transactions[$i]->amount) {
						mergeTransactions($tid1, $i, "SUB");
						mergeTransactions($tid2, $i, "ADD");
						deleteTransaction($transactions[$i]->ID);
						minTransactions();
						break;
					} else if ($transactions[$tid1]->amount < $transactions[$i]->amount) {
						mergeTransactions($i, $tid1, "SUB");
						mergeTransactions($tid2, $tid1, "ADD");
						deleteTransaction($transactions[$tid1]->ID);
						minTransactions();
						break;
					} else if ($transactions[$tid1]->amount == $transactions[$i]->amount) {
						mergeTransactions($tid2, $i, "ADD");

						$ttid1 = $transactions[$tid1]->ID;

						deleteTransaction($transactions[$i]->ID);
						deleteTransaction($ttid1);
						minTransactions();
						break;
					}
				} else if ($transactions[$i]->amount == $transactions[$tid1]->amount) {
					$newID = count($transactions)+1;
					$transaction = new Transaction($newID, $transactions[$i]->fromID, $transactions[$tid1]->toID);
					array_push($transactions, $transaction);

					mergeTransactions($newID-1, $i, "ADD");
					mergeTransactions($newID-1, $tid1, "ADD");
					$transactions[$newID-1]->amount -= $transactions[$i]->amount;

					$ttid1 = $transactions[$tid1]->ID;

					deleteTransaction($transactions[$i]->ID);
					deleteTransaction($ttid1);
					minTransactions();
					break;
				}
			}
		}
	}

	// Generate all the transaction objects between two IDs
	function genTransactions() {
		global $persons;
		global $expenses;
		global $transactions;

		for ($i = 0; $i < count($expenses); $i++) {
			if (!$expenses[$i]->deleted) {
				$numAcc = count($expenses[$i]->accountableIDs);

				for ($j = 0; $j < $numAcc; $j++) {
					// Don't add a transaction of A -> A
					if ($expenses[$i]->accountableIDs[$j] == $expenses[$i]->spenderID) continue;

					addTransaction(
						$expenses[$i]->accountableIDs[$j], $expenses[$i]->spenderID, 
						$numAcc, $expenses[$i]->ID, $expenses[$i]->amount);
				}
			}
		}

		minTransactions();
	}

	function printTransactions() {
		global $transactions;

		for ($i = 0; $i < count($transactions); $i++) {
			echo "ID: ".$transactions[$i]->ID."<BR>";
			echo "From: ".$transactions[$i]->fromID."<BR>";
			echo "To: ".$transactions[$i]->toID."<BR>";
			echo "Amount: ".$transactions[$i]->amount."<BR><BR>";
		}
	}
?>
