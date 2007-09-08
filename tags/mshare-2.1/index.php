<?php
	require_once("HttpAuthPlus_class.php");

	include("class.php");
	include("change.php");
	include("display.php");
	include("file.php");
	include("misc.php");
	include("process.php");

	define("VERSION", "2.1");
	define("DATA", "data/data.xml");
	define("LOG", "mshare2.log");
	define("HEADER", "<head><title>mshare v".VERSION."</title><link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"><script type=\"text/javascript\" language=\"javascript\" src=\"script.js\"></script></head>");

	// Object Arrays
	$expenses = array();
	$persons = array();
	$transactions = array();
	$files = glob("data/*");	// List of data files available to open

	// XML source
	$xml = NULL;

	// Internal Data
	$totalSpent = 0;
	$legacy = 0;		// Set to 1 if input file is in older format
	$closed = 0; 		// Set to 1 if file is marked as closed

	// Options
	$file = DATA; 		// File to open
	$list = "EXPENSES";	// Either "EXPENSES", "PERSONS", "TRANSACTIONS", "LOGOUT"
	$sort = "ID";		// Either "ID", "NAME", "AMOUNT", "DESCRIPTION" for list EXPENSES
	$action = "";		// Either "ADD", "DELETE", "EDIT", "CONVERT", "RESTORE", "RENUMBER", "CLOSE", "CHANGEPASS"
	$type = "";		// Either "PERSON" or "EXPENSE"
	$person = "";		// Contains person's name in an ADD or DELETE function
	$newperson = "~";	// Contains person's new name in EDIT function
	$expense = -1;		// Contains expense ID in an EDIT and DELETE function
	$spender = 0;		// Contains ID of spender in EXPENSE ADD
	$accountable = array();	// Contains IDs of accountable in EXPENSE ADD
	$date = array();	// Contains date['month', 'day', 'year'] in EXPENSE ADD
	$amount = 0;		// Contains amount in EXPENSE ADD
	$description = ""; 	// Contains description in EXPENSE ADD

	// Login related
	$login = new HttpAuthPlus;
	$login->setAuthType('file');
	$login->setAuthFile('data/.mshare.db');
	$login->setAuthEncrypt('crypt');
	$login->AuthUser();

	// Get all available data files, remove .bak files
	$files = glob("data/*");
	$f = array();
	for ($i = 0; $i < count($files); $i++) {
		if (substr($files[$i], -4) == ".xml" || substr($files[$i], -4) == ".bin") {
			array_push($f, $files[$i]);
		}
	}
	$files = $f;

	echo HEADER;
	echo "<body>mshare v".VERSION."<br>-----------<br><br>\n";

	if (!$files) {
		echo "No data files found.\n";
		exit;
	}

	// Parse options
	parse_str($_SERVER['QUERY_STRING']);

	$accountable = array_keys($accountable);
	$date = array_values($date);

	if ($list == "LOGOUT") {
		$login->Logout();
		echo "<a href=\"index.php\">Click here to login</a>";
		exit;
	} else if ($action == "RESTORE") {
		swapFiles();
		displayNav();
		echo "File $file restored.\n\n";
	} else if ($action == "CLOSE") {
		$closed = 1;
		displayNav();
		echo "File $file has been closed.\n\n";
	} else displayNav();

	// Parse data
	parseXML();
	scrubDB();
	genTransactions();

	if ($legacy && $action != "CONVERT") {
		echo "Click <a href=\"index.php?file=$file&list=$list&action=CONVERT\">here</a> to convert file to newer XML format.\n\n";
	}

	if ($action == "CLOSE") {
		commitChanges();
	}

	displayPage();
?>
