// JavaScript Document
function loaddetail(div)
{
	$('#'+div).modal();	
}
var selectedstring	='';
var selectedarray	=	new Array();
function setselected(id)
{
	var ids	=	id.split("_");
	var frm	=	ids[0];
	var id2	=	ids[2];
	var c	=	document.getElementById(frm+'_cb_'+id2);
	
	if (c.checked == false)
	{
		selectedstring += ','+ id2;
	}
	else
	{
		selectedstring = selectedstring.replace(','+id2,""); 
	}
}
function getselected()
{
	//alert('get sel'+selectedstring);
	selectedarray	=	selectedstring.split(',');
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
function highlight(id,clas,ev)
{
	
	var ids	=	id.split("_");
	var frm	=	ids[0];
	//var cb	=	ids[1];
	//var tr	=	ids[1];
	var id2	=	ids[2];
	if(ev=='row')
	{
		//alert(frm+'_cb_'+id);
		setselected(id);//set the selected check boxes array
	}
	var cb	=	document.getElementById(frm+'_cb_'+id2);
	//alert(cb);
	
	if(cb.checked == false)
	{
		document.getElementById(frm+'_tr_'+id2).className='selected'+clas;
		cb.checked		=	true;
		//viewsuppliers	=	id;
	}
	else
	{
		//viewsuppliers=0;
		document.getElementById(frm+'_tr_'+id2).className=clas;
		cb.checked = false;
		document.getElementById('chkAll').checked	=	false;
	}
	if(ev=='chk')
	{
			
		if(cb.checked == false)
		{
			//viewsuppliers=0;
			document.getElementById(frm+'_tr_'+id2).className=clas;
			cb.checked = false;
			document.getElementById('chkAll').checked	=	false;
		}
		else
		{
			document.getElementById(frm+'_tr_'+id2).className='selected'+clas;
			cb.checked		=	true;
			
			//viewsuppliers	=	id;
		}
			
	}
}  
/***********************************************loadsuppliers()*********************************/
function loadsubgrid(div,checks,url,cdiv)
{
	
	
	var selectedbrands	=	getselected();
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
}//loadsuppliers
/***************************************************getsuppliers()**************************************************/
function getgrid(page,checks,tabid,cdiv,param)
{
	var selectedbrands	=	getselected();
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
		selecttab(tabid,page+'?id='+sb1[0]+'&param='+param);
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
}//getsuppliers	

/*****************************************showbrandform()**************************************************/
function showpage(clickedon,cbfield,page,div,cdiv)
{
	
	//alert(clickedon+'='+cbfield);
	if(clickedon == '1')
	{
		var selectedbrands	=	getselected();
		var sb;
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
			
			jQuery("#"+div).load(page+'?id='+sb1[0]);
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
		}//else
	}
	else
	{
		jQuery("#"+div).load(page+'?id=-1');
	}
}//editform
/*************************************************call_ajax_paging()*************************************************/
function call_ajax_paging(string,dest,div)// cals the ajax paging "Loads the section again with page paramater"
{
	jQuery('#'+div).load(dest+'?'+string);
}
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
		var	ab	=	cb.split('b');
		var tr	=	document.getElementById('tr'+ab[1]);
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
function checkAll(currentcheckbox,field)
{
	
	if(currentcheckbox.checked == true )
	{
		for (i = 0; i < field.length; i++)
		{
			var id	=	field[i].id;
			var ids	=	id.split("_");
			var frm	=	ids[0];
			//var cb	=	ids[1];
			//var tr	=	ids[1];
			var id2	=	ids[2];
			
			//var	ab	=	cb.split('b');
			var tr	=	document.getElementById(frm+'_tr_'+id2);
			setselected(frm+'_cb_'+id2);
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
			var id	=	field[i].id;
			var ids	=	id.split("_");
			var frm	=	ids[0];
			//var cb	=	ids[1];
			//var tr	=	ids[1];
			var id2	=	ids[2];
			
			//var	ab	=	cb.split('b');
			var tr	=	document.getElementById(frm+'_tr_'+id2);
			setselected(frm+'_cb_'+id2);
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
	function deleterecords(page,div,qs)
	{
		var selectedbrands	=	getselected();
		var totalrecords	=	(selectedbrands.length)-1;
		
		//alert(selectedbrands);
		if( totalrecords > 0)
		{
			if(confirm('Are you sure to delete selected ('+totalrecords+') record(s)?'))
			{
				//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
				//	var v	=	br1[i].value;
					brandsfordelete	+=	','+selectedbrands[i];
				
					
				}//for
//				alert(brandsfordelete);
				jQuery('#'+div).load(page+'?'+qs+'&oper=del&id='+brandsfordelete);
				//.jQuery("#loading")
				//alert('a');
			}//if confirm
		}//if selected brands
		else
		{
			alert("Please select at least one brand to DELETE.");
		}
	}
/***********************************************************SEARCH AREA********************************************/	
function searchgrid(div,page)
{
		var searchField	=	document.getElementById('searchField'+div).value;
		var searchString	=	document.getElementById('searchString'+div).value;
		var searchOper	=	document.getElementById('searchOper'+div).value;
		var id	=	document.getElementById('id'+div).value;
		$('#'+div).load(page+'?_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id);
}
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