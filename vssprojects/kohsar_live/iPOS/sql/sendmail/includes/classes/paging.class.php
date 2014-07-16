<?php
class PagedResults {
   /* These are defaults */
   var $TotalResults;
   private $CurrentPage = 1;
   public $PageVarName = "page";
   public $ResultsPerPage = 10;
   public $LinksPerPage = 5;
   private $ResultArray;
   private $TotalPages;
   function init() {
      $this->TotalPages = $this->getTotalPages();
      $this->CurrentPage = $this->getCurrentPage();
      $this->ResultArray = array(
                           "PREV_PAGE" => $this->getPrevPage(),
                           "NEXT_PAGE" => $this->getNextPage(),
                           "CURRENT_PAGE" => $this->CurrentPage,
                           "TOTAL_PAGES" => $this->TotalPages,
                           "TOTAL_RESULTS" => $this->TotalResults,
                           "PAGE_NUMBERS" => $this->getNumbers(),
                           "MYSQL_LIMIT1" => $this->getStartOffset(),
                           "MYSQL_LIMIT2" => $this->ResultsPerPage,
                           "START_OFFSET" => $this->getStartOffset(),
                           "END_OFFSET" => $this->getEndOffset(),
                           "RESULTS_PER_PAGE" => $this->ResultsPerPage,
                           );
      // echo __FILE__ . ' ' . __LINE__ ;                    
      //var_dump(debug_backtrace());
   }
   /* Start information functions */
   function getTotalPages() {
      /* Make sure we don't devide by zero */
     // echo $this->TotalResults . ' ' . $this->ResultsPerPage . '<br>';
      if($this->TotalResults != 0 && $this->ResultsPerPage != 0) {
         $result = ceil($this->TotalResults / $this->ResultsPerPage);
      }
      /* If 0, make it 1 page */
      if(isset($result) && $result == 0) {
         return 1;
      } else {
         return $result;
      }
   }
   function getStartOffset() {
      $offset = $this->ResultsPerPage * ($this->CurrentPage - 1);
      if($offset != 0) { $offset++; }
      return $offset;
   }
   function getEndOffset() {
      if($this->getStartOffset() > ($this->TotalResults - $this->ResultsPerPage)) {
         $offset = $this->TotalResults;
      } elseif($this->getStartOffset() != 0) {
         $offset = $this->getStartOffset() + $this->ResultsPerPage - 1;
      } else {
         $offset = $this->ResultsPerPage;
      }
      return $offset;
   }
   function getCurrentPage() {
      if(isset($_GET[$this->PageVarName])) {
         return $_GET[$this->PageVarName];
      } else {
         return $this->CurrentPage;
      }
   }
   function getPrevPage() {
      if($this->CurrentPage > 1) {
         return $this->CurrentPage - 1;
      } else {
         return false;
      }
   }
   function getNextPage() {
      if($this->CurrentPage < $this->TotalPages) {
         return $this->CurrentPage + 1;
      } else {
         return false;
      }
   }
   function getStartNumber() {
      $links_per_page_half = $this->LinksPerPage / 2;
      /* See if curpage is less than half links per page */
      if($this->CurrentPage <= $links_per_page_half || $this->TotalPages <= $this->LinksPerPage) {
         return 1;
      /* See if curpage is greater than TotalPages minus Half links per page */
      } elseif($this->CurrentPage >= ($this->TotalPages - $links_per_page_half)) {
         return $this->TotalPages - $this->LinksPerPage + 1;
      } else {
         return $this->CurrentPage - $links_per_page_half;
      }
   }
   function getEndNumber() {
      if($this->TotalPages < $this->LinksPerPage) {
         return $this->TotalPages;
      } else {
         return $this->getStartNumber() + $this->LinksPerPage - 1;
      }
   }
   function getNumbers() {
      for($i=$this->getStartNumber(); $i<=$this->getEndNumber(); $i++) {
         $numbers[] = $i;
      }
      return $numbers;
   }
   function pageHTML($qrysring='')
   {
   		$this->init();
   		$pgarray = explode("/", $_SERVER['SCRIPT_NAME']);
		$curpage = $pgarray[count($pgarray)-1];
		$curnumber = $this->ResultArray['CURRENT_PAGE'];
		$start = 1;
		$total   = $this->TotalPages;
		$start = (($curnumber - 5) > 0) ? ($curnumber-5) : 1;
		$end   = (($total - $curnumber) >= 5) ? ($curnumber+5) : $total;
		$ret   = '';
		//print"_______ $qrysring _______";
		if($qrysring=='')
		{
			if($_SERVER['QUERY_STRING'])
			{
				$qstring = preg_replace("/&?page=\d+/", '', $_SERVER['QUERY_STRING']);
				//echo "Query string is $qstring<br>";	
				if($qstring)
				{
					$url = $curpage . '?' . $qstring . "&page=";
				}
				else
				{ 
					$url = $curpage . "?page=";	
				}
			}
			else 
			{
				$url = $curpage . "?page=";
			}
		}
		else
		{
			$qrysring = preg_replace("/&?page=\d+/", '', $qrysring);
			$url=$qrysring;
		}
		//echo " Start is $start and end is $end<br>";
		
		$ret="<select id='pageno' name='pageno' onChange=\"checkval(this.value);\">";
		for($i=1; $i<=$total; $i++) 
		{
			//$pageurl = $url . $i;
			if($qrysring)
			{
				$pageurl = str_replace('~~i~~',$i,$url);
			}
			/*if($this->ResultArray["CURRENT_PAGE"] == $i) 
      		{
         		//$ret .=  "<a class='ftr'>$i</a> &nbsp; ";
      		} 
      		else 
      		{
         		$ret .= "<a class = 'link-ftr' href='javascript: void(0)' onclick='$pageurl'>$i</a> &nbsp; ";
      		}*/
			if($this->ResultArray["CURRENT_PAGE"] == $i) 
      		{
         		$selected=" selected ";
				//$ret .=  "<a class='ftr'>$i</a> &nbsp; ";
      		} 
			$ret.="<option value='$pageurl' $selected>$i</option>";
			$selected="";
   		}
		$ret.="</select>&nbsp;";
   		$first = $last = $next = $previous = '';
   		if($this->ResultArray["CURRENT_PAGE"]!= 1) 
   		{
			if($qrysring)
		  	{
				$url2   = str_replace('~~i~~',1,$url);
				$first =  "<a class='link-ftr imgftr2' href='javascript: void(0)' onclick='$url2'>&nbsp;</a> ";
			}else
			{
				$first =  "<a class='link-ftr imgftr2' href='$url" . "1'>&nbsp;</a> ";
			}
   		} 
   		else 
   		{
      		$first = "<a class='ftr imgftr2d'>&nbsp;</a>";
   		}
	   // Print out our prev link 
	   if($this->ResultArray["PREV_PAGE"]) 
	   {
	      if($qrysring)
		  {
		  	$url3   = str_replace('~~i~~',$this->ResultArray["PREV_PAGE"],$url);
			$previous =  "<a class='link-ftr imgftrn2' href='javascript: void(0)' onclick='$url3'>&nbsp;</a>";
		  }else
		  {
		  	$previous =  "<a class='link-ftr imgftrn2' href='$url" . $this->ResultArray["PREV_PAGE"] . "'>&nbsp;</a>";
	   	   }
	   } else 
	   {
	      $previous =  "<a class='ftr imgftrn2d'>&nbsp;</a>";
	   }
   	   // Print out our next link 
	   if($this->ResultArray["NEXT_PAGE"]) {
	     if($qrysring)
		 {
		 	$url4   = str_replace('~~i~~',$this->ResultArray["NEXT_PAGE"],$url);
			$next =  "<a class='link-ftr imgftrn' href='javascript: void(0)' onclick='$url4'>&nbsp;</a>";
		  }else
		  {
		  	$next =  "<a class='link-ftr imgftrn' href='$url" . $this->ResultArray["NEXT_PAGE"] . "'>&nbsp;</a>";
		  }
	   } else {
	      $next =  "<a class='ftr imgftrnd'>&nbsp;</a>";
	   }
	   // Print our last link 
	   if($this->ResultArray["CURRENT_PAGE"]!= $this->ResultArray["TOTAL_PAGES"]) 
	   {
	      if($qrysring)
		 {
		 	//echo $this->ResultArray["TOTAL_PAGES"];
			$url5   = str_replace('~~i~~',$this->ResultArray["TOTAL_PAGES"],$url);
			$last =  "<a class='link-ftr imgftr' href='javascript: void(0)' onclick='$url5'>&nbsp;</a>";
		  }else
		  {
		  	$last =  "<a class='link-ftr imgftr' href='$url" . $this->ResultArray["TOTAL_PAGES"] . "'>&nbsp;</a>";
		  }
	   } else 
	   {
	      $last =  "<a class='ftr imgftrd'>&nbsp;</a>";
	   }
	   if($_SESSION['pagelimit']==10)
	   {
		   $sel10	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==15)
	   {
		   $sel15	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==30)
	   {
		   $sel30	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==50)
	   {
		   $sel50	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==100)
	   {
		   $sel100	=	"selected=\"selected\"";
	   }
	   else
	   {
		   $sel15	=	"selected=\"selected\"";
	   }
	   $purl = str_replace('~~i~~',1,$url);
	   //onChange='$purl'
	   $pagelimit	=	"<select name=\"pagelimit\" id=\"pagelimit\" onChange='checkval(this.value,1);'><option value='10~~$purl' $sel10>10</option><option value='15~~$purl' $sel15>15</option><option value='30~~$purl' $sel30>30</option><option value='50~~$purl' $sel50>50</option><option value='100~~$purl' $sel100>100</option></select>";
	   // Page Summary
	   $summary = "<a class='ftr'>$curnumber of $total </a>&nbsp;&nbsp;&nbsp;";
			$ret = "$pagelimit $summary $first  $previous  $ret $next  $last";
   		//echo $summary;
		return $ret;
   }
}
/*
	Usage
	// Instantiate the paging class! 
	$Paging = new PagedResults();
	// Select the count of total results  e.g. if you are showing active prospect
	$Paging->TotalResults = $db->fetch($sql);
	$Paging->ResultsPerPage = 10; //results per page limit
	$page  = $Paging->getCurrentPage();
	if($page > 1)
		$start = ($page-1) * $Paging->ResultsPerPage;
	else 
		$start = 1;
	$end   = $Paging->ResultsPerPage;
   if($db->query("Select field1, field2........ from prospects where status < 2 order by fieldname limit $start, $end
   {
   }
   // Paging is only required if results count is  greater than number of records we have
   	if($Paging->TotalResults > $paging->ResultsPerPage) 
    	$pagelinks = $Paging->pageHTML();
    // Assign to smarty		
    $smarty->assign('pagelinks', $pagelinks);
    // In Smarty
    {if $pagelinks}
    	<apply any formating necessary e.g. align right etc {$pagelinks}
    {/if}	
*/
?>