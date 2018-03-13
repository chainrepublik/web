<?
class CAds
{
	function CAds($db, $acc, $template)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function newAd($title, $mes, $link)
	{
		// Decode
		$title=base64_decode($title);
		$mes=base64_decode($mes);
		$link=base64_decode($link);
		
		// No escape
		$title=$this->kern->noEscape($title);
		$mes=$this->kern->noEscape($mes);
		$link=$this->kern->noEscape($link);
	    
	 	// Title
		if (strlen($title)<5 || strlen($title)>25)
		{
			$this->template->showErr("Invalid title length (5-25 characters)");
			return false;
		}
		
		// Message
		if (strlen($mes)<70 || strlen($mes)>80)
		{
			$this->template->showErr("Invalid message length (50-75 characters)");
			return false;
		}
		
		// Link
		if (strlen($link)<10 || strlen($title)>100)
		{
			$this->template->showErr("Invalid link length (10-100 characters)");
			return false;
		}
		
		
		// Price
		$query="SELECT * FROM ads WHERE tstamp>".(time()-86400);
		$result=$this->kern->execute($query);	
		$price=mysql_num_rows($result)*0.01;
		if ($price<0.01) $price=0.01;
		
		// Funds
		if ($_REQUEST['balance']['GOLD']<$price)
		{
			$this->template->showErr("Insufficient funds to execute this operation.");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Post an ad message", $tID);
		   
		   // Insert message
		   $query="INSERT INTO ads 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       title='".base64_encode($title)."', 
							   mes='".base64_encode($mes)."', 
							   link='".base64_encode($link)."', 
							   views=0,
							   clicks=0,
							   status='ID_PENDING', 
							   tstamp='".time()."', 
							   tID='".$tID."'"; 
		   $this->kern->execute($query);
		   
		   // Payment
		   $this->acc->finTransfer("ID_CIT", 
	                               $_REQUEST['ud']['ID'],
						           "ID_GAME", 
	                                0, 
						            $price, 
						            "GOLD", 
						            "You have posted a new ad message", 
						            $_REQUEST['ud']['user']." posted a new ad message");
									
		   // Confirm
		   $this->template->showOk("Your ad is pending review. It will be reviewed within 6 hours.");
		   print "<br>";
		   
		   // Commit
		   $this->kern->commit();

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function showMyAds()
	{
		$query="SELECT * 
		          FROM ads 
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
			  ORDER BY ID DESC 
			     LIMIT 0,10";
		$result=$this->kern->execute($query);	
	  
		?>
        
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="43%" class="bold_shadow_white_14">Ad Message</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Status</td>
                <td width="3%" align="center" class="font_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Views</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">Clicks</td>
                <td width="3%" align="center" class="font_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">CTR</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <?
		     if (mysql_num_rows($result)==0)
			 {
				 print "<br><span class='bold_red_14'>No ads found</span>";
				 return false;
			 }
		  ?>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysql_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
                <tr>
                <td width="44%">
                <span class="font_14"><? print base64_decode($row['title']); ?></span>
                <br/>
                <span class="font_10"><? print substr(base64_decode($row['mes']), 0, 25)."..."; ?></span>
                </td>
                <td width="14%" align="center" class="font_14"><span style="color:
                
				<?
				   switch ($row['status'])
				   {
					   case "ID_PENDING" : print "#999900"; break;
					   case "ID_APROVED" : print "#009900"; break;
					   case "ID_REJECTED" : print "#990000"; break;
				   }
				?>
                
                ">
                
                <?
				   switch ($row['status'])
				   {
					   case "ID_PENDING" : print "pending"; break;
					   case "ID_APROVED" : print "aproved"; break;
					   case "ID_REJECTED" : print "rejected"; break;
				   }
				?>
                
                </span></td>
                <td width="14%" align="center" class="font_14"><? print $row['views']; ?></td>
                <td width="14%" align="center" class="font_14"><? print $row['clicks']; ?></td>
                <td width="14%" align="center" class="font_14"><? print round($row['clicks']*100/$row['views'], 2)."%"; ?></td>
                </tr>
                <tr>
                <td colspan="5"><hr></td>
                </tr>
            
            <?
			 }
			?>
            
        </table>
        
        <?
	}
}
?>