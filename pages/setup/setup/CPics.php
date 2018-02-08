<?
class CPics
{
	function CPics($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	function showPicPanel($ID)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="200" colspan="2" align="center"><img src="../../../uploads/<? print $ID; ?>" width="200" height="200"></td>
                  </tr>
                  <tr>
                    <td width="50%" height="60" align="center" id="td_aprove_<? print substr($ID, 0, 10); ?>"><? print "<a href='javascript:void(0)' onclick=\"$('#td_aprove_".substr($ID, 0, 10)."').load('get_page.php?act=aprove_pic&pic=".substr($ID, 0, 10)."')\" style='width:90px' class='btn btn-success'>Aprove</a>"; ?></td>
                    <td width="50%" height="60" align="center" id="td_reject_<? print substr($ID, 0, 10); ?>"><? print "<a href='javascript:void(0)' onclick=\"$('#td_reject_".substr($ID, 0, 10)."').load('get_page.php?act=reject_pic&pic=".substr($ID, 0, 10)."')\" style='width:90px' class='btn btn-danger'>Reject</a>"; ?></td>
                  </tr>
                </tbody>
              </table>
        
        <?
	}
	
	function showUserPanel($userID)
	{
		$query="SELECT * 
		          FROM users 
				 WHERE ID='".$userID."'";
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$user=$row['user'];
		
		$query="SELECT prof.*, us.user 
		          FROM profiles AS prof
				  JOIN users AS us ON us.ID=prof.userID
				 WHERE prof.userID='".$userID."'"; 
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
        
            <table width="620" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td align="left"><? print $user; ?></td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="3" align="center" background="../../template/GIF/lc.png">&nbsp;</td>
            </tr>
            <tr>
              <td width="160" align="center">
              <?
			     if ($row['pic_1']!="" && $row['pic_1_aproved']==0) $this->showPicPanel($row['pic_1']);
			  ?>
              </td>
              <td width="160" align="center">
              <?
			     if ($row['pic_2']!="") $this->showPicPanel($row['pic_2']);
			  ?>
              </td>
              <td width="160" align="center">
              <?
			     if ($row['pic_3']!="") $this->showPicPanel($row['pic_3']);
			  ?>
              </td>
            </tr>
            <tr>
              <td width="160" align="center">
              <?
			     if ($row['pic_4']!="") $this->showPicPanel($row['pic_4']);
			  ?>
              </td>
              <td width="160" align="center">
              <?
			     if ($row['pic_5']!="") $this->showPicPanel($row['pic_5']);
			  ?>
              </td>
              <td width="160" align="center">
			  <?
			     if ($row['pic_6']!="") $this->showPicPanel($row['pic_6']);
			  ?>
              </td>
            </tr>
            <tr>
              <td width="160" align="center">
              <?
			     if ($row['pic_7']!="") $this->showPicPanel($row['pic_7']);
			  ?>
              </td>
              <td width="160" align="center">&nbsp;</td>
              <td width="160" align="center">&nbsp;</td>
            </tr>
            </tbody>
</table>
        
        <?
	}
	
	function showPics($search="")
	{
		$query="SELECT *
		          FROM profiles 
				 WHERE (pic_1<>'' AND pic_1_aproved=0) OR
				       (pic_2<>'' AND pic_2_aproved=0) OR
					   (pic_3<>'' AND pic_3_aproved=0) OR
					   (pic_4<>'' AND pic_4_aproved=0) OR
					   (pic_5<>'' AND pic_5_aproved=0) OR
					   (pic_6<>'' AND pic_6_aproved=0) OR
					   (pic_7<>'' AND pic_7_aproved=0) 
				   LIMIT 0,10"; 
		$result=$this->kern->execute($query);	
	    
		?>
        
<table width="500" border="0" cellspacing="0" cellpadding="0">
         <tbody>
         
		 <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
		 ?>
         
         <tr>
         <td align="center">
         <?
		   $this->showUserPanel($row['userID']);
	 	 ?>
         </td>
         </tr>
         <tr>
         <td align="center">&nbsp;</td>
         </tr>
         
         <?
			}
		 ?>
         </tbody>
</table>
        
        <?
	}
	
	function aprove($pic)
	{
		$query="SELECT * 
		          FROM profiles 
				 WHERE pic_1 LIKE '%".$pic."%' 
				    OR pic_2 LIKE '%".$pic."%' 
					OR pic_3 LIKE '%".$pic."%' 
					OR pic_4 LIKE '%".$pic."%' 
					OR pic_5 LIKE '%".$pic."%' 
					OR pic_6 LIKE '%".$pic."%' 
					OR pic_7 LIKE '%".$pic."%'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// UserID
		$userID=$row['userID'];
		
		// Position
		if (substr($row['pic_1'], 0, 10)==$pic) $pos=1;
		if (substr($row['pic_2'], 0, 10)==$pic) $pos=2;
		if (substr($row['pic_3'], 0, 10)==$pic) $pos=3;
		if (substr($row['pic_4'], 0, 10)==$pic) $pos=4;
		if (substr($row['pic_5'], 0, 10)==$pic) $pos=5;
		if (substr($row['pic_6'], 0, 10)==$pic) $pos=6;
		if (substr($row['pic_7'], 0, 10)==$pic) $pos=7;
		
		 // Aprove
		 $query="UPDATE profiles 
	         	      SET pic_".$pos."_aproved='".time()."'
				    WHERE userID='".$userID."'"; 
		 $this->kern->execute($query);
		 
		 // Confirm
		 print "Aproved";
	}
	
	function reject($pic)
	{
		$query="SELECT * 
		          FROM profiles 
				 WHERE pic_1 LIKE '%".$pic."%' 
				    OR pic_2 LIKE '%".$pic."%' 
					OR pic_3 LIKE '%".$pic."%' 
					OR pic_4 LIKE '%".$pic."%' 
					OR pic_5 LIKE '%".$pic."%' 
					OR pic_6 LIKE '%".$pic."%' 
					OR pic_7 LIKE '%".$pic."%'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// UserID
		$userID=$row['userID'];
		
		// Position
		if (substr($row['pic_1'], 0, 10)==$pic) $pos=1;
		if (substr($row['pic_2'], 0, 10)==$pic) $pos=2;
		if (substr($row['pic_3'], 0, 10)==$pic) $pos=3;
		if (substr($row['pic_4'], 0, 10)==$pic) $pos=4;
		if (substr($row['pic_5'], 0, 10)==$pic) $pos=5;
		if (substr($row['pic_6'], 0, 10)==$pic) $pos=6;
		if (substr($row['pic_7'], 0, 10)==$pic) $pos=7;
		
		// Pic
		$del_pic=$row['pic_'.$pos];
			   
		try
	    {
		   // Begin
		   $this->kern->begin();
			
	  	   $query="UPDATE profiles 
		              SET pic_".$pos."='', 
					      pic_".$pos."_aproved='0' 
				    WHERE userID='".$userID."'"; 
		   $this->kern->execute($query);
		   
		   $this->kern->newEvent("ID_CIT", 
		                         $userID, 
								 "One of your submitted pics was rejected. Please submit only pics of yourself where your face is visible. We dont accept duplicated pics.", $tID);
		   
		   // Delete pic
		   unlink("../../../uploads/".$del_pic);
			
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   print "Rejected";

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
	
	
}
?>