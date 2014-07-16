//Valid Date Check
function isValidDate(s) {
	// format D(D)/M(M)/(YY)YY
	var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;
	if (dateFormat.test(s)) {
		// remove any leading zeros from date values
		s = s.replace(/0*(\d*)/gi,"$1");
		var dateArray = s.split(/[\.|\/|-]/);
		// correct month value
		dateArray[1] = dateArray[1]-1;
		// correct year value
		if (dateArray[2].length<4) {
			// correct year value
			dateArray[2] = (parseInt(dateArray[2]) < 50) ? 2000 + parseInt(dateArray[2]) : 1900 + parseInt(dateArray[2]);
		}
		var testDate = new Date(dateArray[2], dateArray[1], dateArray[0]);
		if (testDate.getDate()!=dateArray[0] || testDate.getMonth()!=dateArray[1] || testDate.getFullYear()!=dateArray[2]) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
function checkval(abc,p)
{
	if(p==1)
	{
		var rpp	=	abc.split('~~');
		var str	=	rpp[1].split('&page=');
		var abc	=	str[0]+'&pagelimit='+rpp[0]+'&page='+str[1];
	}
	//alert(strnew);
	eval(abc);
}
//added from store_common.js by ahsan 21/02/2012
//Load Item name for stocks screen
function loaditemname(bcid)
{
	jQuery('#itemname').load('getitemname.php?bcid='+bcid);
}//end add
// JavaScript Document
function loaddetail(div)
{
	$('#'+div).modal();	
}
var selectedstring	='';

function setselected(i,div)
{
//	alert('selected'+div);
//window['selected' + div]	= 'hello';
//alert(window['selected' + div]);
	var c	=	document.getElementById('cb_'+i+'_'+div);
	if (c.checked == false)
	{
		selectedstring += ','+ i;
		window['selected' + div]	+= ','+ i;
		//eval('selected'+div)	+= ','+ i;	
	}
	else
	{
		selectedstring = selectedstring.replace(','+i,"");
		window['selected' + div]= window['selected' + div].replace(','+i,"");
		//eval('selected'+div) = eval('selected'+div).replace(','+i,"");
	}
}
//added from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function setselected_main(i,div,chkAll)
{
//	alert('selected'+div);
//window['selected' + div]	= 'hello';
//alert(window['selected' + div]);
	var c	=	document.getElementById('cb_'+i+'_'+div);
	if(chkAll==1)
	{
		if(c.checked==false)
		{
			selectedstring += ','+ i;
			window['selected' + div]	+= ','+ i;
		}
		selectedstring = selectedstring.replace(','+i,','+i);
		window['selected' + div]= window['selected' + div].replace(','+i,','+i);
	}
	else if(chkAll==2)
	{
		selectedstring = selectedstring.replace(','+i,'');
		window['selected' + div]= window['selected' + div].replace(','+i,'');
	}
	else
	{
		if (c.checked == false)
		{
			selectedstring += ','+ i;
			window['selected' + div]	+= ','+ i;
			//eval('selected'+div)	+= ','+ i;	
		}
		else
		{
			selectedstring = selectedstring.replace(','+i,"");
			window['selected' + div]= window['selected' + div].replace(','+i,"");
			//eval('selected'+div) = eval('selected'+div).replace(','+i,"");
		}
	}
}
//end edit
//end add
function getselected(div)
{
	var selectedarray	=	new Array();
	selectedarray		=	window['selected' + div].split(',');
//	selectedarray		=	selectedstring.split(',');
	return (selectedarray);
}
/***********************************************loading()*********************************/
function loading(text)
{
	selectedstring = "";
	$("#loading").ajaxStart(function()
	{
    	document.getElementById('loading').innerHTML=text;
		$(this).show();
 	});
	$("#loading").ajaxStop(function()
	{
   		$(this).hide();
	 });
}
/***********************************************highlight()*********************************/
function highlight(id,clas,ev,cdiv)
{
	
	if(ev=='row')
	{
		setselected(id,cdiv);//set the selected check boxes array
	}
	var cb	=	document.getElementById('cb_'+id+'_'+cdiv);
	//alert(cb);
	
	if(cb.checked == false)
	{
		
		document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
		
		cb.checked		=	true;
		//viewsuppliers	=	id;
	}
	else
	{
		//viewsuppliers=0;
		document.getElementById('tr_'+id+'_'+cdiv).className=clas;
		cb.checked = false;
	}
	if(ev=='chk')
	{
			
		if(cb.checked == false)
		{
			//viewsuppliers=0;
			document.getElementById('tr_'+id+'_'+cdiv).className=clas;
			cb.checked = false;
		}
		else
		{
			document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
			cb.checked		=	true;
			//viewsuppliers	=	id;
		}
			
	}
}  
/***********************************************loadsuppliers()*********************************/
function loadsubgrid(div,checks,url,cdiv)
{
//	alert(div+checks+url+cdiv);
	var selectedbrands	=	getselected(cdiv);
	var sb;
	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		var sb1	=	sb.split(cdiv);
		prepareforedit(checks, sb,cdiv);
		$('#'+div).load(url+'?id='+sb1[0]);
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
	//$('#'+div).load(url+'?id='+id);
	
	// document.getElementById(div).style.backgroundColor="#F90";
	// document.getElementById(cdiv).style.backgroundColor="#fff";
}//loadsuppliers
/***************************************************getsuppliers()**************************************************/
function getgrid(page,checks,tabid,cdiv,param)
{
	var selectedbrands	=	getselected(cdiv);
	var sb;

	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		prepareforedit(checks, sb,cdiv);
		//jQuery("#"+div).load(page+'?id='+sb);
		//alert(sb);
		var sb1	=	sb.split(cdiv);
		//alert(sb1[0]+"--"+param)
		selecttab(tabid,page+'?id='+sb1[0]+'&param='+param);
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
}//getsuppliers	

/*****************************************showbrandform()**************************************************/
var olddiv="";
/*//add comment by ahsan 24/02/2012// function showpage(clickedon,cbfield,page,div,cdiv,param)
{
	
	//alert(clickedon+'='+cbfield);
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	var show = 1;
	//alert(selectedbrands);
	if(clickedon == '1')
	{
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			show = 0;
			
		}//else
	}
	else if (clickedon =='2')
	{
		
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
			//	alert(i+'---'+selectedbrands[i]);
				 sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
		}
		else
		{
			id	= '-1';
		}
	}
	else
	{
		id	=	'-1';
	}
	/*************************SHOW !=0**************/
/*//add comment by ahsan 24/02/2012// 	if (show!=0)
	{
		jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
		
	 //document.getElementById(div).className='divborder';
	// document.getElementById(cdiv).className='divborderkhali';
	// olddiv=div;
	}
	// alert(div);
	
	 //document.getElementById(div).style.backgroundColor="#FFFFCC";
	 //document.getElementById(cdiv).style.backgroundColor="#fff";
}//editform
*///add comment by ahsan 24/02/2012// 
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function showpage(clickedon,cbfield,page,div,cdiv,param,pagetype)
{
	//alert(clickedon+'='+cbfield);
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	var show = 1;
	//alert(selectedbrands);
	if(clickedon == '1')
	{
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			show = 0;
			
		}//else
	}
	else if (clickedon =='2')
	{
		
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
			//	alert(i+'---'+selectedbrands[i]);
				 sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
		}
		else
		{
			id	= '-1';
		}
	}
	else
	{
		id	=	'-1';
	}
	/*************************SHOW !=0**************/
//alert(id);
//alert('jafer');
//return;
	if(pagetype!='f' || pagetype=='')
	{
		if (show!=0)
		{
			jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
		}
		$('#'+div).fadeIn(1500);
	}
	else
	{	//opens the dialogbox for forms
		var targetpage=page+'?id='+id+'&param='+param;
		//jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
		showdialog('',targetpage)
	}

}//editform
//end edit

/*/////////////////ADDED By FAHAD 17-04-2012/////////////////////////////////////////////////*/
/*****************************************showbrandform()**************************************************/
function showpage_acc(clickedon,cbfield,page,div,cdiv,param)
{
	
	//alert(clickedon+'='+cbfield);
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	var show = 1;
	//alert(selectedbrands);
	if(clickedon == '1')
	{
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			show = 0;
			
		}//else
	}
	else if (clickedon =='2')
	{
		
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
			//	alert(i+'---'+selectedbrands[i]);
				 sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
		}
		else
		{
			id	= '-1';
		}
	}
	else
	{
		id	=	'-1';
	}
	/*************************SHOW !=0**************/
	if (show!=0)
	{
		jQuery("#"+div).load(page+'&id='+id+'&param='+param);	
		
	 //document.getElementById(div).className='divborder';
	// document.getElementById(cdiv).className='divborderkhali';
	// olddiv=div;
	}
	// alert(div);
	
	 //document.getElementById(div).style.backgroundColor="#FFFFCC";
	 //document.getElementById(cdiv).style.backgroundColor="#fff";
}//editform
/*///////////////////////////////////////////////////////////////////*/







function selectallrecords(page,div,param)//coding by jafer balti
	{
		var selectedfields	=	getselected(div);
		var totalrecords	=	(selectedfields.length)-1;
		requiredfields='';
		var msg='Are you sure you want to print';

		if( totalrecords > 0)
		{
			if(confirm(msg+' ('+totalrecords+') record(s)?'))
			{

				for (i = 0; i < selectedfields.length; i++)
				{
				//	var v	=	br1[i].value;
					requiredfields	+=	','+selectedfields[i];
				}//for
				//alert(requiredfields);
				window.open(page+'?id='+requiredfields+'&param='+param,"Print Tag","width=140,height=100")				
			}//if confirm
		
		}
		else
		{
			alert("Please select at least one record.");
		}
	}
	
function selectallpayeerecords(page,div,param)//coding by jafer balti for payee accounts
	{
		var selectedfields	=	getselected(div);
		var totalrecords	=	(selectedfields.length)-1;
		requiredfields='';

		if( totalrecords > 0)
		{
				for (i = 0; i < selectedfields.length; i++)
				{
				//	var v	=	br1[i].value;
					requiredfields	+=	','+selectedfields[i];
				}//for
				//alert(requiredfields);
				jQuery("#subsection").load(page+'?id='+requiredfields+'&param='+param);					
		}
		else
		{
			alert("Please select at least one record.");
		}
	}
		
//from main, start edit by ahsan 13/02/2012
function chkadvancesearch(div,param)
{
		//builds the query string of advance search
		//this function is called on paging action ,sorting, Advance serach 
		
		
		if(param=='filter')
		{
			var elm='advancedatafilterfrm'+div;
			var formname='advancedatafilterfrm'+div;
		}
		else
		{
			var elm='advancesearch'+div;
			var formname='advancesearchform'+div;
		}
		//alert(param);
		if(document.getElementById(elm).style.display=='block')
		{
			
			var len=formname.length;
			var fel	=	document.getElementById(formname);
			var ck=2;
			var searchField='';
			var searchOper='';
			var searchString='';
			var compound='';
			var totalsearchfields='';
			var qs='';
			var str;
			var v=2;
			var compound='';
			var	indexscompound='';
			//alert(len);
			if(param!='filter')
			{
				
				for(var x=1;x<(fel.length-1);x++)
				{
					//will get the name and value of each element and build the query string
					//searchField	=	
					//alert(fel[ck].name+'='+fel[ck].value);
					
					if(fel[ck].value!='')
					{
						var searchStringName	=	fel[ck].name;
						searchString			=	encodeURIComponent(fel[ck].value);
						indexsearchField		=	'searchField'+x;
						
						searchField				=	document.getElementById(indexsearchField).value;
						
						indexsearchOper			=	'searchOper'+x;
						searchOper				=	document.getElementById(indexsearchOper).value;
						
						
						indexscompound		=	'compound'+v;
						v++;
						//alert(indexscompound);
						var ic	=	eval('document.'+formname+'.'+indexscompound);
						
						if(ic[1].checked==true)
						{
							//alert('OR');
							compound='OR';
						}
						else
						{
							//alert('AND');
							compound='AND';
						}
						qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';
					}//for
					ck+=5;
					if(fel.length<=ck)
					{
						x=ck;
						//return true;	
					}
				}
			}//if !filter
					//alert(len);
				if(param=='filter')
				{
					for(var x=0;x<(fel.length-1);x++)
					{
							indexsearchField		=	'searchField'+x;
							var ty	=	fel[x].type
							//alert(ty);
							if(ty=='radio')
							{
								if(ic==true)
								{
									compound='OR';
									indexscompound=fel[ck].name+''+x;
								}
								else
								{
									compound='AND';
									indexscompound=fel[ck].name+''+x;
								}	
								
								x++;
							}
							else
							{
								searchStringName		=	fel[x].name;
								searchString			=	encodeURIComponent(fel[x].value);
								indexsearchOper			=	'searchOper'+x;
								searchOper				=	'cn';
							}
							
							qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';	
						
					
				}//for
			//	alert(qs);
				return false;
			}//if param
				//return false;
		//	alert(qs);
			if(qs!='' && qs!='&')
			{
				totalsearchfields=document.getElementById('totalsearchfields').value;
				qs+='totalsearchfields='+totalsearchfields;
				return(qs);
			}
			else
			{
				qs	=	qs.trim('&');
				return (qs);	
			}
		}//if style check
}
function resetsearchform(div,page)//will reset the Advance search form 
{
	var formname='advancesearchform'+div;
	jQuery('#'+div).load(page);
	//alert('abc');
	
	if(document.getElementById('advancesearch'+div))
	{
		//alert(div);
		//jQuery().show();
		document.getElementById('advancesearch'+div).style.display=='block'	;
	}
}	
//end edit
//end add
/*************************************************call_ajax_paging()*************************************************/
function call_ajax_paging(string,dest,div,str)// cals the ajax paging "Loads the section again with page paramater"
{ 
	str				=	encodeURIComponent(str);  ///////Function Updated By Fahad 5-4-2012///////
	if(str==''){
	jQuery('#'+div).load(dest+'?'+string);
	}else{
	var pagelimit	=	document.getElementById('pagelimit').value;
	var rpp	=	pagelimit.split('~~');  	
	jQuery('#'+div).load(dest+'?'+string+'&searchString='+str+'&pagelimit='+rpp[0]);
		}
}
//add from store_common.js by ahsan 21/02/2012
/*************************************************call_ajax_paging()*************************************************/
function call_ajax_paging_store(string,dest,div,str)// cals the ajax paging "Loads the section again with page paramater"
{
	str				=	encodeURIComponent(str);
	var pagelimit	=	document.getElementById('pagelimit').value;
	jQuery('#'+div).load(dest+'?'+string+'&searchString='+str+'&pagelimit='+pagelimit);
}
//end add
/*************************************************call_ajax_sort()*************************************************/
function call_ajax_sort(field,order,page,urlpage,div)
{
	var param='';
	if(page)
	{
		param='&page='+page;
	}
	jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page);
}
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
/*************************************************call_ajax_sort()*************************************************/
function call_ajax_sort_main(field,order,page,urlpage,div)
{
	var advancesearchstr	=	chkadvancesearch(div);
	var param='';
	if(page)
	{
		param='&page='+page;
	}
	jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page+'&'+advancesearchstr);
}
//end edit
//end add
/*************************************************showhide()*************************************************/
function showhide(id,lin)
{
		var cur	=	document.getElementById(id).style.display;
		if(cur == 'none')
		{
			document.getElementById(id).style.display='block';
			document.getElementById(lin).innerHTML='Hide';
			
		}
		else
		{
			document.getElementById(id).style.display='none';
			document.getElementById(lin).innerHTML='Show';
		}
}
/**************************************************************************************************************/
function prepareforedit(field, dontchangethis,cdiv)
{
	//alert(cdiv);
	for (i = 0; i < field.length; i++)
	{
		var cb	=	field[i].id;
		var	ab	=	cb.split('_');
		var tr	=	document.getElementById('tr_'+ab[1]+'_'+cdiv);
		if (ab[1] != dontchangethis)
		{
			field[i].checked = false;
			
			if(tr.className == 'even' || tr.className == 'selectedeven' )
			{
				tr.className	=	'even';
			}
			else
			{
				tr.className	=	'odd';
			}
		}
	}//for
}
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function toggleChecked(status) {
$(".checkbox").each( function() {
$(this).attr("checked",status);
})
}
//end edit
//end add
function checkAll(currentcheckbox,field,cdiv)
{
	if(currentcheckbox.checked == true )
	{
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			setselected(ab[1],ab[2]);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'selectedeven';
			}
			else
			{
				tr.className	=	'selectedodd';
			}
			
			field[i].checked = true ;
		}//for
	}//if
	else
	{
		selectedstring = "";
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'even';
			}
			else
			{
				tr.className	=	'odd';
			}
			field[i].checked = false ;
		}//for
	}
	
}//  End -->
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function checkAll_main(currentcheckbox,field,cdiv)
{
	//alert(field);
	if(currentcheckbox.checked == true )
	{
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			setselected(ab[1],ab[2],1);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'selectedeven';
			}
			else
			{
				tr.className	=	'selectedodd';
			}
			
			field[i].checked = true ;
		}//for
	}//if
	else
	{
		selectedstring = "";
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			setselected(ab[1],ab[2],2);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'even';
			}
			else
			{
				tr.className	=	'odd';
			}
			field[i].checked = false ;
		}//for
	}
	
}//  End -->
//end edit
//end add
function buttonmouseover(id)
{
		document.getElementById(id).className ='button3';
}
function buttonmouseout(id)
{
		document.getElementById(id).className ='button2';
}
function create_crumb(crumb)
{
	$('#breadcrumbs').html(crumb);
}
var brandsfordelete='';
	function deleterecords(page,div,qs,param)
	{
		var selectedbrands	=	getselected(div);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		var msg='';
		var act='';
		if(param=='confirm')
		{
			 msg='Are you sure to CONFIRM selected';
			 act="CONFIRM.";
		}
		else
		{
			 msg='Are you sure to DELETE selected';	
			  act="DELETE.";
		}
		if( totalrecords > 0)
		{
			
			if(confirm(msg+' ('+totalrecords+') record(s)?'))
			{
				//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
				//	var v	=	br1[i].value;
					brandsfordelete	+=	','+selectedbrands[i];
				
					
				}//for
				//alert(brandsfordelete);
				
				jQuery('#'+div).load(page+'?'+qs+'&oper=del&id='+brandsfordelete+'&param='+param);
				
				//jQuery('#'+div).show();
				//alert(document.getElementById(div).innerHTML);
				//alert(jQuery('#'+div).html());
				//.jQuery("#loading")
				//alert('a');
			}//if confirm
		
		}//if selected brands
		else
		{
			alert("Please select at least one record to "+act);
		}
	}
//add from store_common.js by ahsan 21/02/2012
	/*******************************************************************************/
	function changeproduct(page,div1,div2)
	{
		var selectedbrands	=	getselected(div1);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		if( totalrecords == 0)
		{
			alert("Please select at least one row.");
			return;
		}//if selected brands
		jQuery('#'+div2).load(page+'?'+'id='+selectedbrands);
	}
	
	//from main, start edit by ahsan 13/02/2012
		/*******************************************************************************/
	function changeproduct_main(page,div1,div2,param)
	{
		var selectedbrands	=	getselected(div1);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		if( totalrecords == 0)
		{
			alert("Please select at least one row.");
			return;
		}//if selected brands
		jQuery('#'+div2).load(page+'?'+'id='+selectedbrands+'&param='+param);
	}
	//marks the items as fixed and wrong
	function markitems(page,div1,div2,status,param)
	{
		var selectedbrands	=	getselected(div1);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		if( totalrecords == 0)
		{
			alert("Please select at least one row.");
			return;
		}//if selected brands
		jQuery('#'+div2).load(page+'?'+'id='+selectedbrands+'&status='+status+'&mark=1&param='+param);
	}
//end edit

	function displayrecords(page,dispdiv,maindiv,qs,param)
	{
		var totalrecords	=	(selectedstring.length)-1;
		if( totalrecords > 0)
		{
			jQuery('#'+dispdiv).load(page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param);
		}//if selected brands
		else
		{
			alert("Please select at least one record to display.");
		}
	}
	//from main, start edit by ahsan 13/02/2012
		function displayrecords_main(page,dispdiv,maindiv,qs,param,pagetype)
	{
		
		var totalrecords	=	(selectedstring.length)-1;
		
		

		if( totalrecords > 0)
		{
			if(pagetype!='f' || pagetype=='')
			{
				
				jQuery('#'+dispdiv).load(page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param);
				//jQuery("#"+div).load(page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param);	
				
					//$('#'+div).fadeIn(1500);
			}
			else
			{
				var targetpage=page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param;
				//jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
				showdialog('',targetpage)
			}
		}//if selected brands
		else
		{
			alert("Please select at least one record to display.");
		}
	}
//end edit
//end add	
	function submitchecks(page,div,cdiv)
	{
	
		var selectedbrands	=	getselected(cdiv);
		var totalrecords	=	(selectedbrands.length)-1;
		
		//alert(selectedbrands);
			//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
					selectedbrands[i]=selectedbrands[i].replace(cdiv,'');
					brandsfordelete	+=	','+selectedbrands[i];
				}//for
				brandsfordelete=brandsfordelete.replace(',,','');
				//alert(brandsfordelete);
				jQuery('#'+div).load(page+'?id='+brandsfordelete);
	}
/***********************************************************SEARCH AREA********************************************/	
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function decode(str)
{
	return inputString	=	unescape(str);
}
function encode(str)
{
	var encodedInputString=escape(str);
	encodedInputString=encodedInputString.replace("+", "%2B");
	encodedInputString=encodedInputString.replace("/", "%2F");        
	return encodedInputString;
}
//end edit
//end add
function searchgrid(div,page)
{
		/*var searchField		=	document.getElementById('searchField'+div).value;
		var searchString	=	document.getElementById('searchString'+div).value;
		searchString		=	encodeURIComponent(searchString);
		var searchOper		=	document.getElementById('searchOper'+div).value;
		var id	=	document.getElementById('id'+div).value;
		$('#'+div).load(page+'?_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id);*/
		var searchField		=	document.getElementById('searchField'+div).value;
		var searchString	=	document.getElementById('searchString'+div).value;
		searchString		=	encodeURIComponent(searchString);
		var searchOper		=	document.getElementById('searchOper'+div).value;
		var param			=	document.getElementById('param').value;
		var id				=	document.getElementById('id'+div).value;
		$('#'+div).load(page+'?_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id+'&param='+param);
}
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by Ahsan 13/02/2012
function chkadvancedatafilter(div,param)
{
		//builds the query string of advance search
		//this function is called on paging action ,sorting, Advance serach 
		
		
		if(param=='filter')
		{
			var elm='advancedatafilterfrm'+div;
			var formname='advancedatafilterfrm'+div;
			
		}
		else
		{
			var elm='advancesearch'+div;
			var formname='advancesearchform'+div;
			totalsearchfields=document.getElementById('totalsearchfields').value;
		}
		//alert(param);
		if(document.getElementById(elm).style.display=='block')
		{
			
			var len=formname.length;
			var fel	=	document.getElementById(formname);
			var ck=2;
			var searchField='';
			var searchOper='';
			var searchString='';
			var compound='';
			var totalsearchfields='';
			var qs='';
			var str;
			var v=1;
			
			//alert(len);
			
				
				for(var x=1;x<(fel.length-1);x++)
				{
					//will get the name and value of each element and build the query string
					//searchField	=	
					//alert(fel[ck].name+'='+fel[ck].value);
					var searchStringName	=	fel[ck].name;
					//alert(searchStringName+'====id '+x);
					
						var searchStringName	=	fel[ck].name;
						searchString			=	encodeURIComponent(fel[ck].value);
					
						if(param=='filter')
						{
							var txtboxfilter	=	document.getElementsByClassName('searchfieldfiltertextbox');
							//alert(txtboxfilter.length);
							searchStringName		=	'searchString'+x;
							//indexsearchName			=	'searchFieldName'+x;
							//searchString			=	encodeURIComponent(fel[ck].value);
							//var searchStringName	=	fel[ck].name;
							//searchString			=	encodeURIComponent(fel[ck].value);
							indexsearchField		=	'searchFieldFilter'+x;
							indexsearchName			=	'searchFieldName'+x;
							//alert(indexsearchField);
							if(txtboxfilter.length>=x)
							{
								//alert(indexsearchField);
								searchString			=	encodeURIComponent(document.getElementById(indexsearchField).value);
								searchField				=	document.getElementById(indexsearchName).value;
							}
							indexsearchOper			=	'searchOperFilter'+x;
							if(document.getElementById(indexsearchOper))
							{
								searchOper				=	document.getElementById(indexsearchOper).value;
								
							}
							indexscompound			=	'compoundFilter'+parseInt(v);
						//	alert(indexscompound);
							indexsearchField		=	'searchField'+x;
							indexsearchName			=	'searchField'+x;
							indexsearchOper			=	'searchOper'+x;
							totalsearchfields=document.getElementById('totalsearchfieldsfilter').value;

							//alert(indexscompound);
							//if(v<=txtboxfilter.length)
							//{
								
							//}
						}
						else
						{
							
							//if(fel[ck].value!='')
							//{
								indexsearchField		=	'searchField'+x;
								searchField				=	document.getElementById(indexsearchField).value;
								indexsearchOper			=	'searchOper'+x;
								if(document.getElementById(indexsearchOper))
								{
									searchOper				=	document.getElementById(indexsearchOper).value;
									
								}
								indexscompound			=	'compound'+parseInt(v+1);
	
							//}
							/*var ic	=	eval('document.'+formname+'.'+indexscompound);
							indexscompound			=	'compound'+v;*/
						}
						v++;
						//alert(indexscompound);
						
						
							//alert("hello");
							//ic='';	
						
						//alert(ic);
						//var cmpname	=	ic[1].name;
						//return false;
						//alert(document.getElementsByTagName('radio').length);
						//chk=chk+5;
						
						var len=fel.length-4;
						//alert(len+'-----'+ck);
						if(ck<=len)
						{
								var ic	=	eval('document.'+formname+'.'+indexscompound);
							//alert(ic[1].id);
							
								if(ic[1].checked==true)
								{
									compound='OR';
								}
								else
								{
									compound='AND';
								}
								indexscompound	=	'compound'+v;
									
								
							}
						
						
						//alert(indexscompound);
						qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';
						ck+=5;
						if(fel.length<=ck)
						{
							x=ck;
							//return true;	
						}
					}//for
					/*ck+=5;
					if(fel.length<=ck)
					{
						x=ck;
						//return true;	
					}*/
			
				
			if(qs!='' && qs!='&')
			{
				//alert(qs);
				qs+='totalsearchfields='+totalsearchfields+'&filter='+param;
				return(qs);
			}
			else
			{
				qs	=	qs.trim('&');
				return (qs);	
			}
			
			//return false;
		}//if style check
}
var resdiv;
/*function advancesearchgrid(div,page)
{
	
	var advancesearchqs	=	chkadvancesearch(div);
	jQuery('#'+div).load(page+'?'+advancesearchqs);	
}
*/
function advancesearchgrid(div,page,frm,para)
{
	var advancesearchqs	='';
	if(para=='filter')
	{
		advancesearchqs	=	chkadvancedatafilter(div,para);
	}
	else
	{
		advancesearchqs	=	chkadvancesearch(div,para);
	}
	jQuery('#'+div).load(page+'?'+advancesearchqs);	
}
/******************************************************************************************************************/
//end edit
//end add
/******************************************************************************************************************/
function checkkey(event,div,page)
{
	if(event.keyCode == 13)	
	{
		searchgrid(div,page);
		return false;
	}
	
}
/********************************************************************/
function numbersonly(e, decimal) {
var key;
var keychar;

if (window.event) {
   key = window.event.keyCode;
}
else if (e) {
   key = e.which;
}
else {
   return true;
}
keychar = String.fromCharCode(key);

if ((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) ) {
   return true;
}
else if ((("0123456789").indexOf(keychar) > -1)) {
   return true;
}
else if (decimal && (keychar == ".")) { 
  return true;
}
else
   return false;
}
function clearfield(val,targetfield)
{
	if(val!='')
	{
		document.getElementById(targetfield).value='';
		document.getElementById(targetfield).readOnly =true;
	}
	else
	{
		document.getElementById(targetfield).readOnly =false;
		document.getElementById(targetfield).value='Add New';
	}
}
var previns='';
function viewstoredetails(rowid)
{
	var dis	=	document.getElementById(rowid+'_detail').style.display;	
	if(dis=='block')
	{
			document.getElementById(rowid+'_link').src="images/max.gif";	
			 $('#'+rowid+'_detail').slideUp("slow");
			//document.getElementById(rowid+'_detail').style.display='none';	
	}
	else
	{
		document.getElementById(rowid+'_link').src="images/min.gif";
		//document.getElementById(rowid+'_detail').style.display='block';	
		 $('#'+rowid+'_detail').slideDown("slow");	
	}
	//tole the previouse
	//alert(previns+'=='+rowid);
	if(previns==rowid)
	{
		previns='';	
	}
	if(previns!='')
	{
		var disp	=	document.getElementById(previns+'_detail').style.display;	
		if(dis==disp)
		{
			if(dis=='none')
			{
				disp='block';	
			}
			else
			{
				disp='none';	
			}
		}
		if(disp=='block')
		{
				document.getElementById(previns+'_link').src="images/max.gif";	
				 $('#'+previns+'_detail').slideUp("slow");
				//document.getElementById(rowid+'_detail').style.display='none';	
				
		}
		else
		{
			document.getElementById(previns+'_link').src="images/min.gif";
			//document.getElementById(rowid+'_detail').style.display='block';	
			 $('#'+previns+'_detail').slideDown("slow");	
		}
	}
	previns=rowid;
}
function hidediv(divid)
{
	//document.getElementById(olddiv).className='divborderkhali';
	jQuery('#'+divid).html('');	
	
}
//add from store_common.js by ahsan 21/02/2012
//from main, start edit by ahsan 13/02/2012
function hidediv_main(divid,pagetype)
{
	//document.getElementById(olddiv).className='divborderkhali';
//	jQuery('#'+divid).html('');
	if(pagetype=='')
	{
		$('#'+divid).slideUp(500);
		$('#subsection').slideUp(500);
	}
	else
	{
		$('#dialog-form').dialog( "close" );	
	}
	$('#'+divid).hide();
	//document.getElementById("subsection").style.display	=	'none';
	
}
function hidedialog(did)
{
	$('#'+did).dialog( "close" );	
}
//end edit
//end add
function toggleitem(id)
{
	var dis	=	document.getElementById(id).style.display;
	if(dis=='none')
	{
		//document.getElementById(id).style.display='block';
		 $('#'+id).slideDown("slow");
		document.getElementById(id+'_img').src="images/min.gif";
		return false;
	}
	else
	{
		document.getElementById(id+'_img').src="images/max.gif";
		$('#'+id).slideUp("slow");
		//document.getElementById(id).style.display='none';
		return false;
	}
}

function adminnotice(text,cancel,timelimit)
{

	if(cancel=='1')
	{
		jQuery('#msg').hide();
	}
	else
	{
		//line added from store_common.js by ahsan 21/02/2012
		document.getElementById('msg').style.display	=	'block';//from main, edit by Ahsan 13/02/2012
		text	="<div id=error class=notice>"+text+"    <a href='javascript:adminnotice(0,1,0)'><img src='../images/min.GIF' border='0' /></a></div>";
		jQuery('#msg').show();//line added from store_common.js by ahasn 21/02/2012
		jQuery('#msg').html(text);
		jQuery('#msg').fadeOut(5000);	
		//line added from store_common.js by ahsan 21/02/2012
		jQuery('#error').delay(timelimit).fadeOut(1000);	//from main, edit by ahsan 13/02/2012

	}
}
/*function adminnotice(text,cancel,timelimit)
{
	//alert(cancel);
	if(cancel==1)
	{
		alert(cancel);
		//document.getElementById('notice').style.display='none';
		jQuery('#error').hide();
		//return false;
	}
	else
	{
		document.getElementById('error').style.display='block';
		text=text+" <a href='javascript:notice(0,1,0)'><img src='images/min.GIF' border='0' /></a>";
		jQuery('#error').html(text);
		jQuery('#error').fadeOut(timelimit);	
	}
}*/
function trim (str) {
	var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
	for (var i = 0; i < str.length; i++) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(i);
			break;
		}
	}
	for (i = str.length - 1; i >= 0; i--) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}
function isNumberKey(evt)
{
	//alert(evt);
	//if(evt)
	//{
	var charCode = (evt.which ) ? evt.which : evt.keyCode
	//var charCode = (evt.which ) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode !=46))
	return false;
	return true;
	//}
}
//function in common.js POS section only, comment by ahsan 21/02/2012
function isNumberKeyQty(evt)
{
	//alert(evt);
	//if(evt)
	//{
	var charCode = (evt.which ) ? evt.which : evt.keyCode
	//var charCode = (evt.which ) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57) &&(charCode !=45))
	return false;
	return true;
	//}
}
//add from store_common.js by ahsan 21/02/2012
/*******************************************Form Submission***************************************/
var reloaddiv, reloadpage,formid;
function addform(url,frm,div,page)
{
//	alert(url+' '+formid+' '+reloaddiv+' '+reloadpage);
	reloaddiv	=	div;
	reloadpage	=	page;
	formid		=	frm;
	options	=	{	
					url : url,
					type: 'POST',
					success: response
				}
	jQuery('#'+formid).ajaxSubmit(options);
}
function response(text)
{
	if(text.length > 2)
	{
		adminnotice(text,0,5000);	
	}
	else
	{
		adminnotice('Data has been saved.',0,5000);
		reloaddiv = reloaddiv+'_tab';
		selecttab(reloaddiv,reloadpage);
		//jQuery('#'+reloaddiv).load(reloadpage);
		hideform();
	}
}
//from main, start edit by Ahsan 13/02/2012
/*******************************************Form Submission***************************************/
var reloaddiv, reloadpage,formid,pagetype;
function submitdata(url,frm,div,page,ptype)
{
//	alert(url+' '+formid+' '+reloaddiv+' '+reloadpage);
	reloaddiv	=	div;
	reloadpage	=	page;
	formid		=	frm;
	pagetype	=	ptype;
	
	options	=	{	
					url : url,
					type: 'POST',
					success: showresponse
				}
	jQuery('#'+formid).ajaxSubmit(options);
}
function showresponse(text)
{
	//alert(text);
	if(text.length>2)
	{
		adminnotice(text,0,5000);	
		//alert('test');
	}
	else
	{
		if(pagetype=='f')
		{
			//alert('here');
			hidedialog('dialog-form');	
		}
		adminnotice('Data has been saved.',0,5000);
		//reloaddiv = reloaddiv+'_tab';
		//selecttab(reloaddiv,reloadpage);
		//alert(reloaddiv);
		jQuery('#'+reloaddiv).load(reloadpage);
		hideform_main(formid,pagetype);
	}
}
function hideform_main(divid,pagetype)
{
	//document.getElementById(formid).style.display='none';
	//alert(pagetype);
	//alert(pagetype);
	if(pagetype=='')
	{
		$('#'+divid).slideUp(500);
		$('#subsection').slideUp(500);
	}
	else
	{
		
		$('#dialog-form').dialog( "close" );	
	}
}
//end edit
function hideform()
{
	document.getElementById(formid).style.display='none';
}
function open_win(url_add,t)
{
	window.open(url_add,t,'width=300,height=200,menubar=yes,status=yes,location=yes,toolbar=yes,scrollbars=yes');
}
// added by yasir 12-19-2011
function printTransaction(cdiv, cbfield)
{	//alert(billid);    
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	
	if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		    var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=800,left=100,top=25';
		 	window.open('accounts/reports/printtransaction.php?id='+id,'Invice',display);
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			
		}//else	
	
	}
//end add 