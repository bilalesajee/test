<?php
@session_start();
$tempsaleid	=	$_SESSION['tempsaleid'];
if(!$tempsaleid)
{
	?>
    <script language="javascript" type="text/javascript">
    jQuery('#confirmdiscount').hide();
	</script>
    <?php
}
?>
<script language="javascript">
function confirmdiscount()
{
	var uname		=	$('#uname2').val();
	var pass		=	document.getElementById('pass2').value;
 	jQuery('#cancelsalediv').load('checkconfirmdiscount.php?uname='+uname+'&pass='+pass);	
	return false;
}
</script>

<div id="container2">
<div align="center" style="color:#F00">
  <h4>Please Enter Floor Manager Username and Password to Confirm Discount</h4>
</div>
<div id="discount_error" align="center" style="display:none;color:#F00">
  <h4>Incorrect Username or Password!!!</h4>
</div>
<form id="discountfrm" name="discountfrm" method="post" action="">
	    <table width="200" align="center" id="pos">
	      <tr>
	        <td>Username</td>
	        <td><input name="uname2" id="uname2" type="text"  autocomplete="off" size="20" onfocus="this.select()"/></td>
          </tr>
          <tr>
	        <td>Passsword</td>
	        <td><input name="pass2" id="pass2" type="password"  autocomplete="off" size="20"  onkeydown="javascript:if(event.keyCode==13) {confirmdiscount(); return false;}" onfocus="this.select()"/></td>
          </tr>
	      <tr>
	        <td></td>
            <td align="center">
            <span class="buttons" style="font-size:12px;">
            <button type="button" name="button" id="button" onclick="confirmdiscount();">
                    <img src="images/tick.png" alt=""/> 
                   Confirm Discount                </button>
                		<button type="button" name="button2" id="button2" onclick="javascript:jQuery('#confirmdiscount').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
            </span>       		    </td>
          </tr>
    </table>
		<script language="javascript">
			document.getElementById('uname2').focus();
		</script>
</form>
</div>