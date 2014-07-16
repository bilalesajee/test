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
function suggestnow(e,acdivid,v1)
{
	
	
	if(e.keyCode == 40 || e.keyCode == 39 || e.keyCode == 38 || e.keyCode == 37 || e.keyCode == 13)
	{
		//40 down, 39 right,38 up, 37 left arrow key 
		//var len =document.getElementById('autocompletelist').length;
		//alert(len);
		//scrol(e.keyCode); //Commented by yasir 12-08-11 
		//return false;
	}
	else
	{
		//document.getElementById('results').style.display='block';
		
		autosuggest(v1);
	}
	autodivid=acdivid;
}
function autosuggest(v) 
{
	
	//alert('hello');
	var q='';
	if( document.getElementById('productname'))
	{
		q = document.getElementById('productname').value;
	}	
	//alert(document.getElementById('suggestdiv').innerHTML);
//	alert(q+' is the q'+v);
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
function scrol(eventname)
{ 
	//alert(document.getElementById('recval').value);
	var recval	=	document.getElementById('recval').value;
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
			scr=scr+(recval-1);
		}
		
		//scr=scr+29;
		
		//alert(scr);
	}
	else if(eventname==38 && previous!=0 )//up key
	{
		if(current==1)
		{
			current=recval;
			previous=current-1;
		}
		else if(current>0)
		{
			current	 = current - 1;
			previous = current + 1;
			scr=scr-(recval-1);
		}
	}
	else if(eventname==13 || eventname==39)
	{
		//var barcode	=	document.getElementById('barcodeid').value;
		var barcode=document.getElementById(current+'_l').className;
		getinstance('instancediv',barcode);
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
	 
	 // added by Yasir - 04-07-11
	 
	 if (current > recval){
	 
	 	if(document.getElementById(previous))
		{
			
			document.getElementById(previous).className='';
		}
		
		document.getElementById('1').className='selectedli';
	 }
	 
	 if (current == recval){
	  document.getElementById('1').className='';
	 }
	 
	//	
	
	
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
						
						scr=recval;
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
	//document.getElementById('barcodeid').value=barcodeid;
}
function move_up(to) 
{
	document.getElementById('productname').scrollTop = to;
}
function getlitext(current,barcode)
{
	var litext	=	document.getElementById(current).textContent;
	//var val	=	document.getElementById(current).value;
	
	document.getElementById('productname').value=litext;
	//document.getElementById('quantity').focus();
	
	//alert(barcode);
	previous=0, current=0,scr=0;
	emptyresults();
}
function emptyresults()
{
	document.getElementById('results').innerHTML='';	
}

// function added by yasir 12-08-11
function newScrol(e){
 if(e.keyCode == 40 || e.keyCode == 39 || e.keyCode == 38 || e.keyCode == 37 || e.keyCode == 13)
	{
		//40 down, 39 right,38 up, 37 left arrow key 
		//var len =document.getElementById('autocompletelist').length;
		//alert(len);
		scrol(e.keyCode);
		//return false;
	}
}