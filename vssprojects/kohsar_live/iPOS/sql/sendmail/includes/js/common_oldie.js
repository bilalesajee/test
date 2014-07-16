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
	//alert('get sel'+selectedstring);
	
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
		alert(sb);
		var sb1	=	sb.split(cdiv);
		selecttab(tabid,page+'?id='+sb1[0]+'&param='+param);
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
}//getsuppliers	

/*****************************************showbrandform()**************************************************/
function showpage(clickedon,cbfield,page,div,cdiv,param)
{
	
	//alert(clickedon+'='+cbfield);
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
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
	jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
}//editform
/*************************************************call_ajax_paging()*************************************************/
function call_ajax_paging(string,dest,div)// cals the ajax paging "Loads the section again with page paramater"
{
	//jQuery('#'+div).load(dest+'?'+string);
	if(urlpage.indexOf('?')==-1)
	{
		jQuery('#'+div).load(dest+'?'+string);
	}
	else
	{
		jQuery('#'+div).load(dest+'&'+string);
	}
}
/*************************************************call_ajax_sort()*************************************************/
function call_ajax_sort(field,order,page,urlpage,div)
{
	/*var param='';
	if(page)
	{
		param='&page='+page;
	}
	jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page);*/
	var param='';
	if(page)
	{
		param='&page='+page;
	}
	if(urlpage.indexOf('?')==-1)
	{
		jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page);
	}
	else
	{
		jQuery('#'+div).load(urlpage+'&field='+field+'&order='+order+'&'+page);
	}
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
	function deleterecords(page,div,qs)
	{
		var selectedbrands	=	getselected(div);
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
}
function hidediv(divid)
{
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
 //This page is a result of an autogenerated content made by running test.html with firefox.
            function hotkey()
			{
               
                //jQuery(document).bind('keydown', 'Ctrl+s',function (evt){jQuery('#_esc').addClass('dirty'); return false; });
                /*jQuery(document).bind('keydown', 'tab',function (evt){jQuery('#_tab').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'space',function (evt){jQuery('#_space').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'return',function (evt){jQuery('#_return').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'backspace',function (evt){jQuery('#_backspace').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'scroll',function (evt){jQuery('#_scroll').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'capslock',function (evt){jQuery('#_capslock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'numlock',function (evt){jQuery('#_numlock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'pause',function (evt){jQuery('#_pause').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'insert',function (evt){jQuery('#_insert').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'home',function (evt){jQuery('#_home').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'del',function (evt){jQuery('#_del').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'end',function (evt){jQuery('#_end').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'pageup',function (evt){jQuery('#_pageup').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'pagedown',function (evt){jQuery('#_pagedown').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'left',function (evt){jQuery('#_left').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'up',function (evt){jQuery('#_up').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'right',function (evt){jQuery('#_right').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'down',function (evt){jQuery('#_down').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f1',function (evt){jQuery('#_f1').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f2',function (evt){jQuery('#_f2').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f3',function (evt){jQuery('#_f3').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f4',function (evt){jQuery('#_f4').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f5',function (evt){jQuery('#_f5').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f6',function (evt){jQuery('#_f6').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f7',function (evt){jQuery('#_f7').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f8',function (evt){jQuery('#_f8').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f9',function (evt){jQuery('#_f9').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f10',function (evt){jQuery('#_f10').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f11',function (evt){jQuery('#_f11').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f12',function (evt){jQuery('#_f12').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '1',function (evt){jQuery('#_1').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '2',function (evt){jQuery('#_2').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '3',function (evt){jQuery('#_3').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '4',function (evt){jQuery('#_4').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '5',function (evt){jQuery('#_5').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '6',function (evt){jQuery('#_6').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '7',function (evt){jQuery('#_7').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '8',function (evt){jQuery('#_8').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '9',function (evt){jQuery('#_9').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', '0',function (evt){jQuery('#_0').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'a',function (evt){jQuery('#_a').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'b',function (evt){jQuery('#_b').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'c',function (evt){jQuery('#_c').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'd',function (evt){jQuery('#_d').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'e',function (evt){jQuery('#_e').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'f',function (evt){jQuery('#_f').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'g',function (evt){jQuery('#_g').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'h',function (evt){jQuery('#_h').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'i',function (evt){jQuery('#_i').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'j',function (evt){jQuery('#_j').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'k',function (evt){jQuery('#_k').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'l',function (evt){jQuery('#_l').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'm',function (evt){jQuery('#_m').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'n',function (evt){jQuery('#_n').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'o',function (evt){jQuery('#_o').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'p',function (evt){jQuery('#_p').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'q',function (evt){jQuery('#_q').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'r',function (evt){jQuery('#_r').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 's',function (evt){jQuery('#_s').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 't',function (evt){jQuery('#_t').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'u',function (evt){jQuery('#_u').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'v',function (evt){jQuery('#_v').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'w',function (evt){jQuery('#_w').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'x',function (evt){jQuery('#_x').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'y',function (evt){jQuery('#_y').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'z',function (evt){jQuery('#_z').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+a',function (evt){jQuery('#_Ctrl_a').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+b',function (evt){jQuery('#_Ctrl_b').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+c',function (evt){jQuery('#_Ctrl_c').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+d',function (evt){jQuery('#_Ctrl_d').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+e',function (evt){jQuery('#_Ctrl_e').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f',function (evt){jQuery('#_Ctrl_f').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+g',function (evt){jQuery('#_Ctrl_g').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+h',function (evt){jQuery('#_Ctrl_h').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+i',function (evt){jQuery('#_Ctrl_i').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+j',function (evt){jQuery('#_Ctrl_j').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+k',function (evt){jQuery('#_Ctrl_k').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+l',function (evt){jQuery('#_Ctrl_l').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+m',function (evt){jQuery('#_Ctrl_m').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+n',function (evt){jQuery('#_Ctrl_n').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+o',function (evt){jQuery('#_Ctrl_o').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+p',function (evt){jQuery('#_Ctrl_p').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+q',function (evt){jQuery('#_Ctrl_q').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+r',function (evt){jQuery('#_Ctrl_r').addClass('dirty'); return false; });
                */
				jQuery(document).bind('keydown', 'Ctrl+s',function (evt){jQuery('#_Ctrl_s').addClass('dirty'); return false; });
                /*jQuery(document).bind('keydown', 'Ctrl+t',function (evt){jQuery('#_Ctrl_t').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+u',function (evt){jQuery('#_Ctrl_u').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+v',function (evt){jQuery('#_Ctrl_v').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+w',function (evt){jQuery('#_Ctrl_w').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+x',function (evt){jQuery('#_Ctrl_x').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+y',function (evt){jQuery('#_Ctrl_y').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+z',function (evt){jQuery('#_Ctrl_z').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+a',function (evt){jQuery('#_Shift_a').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+b',function (evt){jQuery('#_Shift_b').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+c',function (evt){jQuery('#_Shift_c').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+d',function (evt){jQuery('#_Shift_d').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+e',function (evt){jQuery('#_Shift_e').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f',function (evt){jQuery('#_Shift_f').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+g',function (evt){jQuery('#_Shift_g').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+h',function (evt){jQuery('#_Shift_h').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+i',function (evt){jQuery('#_Shift_i').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+j',function (evt){jQuery('#_Shift_j').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+k',function (evt){jQuery('#_Shift_k').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+l',function (evt){jQuery('#_Shift_l').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+m',function (evt){jQuery('#_Shift_m').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+n',function (evt){jQuery('#_Shift_n').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+o',function (evt){jQuery('#_Shift_o').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+p',function (evt){jQuery('#_Shift_p').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+q',function (evt){jQuery('#_Shift_q').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+r',function (evt){jQuery('#_Shift_r').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+s',function (evt){jQuery('#_Shift_s').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+t',function (evt){jQuery('#_Shift_t').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+u',function (evt){jQuery('#_Shift_u').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+v',function (evt){jQuery('#_Shift_v').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+w',function (evt){jQuery('#_Shift_w').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+x',function (evt){jQuery('#_Shift_x').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+y',function (evt){jQuery('#_Shift_y').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+z',function (evt){jQuery('#_Shift_z').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+a',function (evt){jQuery('#_Alt_a').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+b',function (evt){jQuery('#_Alt_b').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+c',function (evt){jQuery('#_Alt_c').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+d',function (evt){jQuery('#_Alt_d').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+e',function (evt){jQuery('#_Alt_e').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f',function (evt){jQuery('#_Alt_f').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+g',function (evt){jQuery('#_Alt_g').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+h',function (evt){jQuery('#_Alt_h').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+i',function (evt){jQuery('#_Alt_i').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+j',function (evt){jQuery('#_Alt_j').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+k',function (evt){jQuery('#_Alt_k').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+l',function (evt){jQuery('#_Alt_l').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+m',function (evt){jQuery('#_Alt_m').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+n',function (evt){jQuery('#_Alt_n').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+o',function (evt){jQuery('#_Alt_o').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+p',function (evt){jQuery('#_Alt_p').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+q',function (evt){jQuery('#_Alt_q').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+r',function (evt){jQuery('#_Alt_r').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+s',function (evt){jQuery('#_Alt_s').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+t',function (evt){jQuery('#_Alt_t').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+u',function (evt){jQuery('#_Alt_u').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+v',function (evt){jQuery('#_Alt_v').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+w',function (evt){jQuery('#_Alt_w').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+x',function (evt){jQuery('#_Alt_x').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+y',function (evt){jQuery('#_Alt_y').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+z',function (evt){jQuery('#_Alt_z').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+esc', function (evt){jQuery('#_Ctrl_esc').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+tab', function (evt){jQuery('#_Ctrl_tab').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+space', function (evt){jQuery('#_Ctrl_space').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+return', function (evt){jQuery('#_Ctrl_return').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+backspace', function (evt){jQuery('#_Ctrl_backspace').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+scroll', function (evt){jQuery('#_Ctrl_scroll').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+capslock', function (evt){jQuery('#_Ctrl_capslock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+numlock', function (evt){jQuery('#_Ctrl_numlock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+pause', function (evt){jQuery('#_Ctrl_pause').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+insert', function (evt){jQuery('#_Ctrl_insert').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+home', function (evt){jQuery('#_Ctrl_home').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+del', function (evt){jQuery('#_Ctrl_del').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+end', function (evt){jQuery('#_Ctrl_end').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+pageup', function (evt){jQuery('#_Ctrl_pageup').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+pagedown', function (evt){jQuery('#_Ctrl_pagedown').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+left', function (evt){jQuery('#_Ctrl_left').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+up', function (evt){jQuery('#_Ctrl_up').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+right', function (evt){jQuery('#_Ctrl_right').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+down', function (evt){jQuery('#_Ctrl_down').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f1', function (evt){jQuery('#_Ctrl_f1').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f2', function (evt){jQuery('#_Ctrl_f2').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f3', function (evt){jQuery('#_Ctrl_f3').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f4', function (evt){jQuery('#_Ctrl_f4').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f5', function (evt){jQuery('#_Ctrl_f5').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f6', function (evt){jQuery('#_Ctrl_f6').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f7', function (evt){jQuery('#_Ctrl_f7').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f8', function (evt){jQuery('#_Ctrl_f8').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f9', function (evt){jQuery('#_Ctrl_f9').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f10', function (evt){jQuery('#_Ctrl_f10').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f11', function (evt){jQuery('#_Ctrl_f11').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Ctrl+f12', function (evt){jQuery('#_Ctrl_f12').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+esc', function (evt){jQuery('#_Shift_esc').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+tab', function (evt){jQuery('#_Shift_tab').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+space', function (evt){jQuery('#_Shift_space').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+return', function (evt){jQuery('#_Shift_return').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+backspace', function (evt){jQuery('#_Shift_backspace').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+scroll', function (evt){jQuery('#_Shift_scroll').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+capslock', function (evt){jQuery('#_Shift_capslock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+numlock', function (evt){jQuery('#_Shift_numlock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+pause', function (evt){jQuery('#_Shift_pause').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+insert', function (evt){jQuery('#_Shift_insert').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+home', function (evt){jQuery('#_Shift_home').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+del', function (evt){jQuery('#_Shift_del').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+end', function (evt){jQuery('#_Shift_end').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+pageup', function (evt){jQuery('#_Shift_pageup').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+pagedown', function (evt){jQuery('#_Shift_pagedown').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+left', function (evt){jQuery('#_Shift_left').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+up', function (evt){jQuery('#_Shift_up').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+right', function (evt){jQuery('#_Shift_right').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+down', function (evt){jQuery('#_Shift_down').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f1', function (evt){jQuery('#_Shift_f1').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f2', function (evt){jQuery('#_Shift_f2').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f3', function (evt){jQuery('#_Shift_f3').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f4', function (evt){jQuery('#_Shift_f4').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f5', function (evt){jQuery('#_Shift_f5').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f6', function (evt){jQuery('#_Shift_f6').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f7', function (evt){jQuery('#_Shift_f7').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f8', function (evt){jQuery('#_Shift_f8').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f9', function (evt){jQuery('#_Shift_f9').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f10', function (evt){jQuery('#_Shift_f10').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f11', function (evt){jQuery('#_Shift_f11').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Shift+f12', function (evt){jQuery('#_Shift_f12').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+esc', function (evt){jQuery('#_Alt_esc').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+tab', function (evt){jQuery('#_Alt_tab').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+space', function (evt){jQuery('#_Alt_space').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+return', function (evt){jQuery('#_Alt_return').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+backspace', function (evt){jQuery('#_Alt_backspace').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+scroll', function (evt){jQuery('#_Alt_scroll').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+capslock', function (evt){jQuery('#_Alt_capslock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+numlock', function (evt){jQuery('#_Alt_numlock').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+pause', function (evt){jQuery('#_Alt_pause').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+insert', function (evt){jQuery('#_Alt_insert').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+home', function (evt){jQuery('#_Alt_home').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+del', function (evt){jQuery('#_Alt_del').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+end', function (evt){jQuery('#_Alt_end').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+pageup', function (evt){jQuery('#_Alt_pageup').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+pagedown', function (evt){jQuery('#_Alt_pagedown').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+left', function (evt){jQuery('#_Alt_left').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+up', function (evt){jQuery('#_Alt_up').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+right', function (evt){jQuery('#_Alt_right').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+down', function (evt){jQuery('#_Alt_down').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f1', function (evt){jQuery('#_Alt_f1').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f2', function (evt){jQuery('#_Alt_f2').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f3', function (evt){jQuery('#_Alt_f3').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f4', function (evt){jQuery('#_Alt_f4').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f5', function (evt){jQuery('#_Alt_f5').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f6', function (evt){jQuery('#_Alt_f6').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f7', function (evt){jQuery('#_Alt_f7').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f8', function (evt){jQuery('#_Alt_f8').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f9', function (evt){jQuery('#_Alt_f9').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f10', function (evt){jQuery('#_Alt_f10').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f11', function (evt){jQuery('#_Alt_f11').addClass('dirty'); return false; });
                jQuery(document).bind('keydown', 'Alt+f12', function (evt){jQuery('#_Alt_f12').addClass('dirty'); return false; });*/
            }
            
            