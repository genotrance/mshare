Money Share
===========

Money Share is an expense management tool that can be used keep track of
expenses  among  several  people.  Expenses made on behalf of people can be
kept track of and recovered when balancing accounts. The utility also reduces 
the number of exchanges required in order to balance the accounts.


Usage Details
=============

Persons
-------
Persons can be added and edited from the "Persons" tab. These are the persons
that can be potential spenders or accountable persons. At least one person is
required before adding expenses.

Clicking on a person's name shows all expense related information for that
person.

Deleting a person permanently deletes all expenses made by that person. The 
person is also removed from all expenses where he/she was mentioned as accountable.

Expenses
--------
Each person's expenses are added to the system from the "Expenses" tab by 
specifying date, amount spent, a brief description of the expense and a list of 
persons on behalf of who this expense was made.

All fields of an expense can be modified by using the "Edit" button.

Deleting an expense marks it as deleted in the data file. It is not displayed or
considered in any calculations after it is marked as deleted.

Expenses can be sorted by ID, Name, Amount and Description fields by clicking
on the field names. They are default sorted by date.

Default date for adding an expense is "today". Clicking on a date of an 
existing expense updates the date field to that date.

Clicking on a spender of an existing expense updates the spender field of the 
add expense form. Clicking on the description similarly updates the description
field of the add expense form.

Transactions
------------
This tab calculates a summary of payments based on the expense data. It shows
who owes who how much. It is one of the most reduced forms of payment exchanges.

Close File
----------
This function closes a data file such that no more changes can be made to the
file. This can be done after balancing accounts for example.

Renumber
--------
This function renumbers the expenses by date. Renumbering is done every time a 
new expense is added. This function is potentially useful after deleting some
expenses.

Restore
-------
This function restores the data file to the previous state. It is a global undo
of the last action performed on the data file.

Data files
----------
All files with a .xml and .bin file extension in the data/ directory are listed 
in the data file drop down box. Selecting a file from this drop down box opens 
that file in Mshare.


Major changes since 2.0
=======================

- List all expenses associated with a person in the PERSONS tab
- Close a file, no longer allow writing to it
- Expenses are only marked DELETED when deleted from the GUI. They are
  permanently deleted when the spenderID is deleted.


Major changes since 0.3
=======================

- Mshare has been rewritten in PHP. 
- All GUI based functionality of 0.3 is preserved
- Expense file format is now in XML though 0.3 format is also supported
- Expenses can now be shared among a subset of all persons
- Add/edit/delete is available for expenses as well as persons
- Sorting of expenses is available by ID, Name and Amount
- All available data files are now listed and can be seleceted from a drop-down box


Algorithm
=========

The algorithm has changed significantly since 0.3 due to the new feature that
allows a subset of persons to be involved in an expense.

- A list of all transactions between pairs of people is generated based on all
  expenses
- A -> B and B -> A transactions are minimized
- A -> B -> C and A -> C transactions are minimized
- TODO: Minimize A -> B -> C -> A transactions


Authentication
==============

Authentication is managed by the HttpAuthPlus module from
http://sf.net/projects/httpauthplus/

The password is stored in data/.mshare.db.

The initial login:password pair is user:pass. This can be changed with the
adduser.php script with the following syntax:

http://yourhost/adduser.php?user=username&pass=password

To ensure that the password file or the expense files are not accessible over
the web, an .htaccess file is present in the data/ directory. Please verify
that apache is configured to "AllowOverride Limit" to enable this option.


File Format
===========

Version 2.0
-----------

<mshare version="">
  <persons>
    <person name="" ID=""/>
    ..
  </person>

  <expenses>
    <expense ID="" spenderID="" accountableIDs="" expenseDate="" amount="" description=""/>
    ..
  </expenses>
</mshare>

Eg:

<mshare version="2.0">
  <persons>
    <person name="P1" ID="1"/>
    <person name="P2" ID="2"/>
    <person name="P3" ID="3"/>
  </person>

  <expenses>
    <expense ID="1" spenderID="1" accountableIDs="2:3" expenseDate="123412341234" amount="10.25" description="Pepsi"/>
    <expense ID="2" spenderID="1" accountableIDs="2" expenseDate="123412341234" amount="100" description="Clothes"/>
    <expense ID="3" spenderID="2" accountableIDs="1:2:3" expenseDate="123412341234" amount="1000" description="Rent"/>
  </expenses>
</mshare>

Vesion 0.3
----------

N
ID:Person1
..
..
ID:PersonN
EID:ID:Date:Expense Value:Expense Description

  where Expense Description is optional

Eg: 

2
16:Sam
17:Walter
0:0:Oct 6, 2002:55.78:Groceries
1:1:Oct 7,2002:5.99:Fuel

Extensions
----------

The data files are stored in the data/ directory and end in .xml. A backup file
is expected with a .xml.bak extension with the same filename. Both files need
to have write permission for the webserver's user ID.
