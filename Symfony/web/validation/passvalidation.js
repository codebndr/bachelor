/* GLOBAL variables */
var emailReg = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
var nameReg	 = /^[a-zA-Z_'\-\s]*$/; //match letters/space and _ ' -  chars, empty is true

/* Check if password meets requirements */
function passvalid(pass)
{
	var regnum = /.*\d/; //number
	var reglet = /.*[a-z]/; //letters
	var regcaps = /.*[A-Z]/; //caps
	var regpunc = /.*[@#$%!^&*()\_\-\+=~<>,.?\/:;'"{}|`[\]]/; //symbols
	var regexp = [regnum, reglet, regcaps, regpunc];
	var sets = 0;

	var len = $("#"+pass).val().length;

	if (len == 0)
		return "empty"; //2
	else if(len < 6 || len > 255)
		return "outOfLimits"; //3
	else
	{	
		var value = $("#"+pass).val();
		for(var i=0 ; i < 4; i++)
		{
			if (regexp[i].test(value))
				sets++;
		}
		if (sets < 2)
			return "tooSimple"; //less than 2 charsets in pass - 4
		else
			return "valid"; //valid pass - 1
	}
};

/* Boolean wrapper for passvalid() function */
function isvalidpass(pass)
{
	var valid = passvalid(pass);
	if (valid != "valid")
		valid = false;
	else
		valid = true;
		
	return valid;
};
