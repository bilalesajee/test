/* ---------------------------- */
/* XMLHTTPRequest Enable 		*/
/* ---------------------------- */
function createObject() 
{
	var request_type;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
	request_type = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type = new XMLHttpRequest();
	}
		return request_type;
}
var http = createObject();
/* -------------------------- */
/* SEARCH					 */
/* -------------------------- */
function suggestnow(e,id)
{
	if(e.keyCode == 40 || e.keyCode == 39 || e.keyCode == 38 || e.keyCode == 37 || e.keyCode == 13)
	{
		//40 down, 39 right,38 up, 37 left arrow key 
		scrol(e.keyCode,id);
	}
	else
	{
		autosuggest(id);
	}
}
function autosuggest(vid) 
{
	var q='';
	if( document.getElementById('productname'+vid))
	{
		q = document.getElementById('productname'+vid).value;
		document.getElementById('results').style.diaplay='block';	
	}
	// Set te random number to add to URL request
	if(q!='')
	{
		nocache = Math.random();
		http.open('get', '../includes/autocomplete/search2.php?q='+q+'&nocache = '+nocache+'&pid='+vid);
		http.onreadystatechange = autosuggestReply;
		http.send(null);
	}
	else
	{
		if(document.getElementById('results'))
		{
			var sdiv = document.getElementById('results');
			sdiv.style.display="none";
		}
	}
}
function autosuggestReply() 
{
	if(http.readyState == 4)
	{
		var response = http.responseText;
		e = document.getElementById('results');
		if(response!="")
		{
			e.innerHTML=response;
			e.style.display="block";
		} else 
		{
			e.style.display="none";
		}
	}
}
var previous, current=0,scr=0;
function scrol(eventname,xid)
{
	
	if(eventname==40)//down key
	{
		if(previous == 0 && current==0)
		{
			current = 1;
			scr=scr+0;
		}
		else
		{
			previous	=	current;
			current		=	current +1;
			scr=scr+29;
		}
	}
	else if(eventname==38 && previous!=0 )//up key
	{
		if(current>0)
		{
			current	 = current - 1;
			previous = current + 1;
			scr=scr-29;
		}
	}
	else if(eventname==13 || eventname==39)
	{
		var barcode=document.getElementById(current+'_l').className;
		var itemname=(document.getElementById('itemname_'+current).className);
		document.getElementById('productname'+xid).value=itemname;
		document.getElementById('barcode'+xid).value=barcode;
		if(barcode!='')
		{
			getitemdetails(barcode,0,xid);
		}
		previous=0, current=0,scr=0;
		emptyresults();
	}
	else if(eventname==37)
	{
		emptyresults();	
	}
	if(previous > 0)
	{
		if(document.getElementById(previous))
		{
			document.getElementById(previous).className='';
		}
	}
	if(current>0)
	{
		if(document.getElementById(current))
		{
			document.getElementById(current).className='selectedli';
		}
		else
		{
			current=1;
			previouse=current+1;
			if(eventname==13 || eventname==39)
			{
				if(document.getElementById(current))
				{
					document.getElementById(current).className='selectedli';
					scr=29;
					move_up(scr,xid);
				}
			}
		}
		move_up(scr,xid); 
	}
}//end of scroll 
function writebarcodeid(barcodeid)
{
	document.getElementById('barcodeid').value=barcodeid;
}
function move_up(to,upid) 
{
	document.getElementById('productname'+upid).scrollTop = to;
}
function getlitext(itemname,barcode,proid)
{
	document.getElementById('productname'+proid).value=itemname;
	document.getElementById('barcode'+proid).value=barcode;
	if(barcode!='')
	{
		getitemdetails(barcode,0,proid);
	}
	previous=0, current=0,scr=0;
	emptyresults();
}
function emptyresults()
{
	document.getElementById('results').innerHTML='';
	document.getElementById('results').style.diaplay='none';	
}
function setnextfocus()
{
	var expirystatus	='';
	if(expirystatus)
	{
	}
	else
	{
	}
}
function tab(to)
{
	document.getElementById(to).focus();
}
function getinstance(div,inputdata)
{
	if(inputdata=='')
	{
		if(inputdata=='')
		{
			alert("Please enter Barcode.");
			return false;
		}
		inputdata=jQuery.trim( document.getElementById('barcode1').value);
	}
	inputdata=trim(inputdata);
	document.getElementById('barcode1').value=inputdata;
}