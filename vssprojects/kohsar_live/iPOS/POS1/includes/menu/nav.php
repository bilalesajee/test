<?php
@session_start();

if(file_exists("../security/adminsecurity.php"))
{
	include("../security/adminsecurity.php");
}
else
{
	include("includes/security/adminsecurity.php");	
}
global $AdminDAO,$Component,$qs;
$screens	=	$AdminDAO->getrows("screen","*"," fkmoduleid=2 ORDER BY displayorder");
// retrieving counter info
$counterinfo	=	$AdminDAO->getrows("$dbname_detail.counter","countertype","countername='$countername'");
$countertype	=	$counterinfo[0]['countertype'];
// countertype = 1 means normal pos; countertype = 2 means hotels
?>
<ul id="menu">
<?php
$iteration	=	 0;
foreach($screens as $screen)
{
	$shortkey		=	$screen['shortkey'];
	$screenname		=	$screen['screenname'];
	$shortcut		=	$screen['shortcut'];
	$url			=	$screen['url'];
	// visibility 1= normal pos; 2 = both hotels and pos; 3= hotels only
	$visibility		=	$screen['visibility'];
	$name	=	preg_split('//', $screenname, -1);
	$n2="";
	$flag	=	0;
	foreach($name as $n)
	{
		if(strtolower($n) == $shortkey && $flag!=1)
		{
			$flag	=	1;
			$n = "<u>$n</u>";
		}
		$n2	.=	$n;
	}
	if($countertype==2 && $visibility!=1  )
	{
	?>
    <li >
         <a href="javascript:void(0)" onclick="javascript: selecttab('<?php echo $screenname; ?>_tab','<?php echo $url;?>');" title="<?php echo $screenname." (".$shortcut.")";?>" class="<?php if($iteration == 0) echo "current";?>" id="<?php echo $screenname; ?>_tab">
            <?php echo $n2; ?>
         </a>
    </li>
	<?php
	}
	if($countertype==1 && $visibility!=3  )
	{
	?>
		<li >
         <a href="javascript:void(0)" onclick="javascript: selecttab('<?php echo $screenname; ?>_tab','<?php echo $url;?>');" title="<?php echo $screenname." (".$shortcut.")";?>" class="<?php if($iteration == 0) echo "current";?>" id="<?php echo $screenname; ?>_tab">
            <?php echo $n2; ?>
         </a>
    </li>
    <?php
	}
$iteration++;
}// end foreach
?>
    <li> 
    	<?php 
			$counter	=	$countername;
			$pendingsalerows	=	$AdminDAO->getrows(" $dbname_detail.sale "," pksaleid,from_unixtime(datetime,'%d-%m-%y  %h:%i:%s') as datetime"," countername='$counter' and status='3' order by pksaleid DESC");
			?>
            <!--removed label Holding sale and changed text Select Sale to Held Sales by Yasir 07-07-11 -->
			<div align="right" style="margin-top:10px" id="holdingsale">
			<select name="holdingsales" id="holdingsales" onchange="activatesale(this.value)">
				<option value="">Held Sales</option>
				<?php
				for($h=0;$h<count($pendingsalerows);$h++)
				{
				?>
				<option value="<?php echo $pendingsalerows[$h]['pksaleid'];?>" <?php if($_SESSION['tempsaleid']==$pendingsalerows[$h]['pksaleid']){print"selected";}?>><?php echo $pendingsalerows[$h]['datetime'];?></option>
				<?php
				}
				?>
			</select>
			</div>
	
    
    </li>
    <!--Commented by Yasir - 07-07-11-->
    <!--<li><a href="javascript:void(0)" onclick="javascript: loadsection('main-content','sale.php?salecompleted=3');" title="Hold Sale (CTRL+L)"  id="Holding_tab">Ho<u>l</u>d Sale</a></li>-->
	<li>
		<a href="javascript:void(0)" onclick="javascript:fnctpmode()" title="Click this Menu for Trade price sales.(If this Menu is Selected/Red it means trade price sales mode is active.)" id="tpmode">
		 Trad<u>e</u> Price
		 </a>
	<!--</li>
		<li title="CTRL+;">
		<a href="javascript:void(0)" onclick="selecttab('Pricechange_tab','pricechangetasks.php');" id="Pricechange_tab">
		Prices Tasks
		 </a>
	</li>-->
</ul>