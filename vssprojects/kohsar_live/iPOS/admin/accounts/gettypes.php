<?php
session_start();
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$catid	=	$_GET['catid'];
$typeid	=	$_GET['typeid'];
if($catid == '')
{
	$w	=	" 1 ";
}
else
{
	$w	=	" category_id = $catid ";
}
$types		=	$AdminDAO->getrows(' type','*',"$w");
if(sizeof($types) > 0)
{		  
?>
<select name="types">
	<?php
        for($i=0;$i<sizeof($types);$i++)
        {
    ?>
        	<option <?php if($typeid == $types[$i]['id']) {echo "SELECTED=SELECTED";} ?> value="<?php echo $types[$i]['id'];?>">
				<?php echo $types[$i]['name'];?>
            </option>
    <?php
        }//for
    ?>
</select>
<?php
}//if
else
{
	echo "<font style='color:#FF0080; font-size:14'>Sorry! But this category does not have any Types.</font>";
}
?>
