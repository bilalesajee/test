<?php
@session_start();
?>
<script language="javascript">
function confirmpay()
{
	var uname		=	$('#uname3').val();
	var pass		=	document.getElementById('pass3').value;
 	jQuery('#cancelsalediv').load('checkconfirmpay.php?uname='+uname+'&pass='+pass);	
	return false;
}
</script>

<div id="container2">
<div align="center" style="color:#F00">
  <h4>Please Enter Floor Manager Username and Password</h4>
</div>
<div id="payout_error" align="center" style="display:none;color:#F00">
  <h4>Incorrect Username or Password!!!</h4>
</div>
<form id="payoutfrm" name="payoutfrm" method="post" action="">
	    <table width="200" align="center" id="pos">
	      <tr>
	        <td>Username</td>
	        <td><input name="uname3" id="uname3" type="text"  autocomplete="off" size="20" onfocus="this.select()"/></td>
          </tr>
          <tr>
	        <td>Passsword</td>
	        <td><input name="pass3" id="pass3" type="password"  autocomplete="off" size="20"  onkeydown="javascript:if(event.keyCode==13) {confirmpay(); return false;}" onfocus="this.select()"/></td>
          </tr>
	      <tr>
	        <td></td>
            <td align="center">
            <span class="buttons" style="font-size:12px;">
            <button type="button" name="button" id="button" onclick="confirmpay();">
                    <img src="images/tick.png" alt=""/> 
                   Confirm Payout                </button>
                		<button type="button" name="button2" id="button2" onclick="javascript:jQuery('#confpayout').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
            </span>       		    </td>
          </tr>
    </table>
		<script language="javascript">
			document.getElementById('uname3').focus();
		</script>
</form>
</div>