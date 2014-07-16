<div id="footer">
  <div id="footer_left"></div>
  <div id="footer_right"></div>

    <ul id="bot-navigation">
<?php
for($i=0;$i<sizeof($tabres);$i++)
{
	$screenname		=	$tabres[$i]['screenname'];
	/*$firstscreen	=	$tabres[0]['pkscreenid'];
	$firsturl		=	$tabres[0]['url'];*/
	$pkscreenid		=	$tabres[$i]['pkscreenid'];
	$screenurl		=	$tabres[$i]['url'];
	$visibility		=	$tabres[$i]['visibility'];

	//edit by Ahsan on 09/02/2012
	$display_screen_tabs_footer = "<li id=\"{$pkscreenid}_tab_b\">
						<span><span>
						<a href=\"javascript:void(0)\" onclick=\"javascript: selecttab('{$pkscreenid}_tab','{$screenurl}');\">
						$screenname
						</a>
						</span></span>
						</li>";
	
	//1. main, 2. global (main & store), 3. local (store) 
	if($_SESSION['siteconfig'] == 1){
        if($visibility==1 || $visibility==2)
        {
			echo $display_screen_tabs_footer;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
        }
	}
	if($_SESSION['siteconfig'] == 2){
        if($visibility==1 || $visibility==2 || $visibility==3)
        {
			echo $display_screen_tabs_footer;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
        }
	}
	if($_SESSION['siteconfig'] == 3){
		if($visibility==2 || $visibility==3)
		{
			echo $display_screen_tabs_footer;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
		}
	}

/*	if($visibility==2 || $visibility==3)
	{
	?>
    <li id="<?php echo $pkscreenid.'_tab_b';?>">
        <span><span>
        <a href="javascript:void(0)" onclick="javascript: selecttab('<?php echo $pkscreenid.'_tab';?>','<?php echo $screenurl;?>');">
            <?php echo $screenname;?>
        </a>
        </span></span>
    </li>
    <?php
	}*/
	//end edit
}
/*?>
    <li id="Summary_tab_b"><span><span><a href="javascript: selecttab('Summary_tab','summary.php');">Summary</a></span></span></li>
    
</ul>
    </div>
</div>
<?php
*/
?>
</ul>
</div>
</div>
</div>
</body>
