<?php
	// Display the navigation bar on the top
	function displayNav() {
		global $file;
		global $files;
		global $list;

		$linkex = "nav";
		$linkpe = "nav";
		$linktr = "nav";

		switch ($list) {
			default:
			case "EXPENSES":
				$linkex = "navsel";
				break;
			case "PERSONS":
				$linkpe = "navsel";
				break;
			case "TRANSACTIONS":
				$linktr = "navsel";
				break;
		}


		echo "<form name=\"fileselect\">";

		echo "<a class=$linkex href=\"index.php?file=$file&list=EXPENSES\">Expenses</a> ";
		echo "<a class=$linkpe href=\"index.php?file=$file&list=PERSONS\">Persons</a> ";
		echo "<a class=$linktr href=\"index.php?file=$file&list=TRANSACTIONS\">Transactions</a> ";
		echo "<a class=nav href=\"index.php?file=$file&list=$list&action=CLOSE\">Close File</a> ";
		echo "<a class=nav href=\"index.php?file=$file&list=$list&action=RENUMBER\">Renumber</a> ";
		echo "<a class=nav href=\"index.php?file=$file&list=$list&action=RESTORE\">Restore</a> ";
		//echo "<a class=nav href=\"index.php?file=$file&list=$list&action=CHANGEPASS\">Change Password</a> ";
		echo "<a class=nav href=\"index.php?file=$file&list=LOGOUT\">Logout</a> ";
		echo "<select name=\"file\" OnChange=\"location.href=fileselect.file.options[selectedIndex].value\">";
		echo "<option selected>Data file...";

		for ($i = 0; $i < count($files); $i++) {
			echo "<option value=\"index.php?file=$files[$i]\">$files[$i]";
		}
		
		echo "</select></form></td></tr></table><pre>\n";
	}

	// Display the add expense form
	function displayAddExpense($eid='', $spenderID='', $accID=array(), $month=-1, $day=-1, $year=-1, $amount='', $desc='') {
		global $persons;
		global $list;
		global $file;

		$curdate = array();

		echo "</pre><form name=\"expense\" action=\"index.php\" method=\"get\">\n";
		echo "<input type=\"hidden\" name=\"file\" value=\"$file\">\n";
		echo "<input type=\"hidden\" name=\"type\" value=\"EXPENSE\">\n";

		if ($eid != '') {
			echo "<input type=\"hidden\" name=\"expense\" value=\"$eid\">\n";
			echo "<input type=\"hidden\" name=\"action\" value=\"EDIT\">\n";
			echo "<b>MODIFY EXPENSE</b><br><br>\n";
			echo "<table><tr><td align=right><b><i>Expense ID:</i></b></td><td><b><i>$eid</i></b></td></tr>\n";
		} else echo "<input type=\"hidden\" name=\"action\" value=\"ADD\">\n<table>";

		echo "<tr><td align=right>Spender:</td><td><select name=\"spender\"";
			
		if ($eid != '') echo " value=\"$spenderID\">\n";
		else echo ">\n";

		for ($i = 0; $i < count($persons); $i++) {
			echo "<option value=\"{$persons[$i]->ID}\"";
			if ($persons[$i]->ID == $spenderID) echo " selected=\"selected\"";
			echo ">{$persons[$i]->name}\n";
		}

		echo "</select><tr><td align=right>Accountable:</td><td>\n";

		for ($i = 0; $i < count($persons); $i++) {
			echo "<input type=\"checkbox\" name=\"accountable[{$persons[$i]->name}]\"";
		       
			// Don't check accountables by default
			//if (count($accID) == 0) echo " CHECKED>{$persons[$i]->name} ";
			//else {
				if (in_array($persons[$i]->ID, $accID)) echo " CHECKED";
				echo ">{$persons[$i]->name} ";
			//}
		}

		$curdate = getdate();
		if ($month == -1) $month = $curdate["mon"];
		if ($day == -1) $day = $curdate["mday"];
		if ($year == -1) $year = $curdate["year"];

		echo "</td></tr>\n<tr><td align=right>Date:</td><td><select name=\"date['month']\"><option value=\"-1\">mm\n";

		for ($i = 1; $i < 13; $i++) {
			echo "<option value=\"$i\"";
			if ($i == $month) echo " selected=\"selected\"";
			echo ">$i\n";
		}
		
		echo "</select>\n
			<select name=\"date['day']\"><option value=\"-1\">dd\n";

		for ($i = 1; $i < 32; $i++) {
			echo "<option value=\"$i\"";
			if ($i == $day) echo " selected=\"selected\"";
			echo ">$i\n";
		}

		echo "</select>\n
			<select name=\"date['year']\" value=\"$year\"><option value=\"-1\">yyyy\n";

		for ($i = 2004; $i < 2008; $i++) {
			echo "<option value=\"$i\"";
			if ($i == $year) echo " selected=\"selected\"";
			echo ">$i\n";
		}

		echo "</select></td></tr>\n";
		echo "<tr><td align=right>Amount:</td><td><input type=\"text\" name=\"amount\" size=\"8\" maxlength=\"64\" value=\"$amount\"></td></tr>\n";
		echo "<tr><td align=right>Desc:</td><td><input type=\"text\" name=\"description\" size=\"16\" maxlength=\"64\" value=\"$desc\"></td></tr>\n";
		echo "<tr><td></td><td><input type=\"submit\" value=\"";
		if ($eid == '') echo "Add expense";
		else echo "Modify expense";
		echo "\"></td></tr></table></form>\n";
	}

	// Display the expenses page
	function displayExpenses() {
		global $persons;
		global $expenses;
		global $file;
		global $sort;
		global $closed;
		global $list;

		echo "  <a href=\"index.php?file=$file&list=EXPENSES&sort=ID\">ID</a>   Date        ";
		echo "<a href=\"index.php?file=$file&list=EXPENSES&sort=NAME\">Name</a>      ";
		echo "<a href=\"index.php?file=$file&list=EXPENSES&sort=AMOUNT\">Amount</a>   ";
		echo "<a href=\"index.php?file=$file&list=EXPENSES&sort=DESCRIPTION\">Description</a>        Acccountable\n";
		echo "  --   ----------  --------  -------  -----------------  ------------\n";

		switch ($sort) {
			case "NAME":
				sortExpensesSpender();
				break;
			case "AMOUNT":
				sortExpensesAmount();
				break;
			case "DESCRIPTION":
				sortExpensesDescription();
			default:
			case "ID":
				break;
		}

		for ($i = 0; $i < count($expenses); $i++) {
			if (!$expenses[$i]->deleted) {
				if (!$closed) {
					printf("%4d   <a href=\"javascript:setDate('%s');\">%s</a>  <a href=\"javascript:setSpender('%s');\">%-8s</a>  %4.2f  <a href=\"javascript:setDesc('%s');\">%-19s</a>%-40s    ", 
						$expenses[$i]->ID,
						$expenses[$i]->expenseDate,
						date("M d 'y", $expenses[$i]->expenseDate),
						getPersonName($expenses[$i]->spenderID), 
						getPersonName($expenses[$i]->spenderID), 
						$expenses[$i]->amount,
						$expenses[$i]->description,
						substr($expenses[$i]->description, 0, 17),
						substr(accountableToString($expenses[$i]->accountableIDs), 0, 40));

					echo "<font size=-3><a href=\"index.php?file=$file&list=EXPENSES&action=EDIT&type=EXPENSE&expense={$expenses[$i]->ID}\">edit</a></font>   ";
					echo "<font size=-3><a href=\"index.php?file=$file&list=EXPENSES&action=DELETE&type=EXPENSE&expense={$expenses[$i]->ID}\">delete</a></font>\n";
				} else {
					printf("%4d   %s  %-8s  %4.2f  %-19s%-40s\n", 
						$expenses[$i]->ID,
						date("M d 'y", $expenses[$i]->expenseDate),
						getPersonName($expenses[$i]->spenderID), 
						$expenses[$i]->amount,
						substr($expenses[$i]->description, 0, 17),
						substr(accountableToString($expenses[$i]->accountableIDs), 0, 40));
				}
			}
		}

		if (!$closed) displayAddExpense();
	}

	function displayPersons() {
		global $persons;
		global $expenses;
		global $file;
		global $closed;

		echo "  ID   Name    \n";
		echo "  --   --------\n";

		for ($i = 0; $i < count($persons); $i++) {
			printf("%4d   <a href=\"javascript:togglePerson('%s');\">%-8s</a>    ", 
				$persons[$i]->ID, 
				$persons[$i]->name, 
				$persons[$i]->name);
			if (!$closed) {
				echo "<font size=-3><a href=\"index.php?file=$file&list=PERSONS&action=EDIT&type=PERSON&person={$persons[$i]->name}\">edit</a></font>  ";
				echo "<font size=-3><a href=\"index.php?file=$file&list=PERSONS&action=DELETE&type=PERSON&person={$persons[$i]->name}\">delete</a></font>";
			}

			echo "<div id=\"".$persons[$i]->name."\" style=\"display: none;\"><font size=-1>";
			
			$spent = "";
			$owes = "";
			$spentam = 0;
			$owesam = 0;
			for ($j = 0; $j < count($expenses); $j++) {
				if (!$expenses[$j]->deleted) {
					if ($expenses[$j]->spenderID == $persons[$i]->ID) {
						$am = $expenses[$j]->amount;
						for ($k = 0; $k < count($expenses[$j]->accountableIDs); $k++) {
							if ($expenses[$j]->accountableIDs[$k] == $persons[$i]->ID) {
								$am -= ($am / count($expenses[$j]->accountableIDs));
								break;
							}
						}
						$spentam += $am;
						$spent .= sprintf("             spent  %4.2f", $am);
						if ($expenses[$j]->description) 
							$spent .= "  for  ".substr($expenses[$j]->description, 0, 17);
						$spent .= "\n";
					} else {
						for ($k = 0; $k < count($expenses[$j]->accountableIDs); $k++) {
							if ($expenses[$j]->accountableIDs[$k] == $persons[$i]->ID) {
								$am = $expenses[$j]->amount/count($expenses[$j]->accountableIDs);
								$owesam += $am;
								$owes .= sprintf("             owes  %-8s %4.2f",
									getPersonName($expenses[$j]->spenderID), $am);
								if ($expenses[$j]->description) 
									$owes .= "  for  ".substr($expenses[$j]->description, 0, 17);
								$owes .= "\n";
							}
						}
					}
				}
			}

			$spent .= sprintf("           Total spent: %.2f\n", $spentam);
			echo $spent;

			$owes .= sprintf("           Total owed: %.2f\n\n", $owesam);
			echo $owes;

			if ($spentam < $owesam) 
				printf("           Needs to pay %.2f\n", $owesam - $spentam);
			else if ($spentam > $owesam) 
				printf("           Will receive %.2f\n", $spentam - $owesam);
			else echo "            No balance\n";

			echo "</font></div>\n";
		}

		echo "</pre>\n";
		
		if (!$closed) {
			echo "<form action=\"index.php\" method=\"get\">\n";
			echo "<input type=\"hidden\" name=\"file\" value=\"$file\">\n";
			echo "<input type=\"hidden\" name=\"action\" value=\"ADD\">\n";
			echo "<input type=\"hidden\" name=\"type\" value=\"PERSON\">\n";
			echo "<input type=\"text\" name=\"person\" size=\"16\" maxlength=\"64\">\n";
			echo "<input type=\"submit\" value=\"Add a person\">\n";
			echo "</form>\n";
		}
	}

	function displayEditPerson() {
		global $persons;
		global $person;
		global $file;

		$id = findPersonName($person);
		if ($id == -1) {
			echo "Invalid person $person.\n";
			exit;
		}

		$ID = $persons[$id]->ID;

		echo "</pre><b>MODIFY PERSON</b><br><br><form action=\"index.php\" method=\"get\">\n";
		echo "<input type=\"hidden\" name=\"file\" value=\"$file\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"EDIT\">\n";
		echo "<input type=\"hidden\" name=\"type\" value=\"PERSON\">\n";
		echo "<input type=\"hidden\" name=\"person\" value=\"$person\">\n";
		echo "<table><tr><td align=right><b><i>ID:</i></b></td><td><b><i>$ID</i></b></td></tr>";
		echo "<tr><td align=right>Name:</td><td><input type=\"text\" name=\"newperson\" value=\"$person\"></td></td>";
		echo "<tr><td></td><td><input type=\"submit\" value=\"Modify person\"></td></tr></table>\n";
		echo "</form>\n";
	}

	function displayTransactions() {
		global $transactions;

		echo "  ID   From      To        Amount\n";
		echo "  --   --------  --------  -------\n";

		for ($i = 0; $i < count($transactions); $i++) {
			printf("%4d   %-8s  %-8s  %4.2f\n",
				$transactions[$i]->ID,
				getPersonname($transactions[$i]->fromID),
				getPersonname($transactions[$i]->toID),
				$transactions[$i]->amount);
		}
	}

	function displayPage() {
		global $expenses;
		global $file;
		global $list;
		global $action;
		global $type;
		global $person;
		global $newperson;
		global $expense;
		global $spender;
		global $accountable;
		global $date;
		global $amount;
		global $description;

		switch($action) {
			case "ADD":
				if ($type == "PERSON") {
					addPerson($person);
					$list = "PERSONS";
				} else if ($type == "EXPENSE") {
					addExpense($spender, $accountable, $date, $amount, $description);
					$list = "EXPENSES";
				}
				commitChanges();
				break;
			case "DELETE":
				if ($type == "PERSON") {
					deletePerson($person);
					$list = "PERSONS";
				} else if ($type == "EXPENSE") {
					deleteExpense($expense, 0);
					$list = "EXPENSES";
				}
				commitChanges();
				break;
			case "EDIT":
				if ($type == "PERSON") {
					if ($newperson == "~") {
						displayEditPerson();
						$list = "NONE";
					} else {
						editPerson();
					}
					break;
				} else if ($type == "EXPENSE") {
					$id = findExpenseID($expense);

					if ($id == -1) {
						echo "Invalid expense ID $expense.\n";
						$list = "EXPENSES";
						break;
					}

					if ($spender == 0) {
						$month = date("n", $expenses[$id]->expenseDate);
						$day = date("j", $expenses[$id]->expenseDate);
						$year = date("Y", $expenses[$id]->expenseDate);

						displayAddExpense($expense, $expenses[$id]->spenderID, 
							$expenses[$id]->accountableIDs, 
							$month, $day, $year,
							$expenses[$id]->amount,
							$expenses[$id]->description);
						$list = NONE;
					} else {
						editExpense($id, $spender, $accountable, $date, $amount, $description);
					}
				}
				break;
			case "CONVERT":
				commitChanges();
				echo "File $file converted to XML format.\n\n";
				break;
			case "RENUMBER":
				sortArray(&$persons);
				sortExpensesDate();
				commitChanges();
			default:
		}

		switch ($list) {
			case "NONE":
				break;
			default:
			case "EXPENSES":
				displayExpenses();
				break;
			case "PERSONS":
				displayPersons();
				break;
			case "TRANSACTIONS":
				displayTransactions();
				break;
		}
	}
?>
