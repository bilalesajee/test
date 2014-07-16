
<script language="javascript">
/*$().ready(function() 
	{
		$("#fromdate").mask("99-99-9999");
		$("#todate").mask("99-99-9999");
		$("#invoicedate").mask("99-99-9999");
		document.getElementById('fromdate').focus();
	});*/
	
	jQuery(function($)
{
 $("#sdate").datepicker({dateFormat: 'yy-mm-dd'});
 $("#edate").datepicker({dateFormat: 'yy-mm-dd'});
});
	
	
function set_year(ik)
{
var nup='';
var nupp='';

var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
if(day<10){
	day='0'+day;
	}

if(month<10){
	month='0'+month;
	}

var year = currentTime.getFullYear();
	if(ik==6){
	///////////////////////////////////////////////////////////////////////////////////////////////////////		
					
					var currentTime33 = new Date();	
					monthr = currentTime33.setMonth(currentTime33.getMonth()-5);
               		monthr =currentTime33.getMonth();
					cday = currentTime33.getDate();
					if(monthr<10){
	                    monthr='0'+monthr;
	                                 }
					yearr = currentTime33.getFullYear();				 
						
					if(cday<10){
	                    cday='0'+cday;
	                                 }
			/////////////////////////////////////////////////////////////////////////////////////////////////////						 
                	nupp=yearr+'-'+monthr+'-'+cday;
					nup=nupp;
				
		
		}else if(ik==1){

        var MDEF=year-1;
		nupp=MDEF+'-'+month+'-'+day;		
	    nup=nupp;	
			}else if(ik==2){
			var MDEF=year-2;
		    nupp=MDEF+'-'+month+'-'+day;	
			 nup=nupp;				
				}else if(ik==11){
			 ////////////////////////////////////////////////////////////////////////////////////////////////////
			        var currentTime1 = new Date();	
					var day_sub = currentTime1.setDate(currentTime1.getDate()-1);
					day_sub = currentTime1.getDate();
					monthr = currentTime1.getMonth()+1;
               		if(monthr<10){
	                    monthr='0'+monthr;
	                                 }
					yearr = currentTime1.getFullYear();
					
					if(day_sub<10){
	                    day_sub='0'+day_sub;
	                                 }				 
			////////////////////////////////////////////////////////////////////////////////////////////////////		
					nupp=yearr+'-'+monthr+'-'+day_sub;
					nup=nupp;
				}else if(ik==12){
					
			///////////////////////////////////////////////////////////////////////////////////////////////////////		
					var currentTime2 = new Date();
					var cday = currentTime2.setDate(currentTime2.getDate()-7);
					cday = currentTime2.getDate();
					
					monthr = currentTime2.getMonth()+1;
               		if(monthr<10){
	                    monthr='0'+monthr;
	                                 }
					yearr = currentTime2.getFullYear();				 
						
					if(cday<10){
	                    cday='0'+cday;
	                                 }
				
			////////////////////////////////////////////////////////////////////////////////////////////////////////		
			
					nupp=yearr+'-'+monthr+'-'+cday;
					nup=nupp;
				}else if(ik==13){
			///////////////////////////////////////////////////////////////////////////////////////////////////////		
					
					var currentTime3 = new Date();	
					monthr = currentTime3.setMonth(currentTime3.getMonth()+0);
               		monthr =currentTime3.getMonth();
					cday = currentTime3.getDate();
					if(monthr<10){
	                    monthr='0'+monthr;
	                                 }
					yearr = currentTime3.getFullYear();				 
						
					if(cday<10){
	                    cday='0'+cday;
	                                 }
			/////////////////////////////////////////////////////////////////////////////////////////////////////						 
                	nupp=yearr+'-'+monthr+'-'+cday;
					nup=nupp;
				
				}
	document.getElementById('sdate').value=nup;
}


</script>


<table width="100%">
  <tbody>
	
     <tr>
          <td width="15%"> Start Date:</td>
          <td width="14%"><input type="text" class="text" name="sdate" id="sdate" value=""></td><td width="71%">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="set_year(11)" ><b>Last Day</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(12)" ><b>Last Week</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(13)" ><b>Last Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(6)" ><b>Six Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(1)" ><b>One Year</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(2)" ><b>Two Year</b></a></td>
        </tr>
      
    <!--<tr>
		<td>To Date </td>
		<td colspan="2"><div id="error2" class="error" style="display:none; float:right;"></div><input name="todate" id="todate" type="text" value="<?php //echo $today; ?>" onkeydown="javascript:if(event.keyCode==13) {showreport();return false;}" size="8"> dd-mm-yyyy</td>
	</tr>-->
    
    
     <tr>
          <td> End Date</td>
          <td><input type="text" class="text"  name="edate"id="edate" value=""></td>
          <td></td>
        </tr>
	<!-- -->
    <!-- -->
	
	</tbody>
</table>


