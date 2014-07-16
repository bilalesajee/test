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
var autodivid='';
function suggestnow(e,acdivid)
{
	
	if(e.keyCode == 40 || e.keyCode == 39 || e.keyCode == 38 || e.keyCode == 37 || e.keyCode == 13)
	{
		//40 down, 39 right,38 up, 37 left arrow key 
		//var len =document.getElementById('autocompletelist').length;
		//alert(len);
		scrol(e.keyCode);
		//return false;
	}
	else
	{
		//document.getElementById('res1').style.display='block';
		
		autosuggest();
	}
	autodivid=acdivid;
}
function autosuggest() 
{
	
	
	var q='';
	if( document.getElementById('productname1'))
	{
		q = document.getElementById('productname1').value;
	}
	// Set te random number to add to URL request
	if(q!='')
	{
		nocache = Math.random();
		http.open('get', 'includes/autocomplete/search.php?q='+q+'&nocache = '+nocache);
		http.onreadystatechange = autosuggestReply;
		http.send(null);
	}
	else
	{
		//alert('i am empty');
		if(document.getElementById('res1'))
		{
			var sdiv = document.getElementById('res1');
			sdiv.style.display="none";
		}
	}
}
function autosuggestReply() 
{
	if(http.readyState == 4)
	{
		var response = http.responseText;
		e = document.getElementById('res1');
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
function scrol(eventname)
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
			//var position	=	document.getElementById(current).offsetTop;
			scr=scr+29;
		}
		
		//scr=scr+29;
		
		//alert(scr);
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
		//var barcode	=	document.getElementById('barcodeid').value;
		var barcode=document.getElementById(current+'_l').className;
		var itemname=document.getElementById(current+'_it').className;
		itemname	=	itemname.trim();
		barcode	=	barcode.trim();
		
		//getinstance('instancediv',barcode);
		//document.getElementsById(current+'_l').click();
		getlitext(current);
		
		
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
						move_up(scr);
					}
				}
			}
			//alert(current);
			//var barcode=document.getElementById(current).value;
			//alert(barcode);
			move_up(scr); 
		}
	
}//end of scroll 
function writebarcodeid(barcodeid)
{
	document.getElementById('barcodeid').value=barcodeid;
}
function move_up(to) 
{
	document.getElementById('productname1').scrollTop = to;
}
function getlitext(current)
{
	//var litext	=	document.getElementById(current).textContent;
	//var val	=	document.getElementById(current).value;
	//litext=litext.trim();
	//document.getElementById('productname1').value=litext;
		var barcode=document.getElementById(current+'_l').className;
		var itemname=document.getElementById(current+'_it').className;
		//itemname	=	itemname.replace("_", " ");
		itemname	=	itemname.trim();
		barcode	=	barcode.trim();
		document.getElementById('productname1').value=itemname;
		document.getElementById('itembarcode').value=barcode;
	//document.getElementById('quantity').focus();
	
	//alert(barcode);
	previous=0, current=0,scr=0;
	emptyresults();
}
function emptyresults()
{
	document.getElementById('res1').innerHTML='';	
}
function getinstance()
{
	
}