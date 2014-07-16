<?php
@session_start();
$tempsaleid	=	$_SESSION['tempsaleid'];
if(!$tempsaleid)
{
	?>
    <script language="javascript" type="text/javascript">
    jQuery('#cancelsalepopup').hide();
	</script>
    <?php
}
?>
<script language="javascript">
function cancelsale_()
{
	var uname		=	$('#uname').val();
	var pass		=	document.getElementById('pass').value;
 	jQuery('#cancelsalediv').load('checkcancelsale.php?pass='+pass+'&uname='+uname);	
	return false;
}
</script>

<div id="container2">
<div align="center" style="color:#F00">
  <h4>Please Enter Floor Manager Username and Password to Cancel Sale</h4>
</div>
<div id="cancel_error" align="center" style="display:none;color:#F00">
  <h4>Incorrect Username or Password!!!</h4>
</div>
<form id="cancelsalefrm" name="cancelsalefrm" method="post" action="">
	    <table width="200" align="center" id="pos">
	      <tr>
	        <td>Username</td>
	        <td><input name="uname" id="uname" type="text"  autocomplete="off" size="20"  onfocus="this.select()"/></td>
          </tr>
          <tr>
	        <td>Passsword</td>
	        <td><input name="pass" id="pass" type="password"  autocomplete="off" size="20"  onkeydown="javascript:if(event.keyCode==13) {cancelsale_(); return false;}" onfocus="this.select()"/></td>
          </tr>
	      <tr>
	        <td></td>
            <td align="center">
            <span class="buttons" style="font-size:12px;">
            <button type="button" name="button" id="button" onclick="cancelsale_();">
                    <img src="images/tick.png" alt=""/> 
                   Cancel Sale                </button>
                		<button type="button" name="button2" id="button2" onclick="javascript:jQuery('#cancelsalepopup').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
            </span>       		    </td>
          </tr>
    </table>
		<script language="javascript">
			document.getElementById('uname').focus();
		</script>
</form>
</div>