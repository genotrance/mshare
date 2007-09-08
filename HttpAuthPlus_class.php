<?php

/*------------------------------------------------------------------------------*
 | HttpAuthPlus v1.15                                                           |
 | Use/modify however, but please leave this header intact                      |
 | Copywrite 2003 Sean Walcek - EJW Associates, Inc. http://www.ejwassoc.com    |
 | Contact: admin@creationfarm.com                                              |
 | Latest version available at: http://sourceforge.net/projects/httpauthplus/   |
 | This software/class is released under & bound by the GNU (GPL) license       |
 -------------------------------------------------------------------------------*/

#require_once('DB.php');

class HttpAuthPlus {

   var $AuthEncrypt;     // the type of encryption used; options are 'crypt', or 'plain-text'; default is 'plain-text'
   var $AuthType;        // authenticate against a 'file' file or against a 'db'; deafult is 'db'
   var $AuthFile;        // the Full path to the flat-file to use; default is '~/users'
   var $FieldDel;        // the character used as a field delimeter in 'file' based authentication; default is '|'
   var $AuthRealm;       // the Realm displayed in the authentication dialog; default is 'HttpAuthPlus'
   var $LoginFailedMsg;  // string that is displayed when user fails authentication; default is 'Authentication Failed'
   var $DbType;          // The type of database to use; Options are ibase, msql, mssql, mysql, oci8, odbc, pgsql and sybase; default is 'mysql'
   var $DbProtocol;      // the protocol to use when connecting to the database; default is 'tcp'
   var $DbPort;          // the port on the database server to connect to; default is '3306'
   var $DbHost;          // the hostname or ip of the database server; default is 'localhost'
   var $DbUser;          // the username to use when connecting to the database server; default is ''
   var $DbPass;          // the password to use when connecting to the database server; default is ''
   var $DbName;          // the name of the database to use; default is 'mysql'
   var $DbInitStr;       // the combination of $DbType,$DbProtocol,$DbPort,$DbHost,$DbUser,$DbPass,$DbName to form the complete string needed for DB::connect
   var $TableName;       // the name of the table to query; default is 'user'
   var $IdField;		 // the name of the primary key field, defualt is 'id'
   var $UsernameField;   // the name of the field that contains the username; default is 'username'
   var $PasswordField;   // the name of the field that contains the password; default is 'password'
   var $EmailField;      // the name of the field that contains the email; default is 'email'
   var $MailFrom;        // The email address to use in the FROM header of the msg sent by the getLostPass() method
   var $LostPassMsg;     // string for use when sending users their forgotten username/password
                         // '<username>' is substituted with real username
                         // '<password>' is substituted with real password

/*---------------------------*
 | Constructor Sets Defaults |
 *---------------------------*/
 
   ###
   ### Constructor sets some default values
   ###
   function HttpAuthPlus()
   {
      // Set the default authentication params
      $this->AuthEncrypt = 'plain-text';
      $this->AuthType = 'db';
      $this->AuthFile = '~/users/password';
      $this->FieldDel = '|';
      $this->AuthRealm = 'HttpAuthPlus';
      // Set some default database parameters
      $this->DbType = 'mysql';
      $this->DbProtocol = 'tcp';
      $this->DbPort = '3306';
      $this->DbHost = 'localhost';
      $this->DbUser = '';
      $this->DbPass = '';
      $this->DbName = '';
      $this->TableName = 'user';
	  $this->IdField = 'id';
      $this->UsernameField = 'username';
      $this->PasswordField = 'password';
      $this->EmailField = 'email';
      $this->DbInitStr = $this->DbType."://".$this->DbUser.":".$this->DbPass."@".$this->DbProtocol."(".$this->DbHost.":".$this->DbPort.")/".$this->DbName;
      // Set the default message for authentication failures
      $this->LoginFailedMsg = 'Authentication Failed';
   }

/*------------------*
 | Public Functions |
 *------------------*/

   ###
   ### define the authentication encryption type
   ###
   function setAuthEncrypt($type='plain-text')
   {
       if ($type != 'crypt' && $type != 'plain-text') {
           echo 'wrong parameter: options are "plain-text" or "crypt"';
           return false;
       }
      $this->AuthEncrypt = trim($type);
   }
   ###
   ### define the authentication mechinism type
   ###
   function setAuthType($type="db")
   {
      $this->AuthType = trim($type);
   }
   ###
   ### define the authentication file location
   ###
   function setAuthFile($file="~/users/password")
   {
      $this->AuthFile = trim($file);
   }
   ###
   ### define the field delimeter for flat-file based authentication
   ###
   function setFieldDel($delimeter="|")
   {
      $this->FieldDel = trim($delimeter);
   }
   ###
   ### defines the Realm for the authorization dialog
   ###
   function setAuthRealm($realm="HttpAuthPlus")
   {
      $this->AuthRealm = trim($realm);
   }
   ###
   ### define the database server type
   ###
   function setDbType($type="mysql")
   {
      $this->DbType = trim($type);
   }
   ###
   ### define the database server protocol
   ###
   function setDbProtocol($protocol="tcp")
   {
      $this->DbProtocol = trim($protocol);
   }
   ###
   ### define the database server port
   ###
   function setDbPort($port="3306")
   {
      $this->DbPort = trim($port);
   }
   ###
   ### define the database server hostname or ip
   ###
   function setDbHost($host="localhost")
   {
      $this->DbHost = trim($host);
   }
   ###
   ### define the database server username
   ###
   function setDbUser($username="")
   {
      $this->DbUser = trim($username);
   }
   ###
   ### define the database server password
   ###
   function setDbPass($password="")
   {
      $this->DbPass = trim($password);
   }
   ###
   ### define the database name
   ###
   function setDbName($db_name='')
   {
      $this->DbName = trim($db_name);
   }
   ###
   ### define the name of the table to authenticate against
   ###
   function setTableName($tbl_name='user')
   {
      $this->TableName = trim($tbl_name);
   }
   ###
   ### define the name of the id field in $TableName
   ###
   function setIdField($field_name='id')
   {
      $this->IdField = trim($field_name);
   }
   ###
   ### define the name of the username field in $TableName
   ###
   function setUsernameField($field_name='username')
   {
      $this->UsernameField = trim($field_name);
   }
   ###
   ### define the name of the password field in $TableName
   ###
   function setPasswordField($field_name='password')
   {
      $this->PasswordField = trim($field_name);
   }
   ###
   ### define the name of the email field in $TableName
   ###
   function setEmailField($field_name='email')
   {
      $this->EmailField = trim($field_name);
   }
   ###
   ### Set the address that appears in the FROM header of the getLostPass() method
   ###
   function setMailFrom($email='')
   {
      if ($email == '' || !$email) {
         echo "Message FROM Header not defined";
         return false;
      } else {
         $this->MailFrom = trim($email);
         return true;
      }
   }
   ###
   ### Set the message to use in the body of the getLostPass() method
   ###
   function setLostPassMsg($string="")
   {
      if ($string == "" || !$string) {
         echo "Message Body not defined";
         return false;
      } else {
         $this->LostPassMsg = $string;
         return true;
      }
   }
   ###
   ### Set the message to use when a user fails authentication
   ###
   function setLoginFailedMsg($string="Authentication Failed")
   {
      $this->LoginFailedMsg = trim($string);
      return true;
   }
   ###
   ### Check to see if a user is logged in or not
   ###
   function getAuthStatus()
   {
      global $HTTP_SERVER_VARS;
      if (!isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) && !is_array(explode(':', base64_decode(substr($HTTP_SERVER_VARS['HTTP_AUTHORIZATION'], 6))))) {
         return false;
      } else {
         return true;
      }
   }
   ###
   ### Give the user the chance to login
   ### Compare username & pass against database or file
   ###
   function AuthUser()
   {
      global $HTTP_SERVER_VARS;
      
      // make sure no pages are cached
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache"); // HTTP/1.0
      
      // if the user has not yet recieved a login prompt
      if (!isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || $HTTP_SERVER_VARS['PHP_AUTH_USER'] == '') {
         header('WWW-Authenticate: Basic realm="'.$this->AuthRealm.'", stale=FALSE');
         header('HTTP/1.0 401 Unauthorized');
         echo $this->LoginFailedMsg."\n";
         exit;
         
      // if the user has already recieved (and filled out) the login prompt
      } else {
          // validate the username & pass the user submitted
          $user_login = $this->validateData($HTTP_SERVER_VARS['PHP_AUTH_USER'], $HTTP_SERVER_VARS['PHP_AUTH_PW']);
          if (!$user_login) {
              $this->Logout();
              $this->AuthUser();
          }
          // if we are authenticating against a database
          if ($this->AuthType == "db") {
             $this->DbAuth($user_login[0], $user_login[1]);
          // if we are using a flat-file for authentication
          } else {
            $this->FileAuth($user_login[0], $user_login[1]);
          }
      }
   }
   ###
   ### decides if DbAddUser or FileAddUser should be used to add a new user
   ###
   function AddUser($username, $password, $email="")
   {
      if ($this->AuthType == "db") {
	     return $this->DbAddUser($username, $password, $email);
      } else {
         return $this->FileAddUser($username, $password, $email);
      }
   }
   ###
   ### decides if DbEditUser or FileEditUser should be used to update an existing user
   ###
   function EditUser($username, $newusername, $password, $email, $newemail)
   {
      if ($this->AuthType == "db") {
         if ($this->DbEditUser($username, $newusername, $password, $email, $newemail)) {
            return true;
         } else {
            return false;
         }
      } else {
         if ($this->FileEditUser($username, $newusername, $password, $email, $newemail)) {
            return true;
         } else {
            return false;
         }
      }
   }
   ###
   ### decides if DbRmUser or FileRmUser should be used to update an existing user
   ###
   function RmUser($username="")
   {
      /* Make sure $username contains a value */
      if ($username == "") {
         return false;
      }
      if ($this->AuthType == "db") {
         if ($this->DbRmUser($username)) {
            return true;
         } else {
            return false;
         }
      } else {
         if ($this->FileRmUser($username)) {
            return true;
         } else {
            return false;
         }
      }
   }
   ###
   ### Method to logout a user
   ###
   function Logout()
   {
      global $HTTP_SERVER_VARS;
      unset($HTTP_SERVER_VARS['PHP_AUTH_USER']);
      unset($HTTP_SERVER_VARS['PHP_AUTH_PW']);
	  unset($HTTP_SERVER_VARS['HTTP_AUTHORIZATION']);
	  return true;
   }
   ###
   ### Method to email the username & password to users who forgot theirs
   ###
   function getLostPass($email)
   {
      // remove any whitespace from the var
      $email = trim($email);
      // Connect to & select the database using PEARs DB class for DB independance
      if (DB::isError( $db = DB::connect( $this->DbInitStr ) ) ) {
         echo DB::errorMessage($db);
         return false;
      }
      $query = "SELECT ".$this->UsernameField.",".$this->PasswordField." FROM ".$this->TableName." WHERE (".$this->EmailField." = \"$email\")";
      // execute the query, & check to see if any errors came back
      if ( DB::isError( $result = $db->query( $query ) ) ) {
         echo DB::errorMessage($result);
         return false;
      }
      if (DB::numRows($result) == 1) {
         $result->fetchRow($result);
         $email_msg = str_replace("<username>", $result[0], $this->LostPassMsg);
         $email_msg = str_replace('<password>', $result[1], "$email_msg");
         mail ( $email, $this->mail_from, $email_msg);
         return true;
      } else {
         return false;
      }
   }
   ###
   ### combine all of the databse vars into one string (DSN) to pass to PEAR's DB::connect
   ###
   function setDbInitStr()
   {
      $this->DbInitStr = $this->DbType."://".$this->DbUser.":".$this->DbPass."@".$this->DbProtocol."(".$this->DbHost.":".$this->DbPort.")/".$this->DbName;
   }

/*-------------------*
 | Private Functions |
 *-------------------*/

   ###
   ### Depending on what $this->AuthEncrypt is, we may or may not need to create a hash of the $password
   ### options are 'crypt' and 'plain-text'; default is 'plain-text'
   ###
   function setPassHash($password) {
       switch ($this->AuthEncrypt) {
           case 'crypt':
                // get salt and crypt()
			    $salt = substr($password, 0, 2);
			    $enc_pass = crypt($password, $salt);
                return $enc_pass;
           case 'plain-text':
                // Do nothing; we're using plain-text
                return $password;
           default:
                return false;
       }
   }
   ###
   ### Validate what the user has entered BEFORE comparisons are done.
   ### Make sure no one tries to pass malicious code through...
   ###
   function validateData($username, $password)
   {
       $username = trim($username);
       $password = trim($password);
       // any trying to submit nada for username & pass
       if ($username == "" || $password == "") {
           return false;
       }
       // for anyone trying to sneek in delimeters
       if (strstr($username, $this->FieldDel) || strstr($password, $this->FieldDel)) {
           return false;
       }
       // for anyone trying to pass spaces through - arbitrary code
       if (strstr($username, " ") || strstr($password, " ")) {
           return false;
       }
       // make sure all quotes are properly escaped before proccessing
       if (!get_magic_quotes_gpc()) {
          $username = addslashes($username);
          $password = addslashes($password);
       }
       $user_login[] = $username;
       $user_login[] = $password;
       return $user_login;
   }
   
  /*-----------------------------*
   | Database specific functions |
   *-----------------------------*/

   ###
   ### function for authenticating against a database; used when $this->AuthType == "db"
   ###
   function DbAuth($username, $password)
   {
      // Connect to & select the database using PEARs DB class for DB independance
      if (DB::isError( $db = DB::connect( $this->DbInitStr ) ) ) {
         echo "Connection Failed: <br>\n".DB::errorMessage($db);
         return false;
      }
      // hash the pass if needed
      $enc_pass = $this->setPassHash($password);
      // Create the query to run against the database
      $query = "SELECT ".$this->UsernameField." FROM ".$this->TableName." WHERE (".$this->UsernameField." = \"$username\") AND (".$this->PasswordField." = \"$enc_pass\")";
      // execute the query, & check to see if any errors came back
      if ( DB::isError( $result = $db->query( $query ) ) ) {
         echo "Query Failed:<br>\n".DB::errorMessage($result);
         return false;
      }
      // If only result came back, the user is valid.
      // If 0 results came back, the user is NOT valid,
      // If more that 1 came back...something is weird
      if ($result->numRows() == 1) {
         return true;
      } else {
         $this->Logout();
         $this->AuthUser();
      }
   }
   ###
   ### updates users when $this->AuthType == "db"
   ###
   function DbEditUser($username, $newusername, $password, $email, $newemail)
   {
      // Connect to & select the database using PEARs DB class for DB independance
      if (DB::isError( $db = DB::connect( $this->DbInitStr ) ) ) {
         echo DB::errorMessage($db);
         return false;
      }
      // make sure the $newusername is not already being used; if we're changing the original username
      if ($username != $newusername) {
         $query = "SELECT ".$this->UsernameField." FROM ".$this->TableName." WHERE(".$this->UsernameField." = \"".trim($newusername)."\")";
         if ( DB::isError( $result = $db->query( $query ) ) ) {
            echo "$query<br><br> ".DB::errorMessage($result);
            return false;
         }
         if ($result->numRows() == 1) {
            echo 'Username: '."$newusername".' already exists';
            return false;
         }
      }
      // make sure the $newemail is not already being used; if we're changing the original email
      if ($email != $newemail) {
         $query = "SELECT ".$this->EmailField." FROM ".$this->TableName." WHERE(".$this->EmailField." = \"".trim($newemail)."\")";
         if ( DB::isError( $result = $db->query( $query ) ) ) {
            echo DB::errorMessage($result);
            return false;
         }
         if ($result->numRows() == 1) {
            echo 'Email: '."$newemail".' already exists';
            return false;
         }
      }
      // build the update query
      $query = "UPDATE ".$this->TableName." SET "
      .$this->UsernameField." = \"$newusername\", ".$this->PasswordField." = \"".$this->setPassHash(trim($password))."\", ".$this->EmailField." = \"".trim($newemail)."\"
      WHERE (".$this->UsernameField." = \"".trim($username)."\")";
      // commit the change to the database
      if ( DB::isError( $result = $db->query( $query ) ) ) {
         echo DB::errorMessage($result);
         return false;
      }
      return true;
   }
   ###
   ### removes a user when $this->AuthType == "db"
   ###
   function DbRmUser($username)
   {
      // Connect to & select the database using PEARs DB class for DB independance
      if (DB::isError( $db = DB::connect( $this->DbInitStr ) ) ) {
         echo DB::errorMessage($db);
         return false;
      }
      $query = "DELETE FROM ".$this->TableName." WHERE (".$this->UsernameField." = \"".trim($username)."\")";
      if ( DB::isError( $result = $db->query( $query ) ) ) {
         echo DB::errorMessage($result);
         return false;
      }
      return true;
   }
   ###
   ### adds users to the database when $this->AuthType == "db"
   ###
   function DbAddUser($username, $password, $email="")
   {
      // Connect to & select the database using PEARs DB class for DB independance
      if (DB::isError( $db = DB::connect( $this->DbInitStr ) ) ) {
         echo DB::errorMessage($db);
         return false;
      }
      // Make sure we don't have any whitespace on the vars
      $email = trim($email);
      // create the has of the pass if needed
      $password = $this->setPassHash(trim($password));
      $username = trim($username);
      // Check for another user with the same '$username' or '$email'
      $query = "SELECT ".$this->UsernameField.",".$this->EmailField." FROM ".$this->TableName."
      WHERE (".$this->EmailField." = \"$email\") OR (".$this->UsernameField." = \"$username\")";
      if (DB::isError($result = $db->query($query))) {
         echo DB::errorMessage($result);
         return false;
      }
      // If we have 1 or more result, the user already exists, and shuold not be added
      if ($result->numRows() == 1) {
         echo 'The Username or Email you are attempting to add already exists';
         return false;
      }
      // Otherwise, insert the user data into the db
      else {
         $query = "INSERT INTO ".$this->TableName." (".$this->UsernameField.",".$this->PasswordField.",".$this->EmailField.") VALUES ('$username','$password','$email')";
         if (DB::isError($result = $db->query($query))) {
            echo DB::errorMessage($result);
            return false;
         }
		 // get the id of this record
		 return $this->DbGetID($username);
      }
   }
   // accepts single arg; the username of the user to get the primary key for
   // returns the id of the user, or false if not found
   function DbGetID($username) 
   {
	  // make sure we have a field name to check
	  if ($this->IdField == '' || $this->IdField == NULL) {
	     echo 'Unable to get primary key. Undefined value for primary key column name.';
		 return false;
	  }
	  // prepare the query
      $query = "SELECT ".$this->IdField." FROM ".$this->TableName." WHERE (".$this->UsernameField." = \"$username\")";
      if (DB::isError($result = $db->query($query))) {
         echo DB::errorMessage($result);
         return false;
      }
      if ($result->numRows() == 1) {
         $result->fetchRow($result);
         return $result[0];
      } else {
	     return false;
      }
   }

  /*-------------------------*
   | File specific functions |
   *-------------------------*/

   ###
   ### function for locking the password file;
   ### controlling simultaneous file access using flock
   ###
   function FileLock($fp, $Lock_Level=LOCK_EX)
   {
      @flock($fp, $Lock_Level) or die ("Could not lock file");
   }
   ###
   ### function for locking the password file;
   ### controlling simultaneous file access using flock
   ###
   function FileUnLock($fp)
   {
      @flock($fp, LOCK_UN) or die ("Could not unlock file");
   }
   ###
   ### checks if $username is already a user or not;
   ### returns true if user does already exist, false otherwise
   ###
   function chkUser($username,$email)
   {
      // Open the file and read it into an array for parsing
      if (!($lines = @file($this->AuthFile))) {
         touch($this->AuthFile);
         return false;
      }
      // Find the record that cantains $username
      // format is: username|password|email\n
      foreach ($lines as $line) {
         /* trim whitespace from front and end of line */
         $cleanline = trim($line);
         $cleanline = explode($this->FieldDel, $cleanline);
         if ( (trim($cleanline[0]) == trim($username)) || (trim($cleanline[2]) == trim($email)) ) {
            $numrows[] = $cleanline[0];
         }
      }
      if (count($numrows) >= 1) {
         return true;
      } else {
         return false;
      }
   }
   ###
   ### function for authenticating against a flat-file; used when $this->AuthType == "file"
   ### returns true if a record containing $username & $password is found ONLY once in the password file
   ###
   function FileAuth($username, $password)
   {
      // hash the password if needed
      $enc_pass = $this->setPassHash($password);
      // Open the file and read it into an array for parsing
      $lines = file($this->AuthFile) or die ("Could not open password file");
      // Find the record that cantains $username
      // format is: username|password|email\n
      foreach ($lines as $line) {
         /* trim whitespace from front and end of line */
         $cleanline = trim($line);
         $cleanline = explode($this->FieldDel, $cleanline);
         if ($cleanline[0] == $username && $cleanline[1] == $enc_pass) {
            $numrows[] = $cleanline[0];
         }
      }
      // Now, if we only have one element in the array, the user is valid; if we have more or less than one, the user is invalid
      if (count($numrows) == 1) {
         return true;
      } else {
         $this->Logout();
         $this->AuthUser();
      }
   }
   ###
   ### adds a user if $this->AuthType == 'file'
   ###
   function FileAddUser($username, $password, $email)
   {
      // make sure we don't have any whitespace on the vars...
      $username = trim($username);
      $password = trim($password);
      $email = trim($email);
      // see if $username already exists in the password file
      if($this->chkUser($username,$email)) {
         echo "User: $username or Email: $email already exists";
         return false;
      } elseif (strchr($username, $this->FieldDel) || strchr($email, $this->FieldDel) || strchr($password, $this->FieldDel)) {
         echo 'Fields may not contains the '.$this->FieldDel.' character';
         return false;
      } else {
          // crypt the password if needed
          $enc_pass = $this->setPassHash($password);
          // combine the vars into 1 '$this->FieldDel' delimeted line to add to the password file
          $line = $username.$this->FieldDel.$enc_pass.$this->FieldDel.$email."\n";
          // open the password file for writing
          $fp = @fopen($this->AuthFile, "a") or die ("Cannot open file for writing.");
          // lock the file exclusively
          $this->FileLock($fp);
          // add the new record to the end of the file
          fwrite($fp, $line);
          // unlock the file
          $this->FileUnLock($fp);
          // close the file
          @fclose($fp);
          return true;
      }
   }
   ###
   ### updates a users record if $this->AuthType == 'file'
   ###
   function FileEditUser($username, $newusername, $password, $email, $newemail)
   {
      // make sure we don't have any whitespace on the vars...
      $username = trim($username);
      $newusername = trim($newusername);
      $password = trim($password);
      $email = trim($email);
      $newemail = trim($newemail);
      // make sure there isn't already a record with this $newusername
      if($this->chkUser($newusername,$newemail)) {
         echo "User: $newusername or Email: $newemail already exists.";
         return false;
      } elseif (strchr($username, $this->FieldDel) || strchr($newusername, $this->FieldDel) || strchr($email, $this->FieldDel) || strchr($newemail, $this->FieldDel) || strchr($password, $this->FieldDel)) {
         echo 'Fields may not contains the '.$this->FieldDel.' character';
         return false;
      } else {
         // open the file for reading, with $fp at the beginning
         $fp = @fopen($this->AuthFile, "r") or die ("Cannot open file for writing.");
         // loop through the file line by line and do some stuff
         while ($line = fgets($fp, 1024)) {
            // trim whitespace from front and end of line
            $cleanline = trim($line);
            // break $line into an array based on the $this->FieldDel delimeter
            $cleanline = explode($this->FieldDel, $cleanline);
            // clean the white space from the front/end of each element
            $tmp_usr = trim($cleanline[0]);
            $tmp_pass = trim($cleanline[1]);
            $tmp_email = trim($cleanline[2]);
            // as long as the $tmp_usr if not equal to the original username
            // we want to add the line to $new_lines array for re-writing with the
            // original record for $username left out, and a new record for $username
            // appended to the end of the file
            if ($username != $tmp_usr) {
               $lines[] = $tmp_usr.$this->FieldDel.$tmp_pass.$this->FieldDel.$tmp_email."\n";
            }
         }
         // now that we have an array of lines (minus the one we're updating) we append the updated record
         $lines[] = $newusername.$this->FieldDel.$this->setPassHash($password).$this->FieldDel.$newemail."\n";
         // close the file
         @fclose($fp);
         // and fnally, we can re-write the password file
         // open the file for writing, with $fp at the beginning; overwrite file contents
         $fp = @fopen($this->AuthFile, "w") or die ("Cannot open file for writing.");
         // lock the file for writing
         $this->FileLock($fp);
         // write the new info to the password file
         foreach ($lines as $line) {
            fputs($fp, "$line");
         }
         // unlock the file
         $this->FileUnLock($fp);
         // close the file
         @fclose($fp);
         return true;
      }
   }
   ###
   ### removes a user from the password file if $this->AuthType == 'file'
   ###
   function FileRmUser($username)
   {
      // remove any whitespace from the var
      $username = trim($username);
      // open the file for reading, with $fp at the beginning
      $fp = @fopen($this->AuthFile, "r") or die ("Cannot open file for writing.");
      // loop through the file line by line and do some stuff
      while ($line = fgets($fp, 1024)) {
         // trim whitespace from front and end of line
         $cleanline = trim($line);
         // break $line into an array based on the $this->FieldDel delimeter
         $cleanline = explode($this->FieldDel, $cleanline);
         // clean the white space from the front/end of each element
         $tmp_usr = trim($cleanline[0]);
         $tmp_pass = trim($cleanline[1]);
         $tmp_email = trim($cleanline[2]);
         // as long as the $tmp_usr if not equal to the original username
         // we want to add the line to $new_lines array for re-writing with the
         // original record for $username left out
         if ($username != $tmp_usr) {
            $lines[] = $tmp_usr.$this->FieldDel.$tmp_pass.$this->FieldDel.$tmp_email."\n";
         }
      }
      // now that we have an array of lines minus $username
      // close the file
      @fclose($fp);
      // and fnally, we can re-write the password file
      // open the file for writing, with $fp at the beginning; overwrite file contents
      $fp = @fopen($this->AuthFile, "w") or die ("Cannot open file for writing.");
      // lock the file for writing
      $this->FileLock($fp);
      // write the new info to the password file
      foreach ($lines as $line) {
         fputs($fp, "$line");
      }
      // unlock the file
      $this->FileUnLock($fp);
      // close the file
      @fclose($fp);
      return true;
   }
}
?>
