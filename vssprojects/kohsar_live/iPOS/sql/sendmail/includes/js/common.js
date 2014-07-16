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
function showpage(clickedon,cbfield,page,div,cdiv,param)
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
		jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
		
	 //document.getElementById(div).className='divborder';
	// document.getElementById(cdiv).className='divborderkhali';
	// olddiv=div;
	}
	// alert(div);
	
	 //document.getElementById(div).style.backgroundColor="#FFFFCC";
	 //document.getElementById(cdiv).style.backgroundColor="#fff";
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
		text	="<div id=error class=notice>"+text+"    <a href='javascript:adminnotice(0,1,0)'><img src='images/min.GIF' border='0' /></a></div>";
		jQuery('#msg').html(text);
		jQuery('#error').fadeOut(5000);	
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