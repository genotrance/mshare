// Generic drop box set function
function dropboxSet(val, drop)
{
	var ctr;
	for (ctr = 0; ctr < drop.length; ctr++) {
		if (drop.options[ctr].text == val) {
			drop.selectedIndex = ctr;
		}
	}
}

// Set spender to value
function setSpender(val)
{
	var drop = document.expense.spender;
	dropboxSet(val, drop);
}


// Set date to val
function setDate(val)
{
	var dt = new Date();
	var ctr;
	var year;
	var drop = document.expense.elements;

	dt.setTime(val * 1000);

	for (ctr = 0; ctr < drop.length; ctr++) {
		if (drop[ctr].name == "date['day']") {
			dropboxSet(dt.getDate(), drop[ctr]);
		} else if (drop[ctr].name == "date['month']") {
			dropboxSet(dt.getMonth()+1, drop[ctr]);
		} else if (drop[ctr].name == "date['year']") {
			year = dt.getYear();
			if (dt.getYear() < 1900) year += 1900;
			
			dropboxSet(year, drop[ctr]);
		}
	}
}

// Set description to value
function setDesc(val)
{
	document.expense.description.value = val;
}

// Expand the person expense list
function togglePerson(id) {
	thisDiv = document.getElementById(id);
	
	if (thisDiv) {
		if (thisDiv.style.display == "none") {
			thisDiv.style.display = "block";
		} else {
			thisDiv.style.display = "none";
		}
	}
}

// Check password and repeat are same and not ""
function checkPassword(theform) {
	if (document.all || document.getElementById) {
		for (i = 0; i < theform.length; i++) {
			var tempobj = theform.elements[i];

			if (tempobj.type.toLowerCase() == "submit") {
				submit = tempobj;
			} else if (tempobj.type.toLowerCase() == "password") {
				if (tempobj.name == "password") {
					password = tempobj;
				} else if (tempobj.name == "passrepeat") {
					passrepeat = tempobj;
				}
			}
		}
		
		if (password && passrepeat && submit) {
			if (password.value != "" && passrepeat.value == password.value)
				submit.disabled = false;
			else
				submit.disabled = true;
		}
	}
}
