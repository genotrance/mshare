<?php
	$numpersons = 0;
	$numexpenses = 0;

	// Convert XML data to Person and Expense objects
	function xmlToObj() {
		global $expenses;
		global $persons;
		global $xml;

		for ($i = 0; $i < count($xml['mshare']['persons']); $i++) {
			$person = new Person(
				$xml['mshare']['persons'][$i]['ID'],
				$xml['mshare']['persons'][$i]['name']);

			array_push($persons, $person);
		}
		sortArray($persons);

		for ($i = 0; $i < count($xml['mshare']['expenses']); $i++) {
			$expense = new Expense(
				$xml['mshare']['expenses'][$i]['ID'], 
				$xml['mshare']['expenses'][$i]['spenderID'], 
				$xml['mshare']['expenses'][$i]['accountableIDs'], 
				$xml['mshare']['expenses'][$i]['expenseDate'], 
				$xml['mshare']['expenses'][$i]['amount'], 
				$xml['mshare']['expenses'][$i]['description']);

			array_push($expenses, $expense);
		}
		sortArray($persons);
	}
	
	function startElement($parser, $name, $attrs='') {
		global $xml;
		global $numpersons;
		global $numexpenses;

		switch ($name) {
			case "mshare":
				$xml['mshare'] = $attrs;
				break;
			case "persons":
				$xml['mshare']['persons'] = $attrs;
				break;
			case "person":
				$xml['mshare']['persons'][$numpersons] = $attrs;
				$numpersons++;
				break;
			case "expenses":
				$xml['mshare']['expenses'] = $attrs;
				break;
			case "expense":
				$xml['mshare']['expenses'][$numexpenses] = $attrs;
				$numexpenses++;
				break;
			default:
				echo "Invalid tag $name!\n";
				exit;
		}
	}

	function endElement($parser, $name) {
		return;
	}

	// Convert XML objects to a string to save to file
	function toXML() {
		global $persons;
		global $expenses;

		$xmlString = "<?xml version='1.0' ?>\n<mshare version=\"".VERSION."\">\n";

		if (count($persons) > 0) {
			$xmlString .= "\t<persons>\n";

			for ($i = 0; $i < count($persons); $i++) {
				$xmlString .= "\t\t<person name=\"".$persons[$i]->name."\" ID=\"".$persons[$i]->ID."\"/>\n";
			}

			$xmlString .= "\t</persons>\n";
		}

		if (count($expenses) > 0) {
			$xmlString .= "\t<expenses>\n";

			for ($i = 0; $i < count($expenses); $i++) {
				$xmlString .= "\t\t<expense ID=\"".$expenses[$i]->ID."\" ";
				$xmlString .= "spenderID=\"".$expenses[$i]->spenderID."\" ";

				$xmlString .= "accountableIDs=\"";
				for ($j = 0; $j < count($expenses[$i]->accountableIDs); $j++) {
					$xmlString .= $expenses[$i]->accountableIDs[$j];

					if ($j != count($expenses[$i]->accountableIDs)-1) {
						$xmlString .= ":";
					}
				}
				$xmlString .= "\" ";

				$xmlString .= "expenseDate=\"".$expenses[$i]->expenseDate."\" ";
				$xmlString .= "amount=\"".$expenses[$i]->amount."\" ";
				$xmlString .= "description=\"".$expenses[$i]->description."\"/>\n";
			}

			$xmlString .= "\t</expenses>\n";
		}

		$xmlString .= "</mshare>\n";

		return $xmlString;
	}

	// Function to read the older version of mshare file format
	function parseBin() {
		global $file;
		global $persons;
		global $expenses;
		global $legacy;

		$line = "";
		$lineno = 0;

		$N = 0;		// Number of people
		$count = 0;

		$f = fopen($file, "r");
		if ($f == NULL) {
			echo "Unable to open $file";
			exit;
		}

		while(!feof($f)) {
			$line = fgets($f);
			$lineno++;

			if ($lineno == 1) {
				$N = (int) $line;
				continue;
			}

			if ($count < $N) {
				$ID = strtok($line, ":");
				$name = rtrim(strtok(":"));

				if (strtok(":")) {
					echo "$file: $lineno: ID:Name format expected";
					exit;
				}

				$person = new Person($ID, $name);
				array_push($persons, $person);
				$count++;
				continue;
			}

			$ID = strtok($line, ":");
			$spenderID = strtok(":");
			$date = strtok(":");
			$amount = strtok(":");
			$desc = rtrim(strtok(":"));
			$accID = "";

			if ($ID == "") continue;

			for ($i = 0; $i < count($persons); $i++) {
				$accID .= "{$persons[$i]->ID}";

				if ($i != count($persons)-1) {
					$accID .= ":";
				}
			}

			$expense = new Expense($ID, $spenderID, $accID, $date, $amount, $desc);
			array_push($expenses, $expense);
		}

		fclose($f);

		$legacy = 1;
	}

	// Swap $file and $file.bak
	function swapFiles() {
		global $file;

		rename($file, $file.".tmp");
		rename($file.".bak", $file);
		rename($file.".tmp", $file.".bak");
	}

	// Write memory contents to data file saving backup to $file.bak
	function commitChanges() {
		global $file;
		global $expenses;

		sortExpensesDate();
		renumber(&$expenses);

		$xmlString = toXML();

		$f = fopen($file.".bak", "w");
		fputs($f, $xmlString);
		fclose($f);

		swapFiles();
	}		
	
	// Load XML data to file and convert to objects
	function parseXML() {
		global $file;

		$xmlString = file_get_contents($file);
		if (!$xmlString) {
			echo "Unable to load file ".$file;
			exit;
		}

		if (!strstr($xmlString, "<?xml version='1.0' ?>")) {
			parseBin();
			return;
		}

		$xmlParser = xml_parser_create();
		xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, FALSE);
		xml_set_element_handler($xmlParser, "startElement", "endElement");

		xml_parse($xmlParser, $xmlString);
		xml_parser_free($xmlParser);

		xmlToObj();
	}
?>
