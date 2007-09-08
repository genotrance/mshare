Money Share
===========

Share the monthly expenses  among  several  people  equally.  Each person
spends  on  different  items,  not  necessarily predetermined. At the end of
the month,  total  expenses  of each person are added  up  and  equally
divided  among  the people.  How  much  each  person  owes  the  other  can
be calculated.


Major changes since 0.3
=======================

- Mshare has been rewritten in PHP. 
- All GUI based functionality of 0.3 is preserved
- Expense file format is now in XML though 0.3 format is also supported
- Expenses can now be shared among a subset of all persons
- Add/edit/delete is available for expenses as well as persons
- Sorting of expenses is available by ID, Name and Amount
- All available data files are now listed and can be seleceted from a drop-down box


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
