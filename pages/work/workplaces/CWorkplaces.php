<?
class CWorkplaces
{
	function CWorkplaces($db, $acc, $template)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
	}
	
	
	function work($workplaceID, $minutes)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		return false;
			
	    // Minutes
		if ($minutes<5 || $minutes>480)
		{
		    $this->template->showErr("Invalid entry data");
		    return false;
	    }
		   
		// Workplace exist ?
		$query="SELECT work.*, 
		                  com.*, 
						  tc.*, 
						  tp.*, 
						  tp.prod AS prod_name, 
				 		  com.name AS com_name, 
						  com.tip AS com_type
		          FROM workplaces AS work
				  JOIN companies AS com ON com.comID=work.comID
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  JOIN tipuri_produse AS tp ON tp.prod=work.prod
				 WHERE work.workplaceID=?
				   AND work.status=?";
				   
		$result=$this->kern->execute($query, 
		                             "is", 
									 $workplaceID, 
									 "ID_FREE");	
		
	    if (mysqli_num_rows($result)==0)
	  	{
		   $this->template->showErr("Invalid entry data");
		   return false;
		}
		
		// Workplace row
		$work_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		 // Energy
		 if ($_REQUEST['ud']['energy']<round($minutes/5))
		 {
			$this->template->showErr("Insuficient energy");
			return false;
		 }
		 
		 // Company has building ?
		 if (!$this->kern->hasBuilding($work_row['comID']))
		 {
			 $this->template->showErr("Company has no building");
			 return false;
		 }
		 
		 // Company has tools ?
		 if (!$this->kern->hasTools($work_row['comID']))
		 {
			 $this->template->showErr("Company has no tools");
			 return false;
		 }
	
		 $query="SELECT * 
		           FROM work_procs 
				  WHERE adr=? 
				    AND end>?";
		
	     $result=$this->kern->execute($query, 
		                             "si", 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['sd']['last_block']);
		
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			 $this->template->showErr("You are already working");
			 return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Start to work at ".$work_row['name'], $tID);
		
		     // Insert to stack
		     $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssiisi", 
								$_REQUEST['ud']['ID'], 
								"ID_WORK", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$workplaceID,
								$minutes,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
		     $this->template->confirm();
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
	
	function checkWorkBonus($per, $tID)
	{
			   $last=$this->acc->getLastBonusTime("ID_CIT", 
			                                      $_REQUEST['ud']['ID'], 
												  "ID_WORK_".$per."_DAYS");
			   
			   if ($last==0)
			   {
				   $query="INSERT INTO bonuses_paid 
				                   SET receiver_type='ID_CIT', 
								       receiverID='".$_REQUEST['ud']['ID']."', 
									   bonus='ID_WORK_".$per."_DAYS', 
									   amount='0', 
									   tstamp='".time()."'";
				   $this->kern->execute($query);
				   $last=time();
			   }
			   else
			   {
			   // Load workdays
			   $query="SELECT * 
			             FROM work_procs 
						WHERE userID='".$_REQUEST['ud']['ID']."' 
						  AND tstamp>".$last;
			    $result=$this->kern->execute($query);
					
	            if (mysqli_num_rows($result)>=$per)
	               $this->acc->payBonus("ID_WORK_".$per."_DAYS", 
				                        "ID_CIT", 
										$_REQUEST['ud']['ID'],
										1, 
										$tID);
			   }
	}
	
	
	
	function showWorkplaces($tip)
	{
		// Query
		$query="SELECT wp.*, 
		               com.name AS com_name, 
					   com.tip, 
					   com.symbol,
					   tc.name, 
					   tc.pic,
					   cou.country 
		          FROM workplaces AS wp 
				  JOIN companies AS com ON com.comID=wp.comID 
				  JOIN adr AS adr ON adr.adr=com.adr
				  JOIN countries AS cou ON cou.code=adr.cou
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip 
				 WHERE wp.status=? 
				   AND wp.work_ends<?
			  ORDER BY wp.wage DESC, wp.ID ASC 
			     LIMIT 0,20";
		
		// Result	  
		$result=$this->kern->execute($query, 
		                             "si", 
									 "ID_FREE", 
									 time());	
		
		// Can work ?
		if ($this->kern->isWorking($_REQUEST['ud']['adr'])  || 
			$_REQUEST['ud']['energy']<1)
			$can_work=false;
		else
		    $can_work=true;
		
		
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="36%" class="bold_shadow_white_14">Company</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Wage</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="22%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Work</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
         
          <?
		     $a=0;
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 if ($this->kern->reserved("ID_WORK_PACKET", 
										   "par_2_val", 
										   base64_encode($row['workplaceID']))==false)
				 {
					 $a++;
		  ?>
                
                
                <tr>
                <td width="11%" class="font_14">
                <img src="
				<? 
				
				   if ($row['com_pic']!="") 
				      print "../../../uploads/".$row['com_pic']; 
				    else
					  print "../../companies/overview/GIF/prods/big/".$row['pic'].".png"; 
					  
				?>" width="50" height="50" class="img-circle"/></td>
                <td width="28%" class="font_14"><a href="../../companies/overview/main.php?ID=<? print $row['comID']; ?>" class="font_14"><strong><? print base64_decode($row['com_name']); ?></strong></a><br />
                <span class="font_10"><? print $row['name'].", ".$row['country']; ?></span></td>
                <td width="20%" align="center"><span class="bold_verde_14"><? print "".$row['wage']; ?></span><br />
                <span class="font_10">CRC / hour</span></td>
                <td width="25%" align="center">
                <?
				   if ($a==1 && 
					   $can_work==true)
				   {
				?>
                
                <form method="post" action="main.php?act=work&wID=<? print $row['workplaceID']; ?>" id="form_<? print $row['workplaceID']; ?>" name="form_<? print $row['ID']; ?>">
                <select class="form-control" style="width:100px" id="dd_min_<? print $row['ID']; ?>" name="dd_min_<? print $row['workplaceID']; ?>">
                <? if ($_REQUEST['ud']['energy']>=1) print "<option value='5'>5 Minutes</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=3) print "<option value='15'>15 Minutes</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=6) print "<option value='30'>30 Minutes</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=9) print "<option value='45'>45 Minutes</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=12) print "<option value='60'>1 Hour</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=24) print "<option value='120'>2 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=36) print "<option value='180'>3 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=48) print "<option value='240'>4 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=60) print "<option value='300'>5 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=72) print "<option value='360'>6 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=84) print "<option value='420'>7 Hours</option>"; ?>
                <? if ($_REQUEST['ud']['energy']>=96) print "<option value='480'>8 Hours</option>"; ?>
                </select>
                </form>
                
                <?
				   }
				?>
                
                <td width="16%" align="center" class="bold_verde_14">
                <?
				   if ($a==1 && $can_work==true)
				    print "<a href='javascript:void(0)' onClick=\"$('#form_".$row['workplaceID']."').submit()\" class='btn btn-primary'><span class='glyphicon glyphicon-time'></span>&nbsp;&nbsp;Work</a>";
				?>
                
				</td>
                </tr>
                
                <tr>
                <td colspan="5" ><hr></td>
                </tr>
          
          <?
				 }
			  }
		  ?>
          
          </table>
          <br><br><br>
        
        
        <?
	}
	
	
	
	function showAfterWork($salary, $energy, $out, $out_prod, $worked_days)
	{
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod='".$out_prod."'"; 
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$out_prod=$row['name'];
		
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="350" align="center" valign="top" background="GIF/after_work_panel.png"><table width="560" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="14%">&nbsp;</td>
                <td width="28%" height="330" align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="95" align="center" valign="bottom" class="font_15"><strong>You have just won</strong></td>
                  </tr>
                  <tr>
                    <td height="76" align="center" valign="bottom"><br><br><span class="inset_verde_50"><? print "".round($salary, 4); ?></span><br>
                    <span class="font_12">gold (<? print "$".$_REQUEST['sd']['gold_price']*$salary; ?>)</span></td>
                  </tr>
                </table></td>
                <td width="12%" align="center">&nbsp;</td>
                <td width="43%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="3">
                  <tr>
                    <td width="57%">&nbsp;</td>
                    <td width="43%" height="75">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="left" class="font_14">Work Experience</td>
                    <td align="right" class="bold_verde_14">+1 <span class="bold_verde_10">point</span></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left" class="bold_shadow_white_14" ><hr></td>
                    </tr>
                  <tr>
                    <td align="left" class="font_14">Energy</td>
                    <td align="right" class="font_14" style="color:#990000"><strong><? print "-".$energy; ?> <span class="font_10" style="color:#990000">points</span></strong></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left" class="bold_shadow_white_14" ><hr></td>
                    </tr>
                  <tr>
                    <td align="left" class="font_14">Total Worked Days</td>
                    <td align="right" class="bold_verde_14"><? print $worked_days; ?><span class="bold_verde_10"> days</span></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left" ><hr></td>
                    </tr>
                  <tr>
                    <td align="left"><span class="font_14">Output</span></td>
                    <td align="right">
                    <span class="bold_verde_14"><? print "+".$out; ?></span>
                    <span class="bold_verde_10"><? print $out_prod; ?></span>
                    </td>
                  </tr>
                </table></td>
                <td width="3%">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	
	function showTimeLeft()
	{
		 $query="SELECT * 
			       FROM work_procs 
				  WHERE userID='".$_REQUEST['ud']['ID']."' 
			   ORDER BY ID DESC 
			      LIMIT 0,1";
		  $result=$this->kern->execute($query);	
	      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			   
		  $day=floor($row['tstamp']/86400);
	      $today=floor(time()/86400);   
		  
		  // New day ?
		  if ($today==$day) 
		     $can_work=($day*86400)+86400-time();
		  else
		     $can_work=0;
			
		  // In a work process ?
		  if ($_REQUEST['ud']['working']>time())
		    if (($_REQUEST['ud']['working']-time())>$can_work)
			   $can_work=$_REQUEST['ud']['working']-time();
		  
		  // Time to work
		  $h=floor($can_work/3600);
		  $m=floor(($can_work-$h*3600)/60);
		  $s=$can_work-$h*3600-$m*60;
		  
		  $_REQUEST['can_work']=$can_work;
		?>
        
        <br>
        <table width="560" border="0">
          <tbody>
            <tr>
              <td height="110" align="center" valign="top" background="GIF/work_back.png"><table width="95%" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td width="45%" height="100" align="center" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tbody>
                        <tr>
                          <td height="30" align="center" class="bold_shadow_white_14">You can work in</td>
                        </tr>
                        <tr>
                          <td height="65" align="center" valign="bottom" class="bold_red_35"><table width="80%" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td width="28%" align="center" id="td_hours">
								
								<? 
								    if ($h<10) 
									   print "0".$h; 
									else 
									   print $h; 
								?>
                                
                                </td>
                                <td width="6%" align="center">:</td>
                                <td width="29%" align="center" id="td_min">
								
								<? 
								    if ($m<10) 
									   print "0".$m; 
									else 
									   print $m; 
							    ?>
                                
                                </td>
                                <td width="8%" align="center">:</td>
                                <td width="29%" align="center" id="td_sec">
								
								<? 
								    if ($s<10) 
									   print "0".$s; 
									else 
									   print $s; 
								?>
                                
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          </td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="4%">&nbsp;</td>
                    <td width="51%" align="center" valign="top"><table width="95%" border="0" cellpadding="0" cellspacing="0">
                      <tbody>
                        <tr>
                          <td align="center" valign="top" class="inset_gri_10"><table width="95%" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td height="85" valign="bottom">You can work once a day. The day starts at UTC 02:00. All work processes last 8 hours. You can't start working if your last work process started less than 8 hours ago.</td>
                              </tr>
                            </tbody>
                          </table></td>
                        </tr>
                      </tbody>
                    </table></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
        <br>
        
        <script>
        function interval()
        {
	       s=parseInt($('#td_sec').text());
	       s=s-1; 
	       if (s>=0) 
	       {
			  if (s<10) s="0"+s;
		      $('#td_sec').text(s);
	       }
	       else
	       {
		      m=parseInt($('#td_min').text());
	          m=m-1; 
	          if (m>=0) 
		      {   
			     if (m<10) m="0"+m;
		         $('#td_min').text(m);
		      }
		      else
		      {
			    $('#td_min').text('59');
			    h=parseInt($('#td_hours').text());
			    h=h-1;
				
			    if (h>=0) 
			    {
				   if (h<10) h="0"+h;
				   $('#td_hours').text(h);
			    }
			    else
			    {
				   $('#td_hours').text('24');
			     }
		     }
		
		     $('#td_sec').text('59');
	        }
         }

         setInterval(interval, 1000);
         </script>
        
        <?
	}
	
	function showSMS()
	{
		$query="SELECT * 
		          FROM countries 
				 WHERE code='".$_SERVER["HTTP_CF_IPCOUNTRY"]."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		// Country
		$country=$row['country'];
		
		// Modal
		$this->template->showModalHeader("sms_modal", "Unverified Account", "act", "send_sms");
		
		?>
            
           
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/sms.png" width="165" height="174" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">SMS verification</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_12">Only verified accounts can work. To verify your account, you need to provide a phone number where we will send you an SMS containing a 6 digit code. You will need to provide this code to verify your account. You can only use a number registered in your country of residence (<strong class="font_12"><? print $country; ?></strong>). Type your phone number below and press Send SMS.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tbody>
                      <tr>
                        <td width="19%"><input class="form-control" disabled id="txt_prefix" name="txt_prefix" value="<? print $this->getTelCode($_SERVER["HTTP_CF_IPCOUNTRY"]); ?>" style="width:50px"/></td>
                        <td width="5%" align="center">-</td>
                        <td width="76%"><input class="form-control"  id="txt_tel" name="txt_tel" placeholder="00000000000" style="width:220px"/></td>
                      </tr>
                    </tbody>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
         <script>
		   $('#form_sms_modal').submit(
		   function()
		   {
			   $('#txt_prefix').prop('disabled', false);
			});
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Send");
	}
	
	function showEnterCode()
	{
		?>
           
           <br><br>
           <form action="main.php?act=check_code" method="post" id="form_code" name="form_code">
           <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/sms.png" width="165" height="174" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">SMS verification</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_12">We have send an SMS containing a six digit code to number. You should receive it within 30 seconds. If you did not receive the SMS click SMS not received to try another method of verification. Enter your code below.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tbody>
                      <tr>
                        <td width="76%"><input class="form-control"  id="txt_code" name="txt_code" placeholder="000000" style="width:100px"/></td>
                      </tr>
                    </tbody>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="top" background="../../template/GIF/lp_gri.png">&nbsp;</td>
            </tr>
          <tr>
            <td align="left" valign="top"><a href="main.php?act=show_send_sms" class="red_but" style="width:130px">Not Received</a></td>
            <td align="right" valign="top"><a href="#" onclick="$('#form_code').submit()" class="btn btn-primary">Confirm</a></td>
          </tr>
        </table>
        </form>
        
        <?
	}
	
	function showSendSMS()
	{
		?>
           
           <br><br>
           <form action="main.php?act=check_code" method="post">
           <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/sms.png" width="165" height="174" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">SMS verification</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_12">Send an SMS containing your username (<span class="bold_mov_12"><? print $_REQUEST['ud']['user']; ?></span>) to the following number. Once we receive the message, your account will be verified. This is a regular phone number. Your company will not extra charge you. </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_mov_26">+44 7624 803 705</td>
              </tr>
              <tr>
                <td height="40" align="center" class="simple_mov_10">waiting for sms...</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        </form>
        
        <?
	}
	
	function sendSMS($prefix, $phone)
	{
		// Prefix
		if (strlen($prefix)>6 || strlen($prefix)<3)
		{
		   $this->template->showErr("Invalid phone number");
		   return false;
		}
		
		// Phone number
		if (strlen($phone)<5 || strlen($phone)>20)
		{
		   $this->template->showErr("Invalid phone number");
		   return false;
		}
		
		// Already use
		$prefix=str_replace("+", "", $prefix); 
		$query="SELECT * FROM used_numbers WHERE tel='".$prefix.$phone."'"; 
		$result=$this->kern->execute($query);	
		
	    if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("This number was already used");
		    return false;
		}
		
		// Log
		$query="SELECT * 
		          FROM sms_log 
				 WHERE userID='".$_REQUEST['ud']['ID']."'
				   AND tstamp>".(time()-86400);
		$result=$this->kern->execute($query);
			
		if (mysqli_num_rows($result)>=2)
		{
			$this->template->showErr("We can send up to 2 SMS / day / account");
		    return false;
		}
		
		// Generate code
		$code=rand(100000, 999999);
		
		// Send sms
		$prefix=str_replace("+", "", $prefix);
		if ($this->kern->sendSMS($prefix.$phone, "This is your authorization code ".$code)==false)
		{
			$this->template->showErr("We could not send the SMS. Check the phone number you have provided.");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
           $this->kern->newAct("Request sn SMS code", $tID);
		   
		   // Update code
		   $query="UPDATE web_users 
		              SET sms_code='".$code."',
					      sms_tel='".$prefix.$phone."'
				    WHERE ID='".$_REQUEST['ud']['ID']."'";
		   $this->kern->execute($query); 
		   
		   // Insert sms
		   $query="INSERT INTO sms_log 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       number='".$prefix.$phone."', 
							   tstamp='".time()."'";
           $this->kern->execute($query); 
		   
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
	
	function checkCode($code)
	{
		// Code valid
		if (strlen($code)!=6)
		{
			$this->template->showErr("Invalid code");
			$this->showEnterCode();
		    return false;
		}
		
		// Check code
		$query="SELECT * from web_users WHERE id='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		if ($row['sms_code']!=$code)
		{
			$this->template->showErr("Invalid code");
			$this->showEnterCode();
			return false;
		}
	  
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
           $this->kern->newAct("Confirm SMS code", $tID);
		   
		   // Update code
		   $query="UPDATE web_users 
		              SET sms_status='ID_OK'
				    WHERE ID='".$_REQUEST['ud']['ID']."'"; 
		   $this->kern->execute($query);	
		   
		   // Used number
		   $query="INSERT INTO used_numbers 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       tel='".$row['sms_tel']."', 
							   tstamp='".time()."'";
		   $this->kern->execute($query);	
		   
		   // Confirm
		   $this->template->showOk("Congrats. Your account is verified.");

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
	
	function loadCodes()
	{
		$countries = array();
        $countries[] = array("code"=>"AF","name"=>"Afghanistan","d_code"=>"+93");
        $countries[] = array("code"=>"AL","name"=>"Albania","d_code"=>"+355");
        $countries[] = array("code"=>"DZ","name"=>"Algeria","d_code"=>"+213");
        $countries[] = array("code"=>"AS","name"=>"American Samoa","d_code"=>"+1");
        $countries[] = array("code"=>"AD","name"=>"Andorra","d_code"=>"+376");
        $countries[] = array("code"=>"AO","name"=>"Angola","d_code"=>"+244");
        $countries[] = array("code"=>"AI","name"=>"Anguilla","d_code"=>"+1");
        $countries[] = array("code"=>"AG","name"=>"Antigua","d_code"=>"+1");
        $countries[] = array("code"=>"AR","name"=>"Argentina","d_code"=>"+54");
        $countries[] = array("code"=>"AM","name"=>"Armenia","d_code"=>"+374");
        $countries[] = array("code"=>"AW","name"=>"Aruba","d_code"=>"+297");
        $countries[] = array("code"=>"AU","name"=>"Australia","d_code"=>"+61");
        $countries[] = array("code"=>"AT","name"=>"Austria","d_code"=>"+43");
        $countries[] = array("code"=>"AZ","name"=>"Azerbaijan","d_code"=>"+994");
        $countries[] = array("code"=>"BH","name"=>"Bahrain","d_code"=>"+973");
        $countries[] = array("code"=>"BD","name"=>"Bangladesh","d_code"=>"+880");
        $countries[] = array("code"=>"BB","name"=>"Barbados","d_code"=>"+1");
        $countries[] = array("code"=>"BY","name"=>"Belarus","d_code"=>"+375");
        $countries[] = array("code"=>"BE","name"=>"Belgium","d_code"=>"+32");
        $countries[] = array("code"=>"BZ","name"=>"Belize","d_code"=>"+501");
        $countries[] = array("code"=>"BJ","name"=>"Benin","d_code"=>"+229");
        $countries[] = array("code"=>"BM","name"=>"Bermuda","d_code"=>"+1");
        $countries[] = array("code"=>"BT","name"=>"Bhutan","d_code"=>"+975");
        $countries[] = array("code"=>"BO","name"=>"Bolivia","d_code"=>"+591");
        $countries[] = array("code"=>"BA","name"=>"Bosnia and Herzegovina","d_code"=>"+387");
        $countries[] = array("code"=>"BW","name"=>"Botswana","d_code"=>"+267");
        $countries[] = array("code"=>"BR","name"=>"Brazil","d_code"=>"+55");
        $countries[] = array("code"=>"IO","name"=>"British Indian Ocean Territory","d_code"=>"+246");
        $countries[] = array("code"=>"VG","name"=>"British Virgin Islands","d_code"=>"+1");
        $countries[] = array("code"=>"BN","name"=>"Brunei","d_code"=>"+673");
        $countries[] = array("code"=>"BG","name"=>"Bulgaria","d_code"=>"+359");
        $countries[] = array("code"=>"BF","name"=>"Burkina Faso","d_code"=>"+226");
        $countries[] = array("code"=>"MM","name"=>"Burma Myanmar" ,"d_code"=>"+95");
        $countries[] = array("code"=>"BI","name"=>"Burundi","d_code"=>"+257");
        $countries[] = array("code"=>"KH","name"=>"Cambodia","d_code"=>"+855");
        $countries[] = array("code"=>"CM","name"=>"Cameroon","d_code"=>"+237");
        $countries[] = array("code"=>"CA","name"=>"Canada","d_code"=>"+1");
        $countries[] = array("code"=>"CV","name"=>"Cape Verde","d_code"=>"+238");
        $countries[] = array("code"=>"KY","name"=>"Cayman Islands","d_code"=>"+1");
        $countries[] = array("code"=>"CF","name"=>"Central African Republic","d_code"=>"+236");
        $countries[] = array("code"=>"TD","name"=>"Chad","d_code"=>"+235");
        $countries[] = array("code"=>"CL","name"=>"Chile","d_code"=>"+56");
        $countries[] = array("code"=>"CN","name"=>"China","d_code"=>"+86");
        $countries[] = array("code"=>"CO","name"=>"Colombia","d_code"=>"+57");
        $countries[] = array("code"=>"KM","name"=>"Comoros","d_code"=>"+269");
        $countries[] = array("code"=>"CK","name"=>"Cook Islands","d_code"=>"+682");
        $countries[] = array("code"=>"CR","name"=>"Costa Rica","d_code"=>"+506");
        $countries[] = array("code"=>"CI","name"=>"Côte d'Ivoire" ,"d_code"=>"+225");
        $countries[] = array("code"=>"HR","name"=>"Croatia","d_code"=>"+385");
        $countries[] = array("code"=>"CU","name"=>"Cuba","d_code"=>"+53");
        $countries[] = array("code"=>"CY","name"=>"Cyprus","d_code"=>"+357");
        $countries[] = array("code"=>"CZ","name"=>"Czech Republic","d_code"=>"+420");
        $countries[] = array("code"=>"CD","name"=>"Democratic Republic of Congo","d_code"=>"+243");
        $countries[] = array("code"=>"DK","name"=>"Denmark","d_code"=>"+45");
        $countries[] = array("code"=>"DJ","name"=>"Djibouti","d_code"=>"+253");
        $countries[] = array("code"=>"DM","name"=>"Dominica","d_code"=>"+1");
        $countries[] = array("code"=>"DO","name"=>"Dominican Republic","d_code"=>"+1");
        $countries[] = array("code"=>"EC","name"=>"Ecuador","d_code"=>"+593");
        $countries[] = array("code"=>"EG","name"=>"Egypt","d_code"=>"+20");
        $countries[] = array("code"=>"SV","name"=>"El Salvador","d_code"=>"+503");
        $countries[] = array("code"=>"GQ","name"=>"Equatorial Guinea","d_code"=>"+240");
        $countries[] = array("code"=>"ER","name"=>"Eritrea","d_code"=>"+291");
        $countries[] = array("code"=>"EE","name"=>"Estonia","d_code"=>"+372");
        $countries[] = array("code"=>"ET","name"=>"Ethiopia","d_code"=>"+251");
        $countries[] = array("code"=>"FK","name"=>"Falkland Islands","d_code"=>"+500");
        $countries[] = array("code"=>"FO","name"=>"Faroe Islands","d_code"=>"+298");
        $countries[] = array("code"=>"FM","name"=>"Federated States of Micronesia","d_code"=>"+691");
        $countries[] = array("code"=>"FJ","name"=>"Fiji","d_code"=>"+679");
        $countries[] = array("code"=>"FI","name"=>"Finland","d_code"=>"+358");
        $countries[] = array("code"=>"FR","name"=>"France","d_code"=>"+33");
        $countries[] = array("code"=>"GF","name"=>"French Guiana","d_code"=>"+594");
        $countries[] = array("code"=>"PF","name"=>"French Polynesia","d_code"=>"+689");
        $countries[] = array("code"=>"GA","name"=>"Gabon","d_code"=>"+241");
        $countries[] = array("code"=>"GE","name"=>"Georgia","d_code"=>"+995");
        $countries[] = array("code"=>"DE","name"=>"Germany","d_code"=>"+49");
        $countries[] = array("code"=>"GH","name"=>"Ghana","d_code"=>"+233");
        $countries[] = array("code"=>"GI","name"=>"Gibraltar","d_code"=>"+350");
        $countries[] = array("code"=>"GR","name"=>"Greece","d_code"=>"+30");
        $countries[] = array("code"=>"GL","name"=>"Greenland","d_code"=>"+299");
        $countries[] = array("code"=>"GD","name"=>"Grenada","d_code"=>"+1");
        $countries[] = array("code"=>"GP","name"=>"Guadeloupe","d_code"=>"+590");
        $countries[] = array("code"=>"GU","name"=>"Guam","d_code"=>"+1");
        $countries[] = array("code"=>"GT","name"=>"Guatemala","d_code"=>"+502");
        $countries[] = array("code"=>"GN","name"=>"Guinea","d_code"=>"+224");
        $countries[] = array("code"=>"GW","name"=>"Guinea-Bissau","d_code"=>"+245");
        $countries[] = array("code"=>"GY","name"=>"Guyana","d_code"=>"+592");
        $countries[] = array("code"=>"HT","name"=>"Haiti","d_code"=>"+509");
        $countries[] = array("code"=>"HN","name"=>"Honduras","d_code"=>"+504");
        $countries[] = array("code"=>"HK","name"=>"Hong Kong","d_code"=>"+852");
        $countries[] = array("code"=>"HU","name"=>"Hungary","d_code"=>"+36");
        $countries[] = array("code"=>"IS","name"=>"Iceland","d_code"=>"+354");
        $countries[] = array("code"=>"IN","name"=>"India","d_code"=>"+91");
        $countries[] = array("code"=>"ID","name"=>"Indonesia","d_code"=>"+62");
        $countries[] = array("code"=>"IR","name"=>"Iran","d_code"=>"+98");
        $countries[] = array("code"=>"IQ","name"=>"Iraq","d_code"=>"+964");
        $countries[] = array("code"=>"IE","name"=>"Ireland","d_code"=>"+353");
        $countries[] = array("code"=>"IL","name"=>"Israel","d_code"=>"+972");
        $countries[] = array("code"=>"IT","name"=>"Italy","d_code"=>"+39");
        $countries[] = array("code"=>"JM","name"=>"Jamaica","d_code"=>"+1");
        $countries[] = array("code"=>"JP","name"=>"Japan","d_code"=>"+81");
        $countries[] = array("code"=>"JO","name"=>"Jordan","d_code"=>"+962");
        $countries[] = array("code"=>"KZ","name"=>"Kazakhstan","d_code"=>"+7");
        $countries[] = array("code"=>"KE","name"=>"Kenya","d_code"=>"+254");
        $countries[] = array("code"=>"KI","name"=>"Kiribati","d_code"=>"+686");
        $countries[] = array("code"=>"XK","name"=>"Kosovo","d_code"=>"+381");
        $countries[] = array("code"=>"KW","name"=>"Kuwait","d_code"=>"+965");
        $countries[] = array("code"=>"KG","name"=>"Kyrgyzstan","d_code"=>"+996");
        $countries[] = array("code"=>"LA","name"=>"Laos","d_code"=>"+856");
        $countries[] = array("code"=>"LV","name"=>"Latvia","d_code"=>"+371");
        $countries[] = array("code"=>"LB","name"=>"Lebanon","d_code"=>"+961");
        $countries[] = array("code"=>"LS","name"=>"Lesotho","d_code"=>"+266");
        $countries[] = array("code"=>"LR","name"=>"Liberia","d_code"=>"+231");
        $countries[] = array("code"=>"LY","name"=>"Libya","d_code"=>"+218");
        $countries[] = array("code"=>"LI","name"=>"Liechtenstein","d_code"=>"+423");
        $countries[] = array("code"=>"LT","name"=>"Lithuania","d_code"=>"+370");
        $countries[] = array("code"=>"LU","name"=>"Luxembourg","d_code"=>"+352");
        $countries[] = array("code"=>"MO","name"=>"Macau","d_code"=>"+853");
        $countries[] = array("code"=>"MK","name"=>"Macedonia","d_code"=>"+389");
        $countries[] = array("code"=>"MG","name"=>"Madagascar","d_code"=>"+261");
        $countries[] = array("code"=>"MW","name"=>"Malawi","d_code"=>"+265");
        $countries[] = array("code"=>"MY","name"=>"Malaysia","d_code"=>"+60");
        $countries[] = array("code"=>"MV","name"=>"Maldives","d_code"=>"+960");
        $countries[] = array("code"=>"ML","name"=>"Mali","d_code"=>"+223");
        $countries[] = array("code"=>"MT","name"=>"Malta","d_code"=>"+356");
        $countries[] = array("code"=>"MH","name"=>"Marshall Islands","d_code"=>"+692");
        $countries[] = array("code"=>"MQ","name"=>"Martinique","d_code"=>"+596");
        $countries[] = array("code"=>"MR","name"=>"Mauritania","d_code"=>"+222");
        $countries[] = array("code"=>"MU","name"=>"Mauritius","d_code"=>"+230");
        $countries[] = array("code"=>"YT","name"=>"Mayotte","d_code"=>"+262");
        $countries[] = array("code"=>"MX","name"=>"Mexico","d_code"=>"+52");
        $countries[] = array("code"=>"MD","name"=>"Moldova","d_code"=>"+373");
        $countries[] = array("code"=>"MC","name"=>"Monaco","d_code"=>"+377");
        $countries[] = array("code"=>"MN","name"=>"Mongolia","d_code"=>"+976");
        $countries[] = array("code"=>"ME","name"=>"Montenegro","d_code"=>"+382");
        $countries[] = array("code"=>"MS","name"=>"Montserrat","d_code"=>"+1");
        $countries[] = array("code"=>"MA","name"=>"Morocco","d_code"=>"+212");
        $countries[] = array("code"=>"MZ","name"=>"Mozambique","d_code"=>"+258");
        $countries[] = array("code"=>"NA","name"=>"Namibia","d_code"=>"+264");
        $countries[] = array("code"=>"NR","name"=>"Nauru","d_code"=>"+674");
        $countries[] = array("code"=>"NP","name"=>"Nepal","d_code"=>"+977");
        $countries[] = array("code"=>"NL","name"=>"Netherlands","d_code"=>"+31");
        $countries[] = array("code"=>"AN","name"=>"Netherlands Antilles","d_code"=>"+599");
        $countries[] = array("code"=>"NC","name"=>"New Caledonia","d_code"=>"+687");
        $countries[] = array("code"=>"NZ","name"=>"New Zealand","d_code"=>"+64");
        $countries[] = array("code"=>"NI","name"=>"Nicaragua","d_code"=>"+505");
        $countries[] = array("code"=>"NE","name"=>"Niger","d_code"=>"+227");
        $countries[] = array("code"=>"NG","name"=>"Nigeria","d_code"=>"+234");
        $countries[] = array("code"=>"NU","name"=>"Niue","d_code"=>"+683");
        $countries[] = array("code"=>"NF","name"=>"Norfolk Island","d_code"=>"+672");
        $countries[] = array("code"=>"KP","name"=>"North Korea","d_code"=>"+850");
        $countries[] = array("code"=>"MP","name"=>"Northern Mariana Islands","d_code"=>"+1");
        $countries[] = array("code"=>"NO","name"=>"Norway","d_code"=>"+47");
        $countries[] = array("code"=>"OM","name"=>"Oman","d_code"=>"+968");
        $countries[] = array("code"=>"PK","name"=>"Pakistan","d_code"=>"+92");
        $countries[] = array("code"=>"PW","name"=>"Palau","d_code"=>"+680");
        $countries[] = array("code"=>"PS","name"=>"Palestine","d_code"=>"+970");
        $countries[] = array("code"=>"PA","name"=>"Panama","d_code"=>"+507");
        $countries[] = array("code"=>"PG","name"=>"Papua New Guinea","d_code"=>"+675");
        $countries[] = array("code"=>"PY","name"=>"Paraguay","d_code"=>"+595");
        $countries[] = array("code"=>"PE","name"=>"Peru","d_code"=>"+51");
        $countries[] = array("code"=>"PH","name"=>"Philippines","d_code"=>"+63");
        $countries[] = array("code"=>"PL","name"=>"Poland","d_code"=>"+48");
        $countries[] = array("code"=>"PT","name"=>"Portugal","d_code"=>"+351");
        $countries[] = array("code"=>"PR","name"=>"Puerto Rico","d_code"=>"+1");
        $countries[] = array("code"=>"QA","name"=>"Qatar","d_code"=>"+974");
        $countries[] = array("code"=>"CG","name"=>"Republic of the Congo","d_code"=>"+242");
        $countries[] = array("code"=>"RE","name"=>"Réunion" ,"d_code"=>"+262");
        $countries[] = array("code"=>"RO","name"=>"Romania","d_code"=>"+40");
        $countries[] = array("code"=>"RU","name"=>"Russia","d_code"=>"+7");
        $countries[] = array("code"=>"RW","name"=>"Rwanda","d_code"=>"+250");
        $countries[] = array("code"=>"BL","name"=>"Saint Barthélemy" ,"d_code"=>"+590");
        $countries[] = array("code"=>"SH","name"=>"Saint Helena","d_code"=>"+290");
        $countries[] = array("code"=>"KN","name"=>"Saint Kitts and Nevis","d_code"=>"+1");
        $countries[] = array("code"=>"MF","name"=>"Saint Martin","d_code"=>"+590");
        $countries[] = array("code"=>"PM","name"=>"Saint Pierre and Miquelon","d_code"=>"+508");
        $countries[] = array("code"=>"VC","name"=>"Saint Vincent and the Grenadines","d_code"=>"+1");
        $countries[] = array("code"=>"WS","name"=>"Samoa","d_code"=>"+685");
        $countries[] = array("code"=>"SM","name"=>"San Marino","d_code"=>"+378");
        $countries[] = array("code"=>"ST","name"=>"São Tomé and Príncipe" ,"d_code"=>"+239");
        $countries[] = array("code"=>"SA","name"=>"Saudi Arabia","d_code"=>"+966");
        $countries[] = array("code"=>"SN","name"=>"Senegal","d_code"=>"+221");
        $countries[] = array("code"=>"RS","name"=>"Serbia","d_code"=>"+381");
        $countries[] = array("code"=>"SC","name"=>"Seychelles","d_code"=>"+248");
        $countries[] = array("code"=>"SL","name"=>"Sierra Leone","d_code"=>"+232");
        $countries[] = array("code"=>"SG","name"=>"Singapore","d_code"=>"+65");
        $countries[] = array("code"=>"SK","name"=>"Slovakia","d_code"=>"+421");
        $countries[] = array("code"=>"SI","name"=>"Slovenia","d_code"=>"+386");
        $countries[] = array("code"=>"SB","name"=>"Solomon Islands","d_code"=>"+677");
        $countries[] = array("code"=>"SO","name"=>"Somalia","d_code"=>"+252");
        $countries[] = array("code"=>"ZA","name"=>"South Africa","d_code"=>"+27");
        $countries[] = array("code"=>"KR","name"=>"South Korea","d_code"=>"+82");
        $countries[] = array("code"=>"ES","name"=>"Spain","d_code"=>"+34");
        $countries[] = array("code"=>"LK","name"=>"Sri Lanka","d_code"=>"+94");
        $countries[] = array("code"=>"LC","name"=>"St. Lucia","d_code"=>"+1");
        $countries[] = array("code"=>"SD","name"=>"Sudan","d_code"=>"+249");
        $countries[] = array("code"=>"SR","name"=>"Suriname","d_code"=>"+597");
        $countries[] = array("code"=>"SZ","name"=>"Swaziland","d_code"=>"+268");
        $countries[] = array("code"=>"SE","name"=>"Sweden","d_code"=>"+46");
        $countries[] = array("code"=>"CH","name"=>"Switzerland","d_code"=>"+41");
        $countries[] = array("code"=>"SY","name"=>"Syria","d_code"=>"+963");
        $countries[] = array("code"=>"TW","name"=>"Taiwan","d_code"=>"+886");
        $countries[] = array("code"=>"TJ","name"=>"Tajikistan","d_code"=>"+992");
        $countries[] = array("code"=>"TZ","name"=>"Tanzania","d_code"=>"+255");
        $countries[] = array("code"=>"TH","name"=>"Thailand","d_code"=>"+66");
        $countries[] = array("code"=>"BS","name"=>"The Bahamas","d_code"=>"+1");
        $countries[] = array("code"=>"GM","name"=>"The Gambia","d_code"=>"+220");
        $countries[] = array("code"=>"TL","name"=>"Timor-Leste","d_code"=>"+670");
        $countries[] = array("code"=>"TG","name"=>"Togo","d_code"=>"+228");
        $countries[] = array("code"=>"TK","name"=>"Tokelau","d_code"=>"+690");
        $countries[] = array("code"=>"TO","name"=>"Tonga","d_code"=>"+676");
        $countries[] = array("code"=>"TT","name"=>"Trinidad and Tobago","d_code"=>"+1");
        $countries[] = array("code"=>"TN","name"=>"Tunisia","d_code"=>"+216");
        $countries[] = array("code"=>"TR","name"=>"Turkey","d_code"=>"+90");
        $countries[] = array("code"=>"TM","name"=>"Turkmenistan","d_code"=>"+993");
        $countries[] = array("code"=>"TC","name"=>"Turks and Caicos Islands","d_code"=>"+1");
        $countries[] = array("code"=>"TV","name"=>"Tuvalu","d_code"=>"+688");
        $countries[] = array("code"=>"UG","name"=>"Uganda","d_code"=>"+256");
        $countries[] = array("code"=>"UA","name"=>"Ukraine","d_code"=>"+380");
        $countries[] = array("code"=>"AE","name"=>"United Arab Emirates","d_code"=>"+971");
        $countries[] = array("code"=>"GB","name"=>"United Kingdom","d_code"=>"+44");
        $countries[] = array("code"=>"US","name"=>"United States","d_code"=>"+1");
        $countries[] = array("code"=>"UY","name"=>"Uruguay","d_code"=>"+598");
        $countries[] = array("code"=>"VI","name"=>"US Virgin Islands","d_code"=>"+1");
        $countries[] = array("code"=>"UZ","name"=>"Uzbekistan","d_code"=>"+998");
        $countries[] = array("code"=>"VU","name"=>"Vanuatu","d_code"=>"+678");
        $countries[] = array("code"=>"VA","name"=>"Vatican City","d_code"=>"+39");
        $countries[] = array("code"=>"VE","name"=>"Venezuela","d_code"=>"+58");
        $countries[] = array("code"=>"VN","name"=>"Vietnam","d_code"=>"+84");
        $countries[] = array("code"=>"WF","name"=>"Wallis and Futuna","d_code"=>"+681");
        $countries[] = array("code"=>"YE","name"=>"Yemen","d_code"=>"+967");
        $countries[] = array("code"=>"ZM","name"=>"Zambia","d_code"=>"+260");
        $countries[] = array("code"=>"ZW","name"=>"Zimbabwe");
		
		return $countries;
	}
	
	function getTelCode($code)
	{
		$codes=$this->loadCodes();
		
		for ($a=0; $a<=sizeof($codes)-1; $a++)
		  if ($codes[$a]['code']==$code)
		     return $codes[$a]['d_code'];
	}
	
	function showWorkFeeModal($link="")
	{   
		
		// Modal
		$this->template->showModalHeader("work_fee_modal", "Work Fee", "act", "check_market", "wID", "");
		?>
            
           <input id="link" name="link" value="<? print $link; ?>" type="hidden">
           <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/money.png" width="134" height="228" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Not enough energy</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="simple_gri_14">You need an <strong>energy</strong> of minimum <span class="font_14" id="td_min_energy"><? print $min_prod; ?> points</span> to start working. The easiest way to increase your energy is by consuming food, wearing clothes and so on. Below are some suggestions. Go to Market section for details.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="middle" class="bold_green_14">- Consume food</td>
              </tr>
              <tr>
                <td height="30" valign="middle"><span class="bold_green_14">- Own/ rent clothes</span></td>
              </tr>
              <tr>
                <td height="30" valign="middle"><span class="bold_green_14">- Own/ rent a car </span></td>
              </tr>
              <tr>
                <td height="30" valign="middle"><span class="bold_green_14">- Own/ rent a house </span></td>
              </tr>
              <tr>
                <td height="30" valign="middle"><span class="bold_green_14">- Own/ rent jewelry</span></td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
    
        <?
		$this->template->showModalFooter("Cancel", "Check Marketplace");
	}
	
	function consumed()
	{
		// Energy
		if ($_REQUEST['ud']['energy']>50)
		   return true;
		
		// Balance
		$balance=round($_REQUEST['balance']['USD']+$_REQUEST['balance']['GOLD']*$_REQUEST['sd']['gold_price'], 2);
		
		// Min balance
		if ($balance<0.25) 
		  return true;
		
		// Consume req
		if ($balance>1.25) 
		   $req=5;
		else
		   $req=round($balance*5);
		   
		// Consumed
		$query="SELECT COUNT(*) AS total
		          FROM items_consumed 
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
				   AND tstamp>".(time()-86400);
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Consumed
		$consumed=$row['total'];
		
		// Format
		if ($consumed=="") $consumed=0;
		
		// Consumed
		if ($consumed>=$req) 
		   return true;
		else
		   return false;
	}
	
	function showPanel()
	{
		// Energy
		if ($_REQUEST['ud']['energy']>50 || $_REQUEST['ud']['working']>time()) return;
		
		// Balance
		$balance=round($_REQUEST['balance']['USD']+$_REQUEST['balance']['GOLD']*$_REQUEST['sd']['gold_price']);
		
		// Min balance
		if ($balance<1) return true;
		
		// Consume req
		if ($balance>5) 
		   $req=5;
		else
		   $req=$balance;
		   
		// Consumed
		$query="SELECT COUNT(*) AS total
		          FROM items_consumed 
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
				   AND tstamp>".(time()-86400);
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Consumed
		$consumed=$row['total'];
		
		// Consumed ?
		if ($consumed>=$req) return true;
		
		
		?>
        
            <div class="panel panel-default" style="width:92%">
            <div class="panel-body">
   
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="15%" align="left"><img src="GIF/battery.png" width="69" height="100" alt=""/></td>
                  <td width="85%" valign="top"><span class="font_16"><strong>You need to consume more products</strong></span><br><span class="font_12">In order to work you need to consume at least <strong><? print $req; ?> items / day</strong> from the following product categories : <strong>cigars, drinks or food</strong>. You have consumed <strong><? print $consumed; ?> items</strong> in the last 24 hours. Go to market and find some good offerts.</span></td>
                </tr>
              </tbody>
            </table>
            
           </div>
           </div>
           <br>
        
        <?
	}
	
	function showWorking()
	{
		// Working ?
		$query="SELECT * 
		          FROM work_procs 
				 WHERE adr=? 
				   AND end>?";
		
		// Load data
	    $result=$this->kern->execute($query, 
										 "si", 
										 $_REQUEST['ud']['adr'], 
										 $_REQUEST['sd']['last_block']);
		
		if (mysqli_num_rows($result)>0)
		{
			// Load process data
			$query="SELECT com.name,
			               wp.end
			          FROM work_procs AS wp 
					  JOIN workplaces AS work ON work.workplaceID=wp.workplaceID 
					  JOIN companies AS com ON com.comID=work.comID 
					 WHERE wp.adr=? 
					   AND wp.end>?";
			
			// Load data
			$result=$this->kern->execute($query, 
										 "si", 
										 $_REQUEST['ud']['adr'], 
										 $_REQUEST['sd']['last_block']);	
			
			// Row
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>

           <table width="550">
					<td background="./GIF/working.png" height="150">
						<table width="80%" align="center">
							<tr>
								<td width="20%">&nbsp;</td>
								<td width="80%" height="90" valign="top"><strong>You are Working</strong><br><span class="font_12">You are working at <strong><? print base64_decode($row['name']); ?></strong>. While you are working, you can't travel, fight or start a new work process. Your work process will end in <strong>~<? print $this->kern->timeFromBlock($row['end']); ?></strong> (<? print $row['end']-$_REQUEST['sd']['last_block'] ?> blocks)</span></td>
							</tr>
						</table>
					</td>
				</table>
            <br>
          
        <?
		}
		}
	
}
?>