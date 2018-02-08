<?
   include "../../../kernel/db.php";
   include "../../template/CTemplate.php";
   include "CPrelaunch.php";
   
   $db=new db();
   $template=new CTemplate($db);
   $prelaunch=new CPrelaunch($db, $template);
   
   $dif=1435708800-time();
   $days=floor($dif/86400);
   $hours=floor(($dif-$days*86400)/3600);
   $minutes=floor(($dif-$days*86400-$hours*3600)/60);
   $sec=$dif-$days*86400-$hours*3600-$minutes*60;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
</head>

<body style="background-color:#8b5ea3">

<script>
function interval()
{
	s=parseInt($('#td_sec').text());
	s=s-1; 
	if (s>=0) 
	{
		$('#td_sec').text(s);
	}
	else
	{
		m=parseInt($('#td_min').text());
	    m=m-1; 
	    if (m>=0) 
		{   
		   $('#td_min').text(m);
		}
		else
		{
			$('#td_min').text('59');
			h=parseInt($('#td_hours').text());
			h=h-1;
			if (h>=0) 
			{
				$('#td_hours').text(h);
			}
			else
			{
				$('#td_hours').text('24');
				d=parseInt($('#td_days').text());
				d=d-1;
				$('#td_days').text(d);
			}
		}
		
		$('#td_sec').text('59');
	}
}

setInterval(interval, 1000);
</script>

<table width="1045" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><?  if ($_REQUEST['act']=="update") $prelaunch->notify($_REQUEST['txt_email']); ?></td>
  </tr>
  <tr>
    <td height="700" align="center" valign="top" background="back.jpg"><table width="740" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="150" height="215" align="center" valign="bottom" class="bold_shadow_white_16">Days Left</td>
        <td width="47" align="center" valign="bottom">&nbsp;</td>
        <td width="141" align="center" valign="bottom" class="bold_shadow_white_16">Hours Left</td>
        <td width="55" align="center" valign="bottom">&nbsp;</td>
        <td width="150" align="center" valign="bottom" class="bold_shadow_white_16">Minutes Left</td>
        <td width="43" align="center" valign="bottom">&nbsp;</td>
        <td width="154" align="center" valign="bottom" class="bold_shadow_white_16">Seconds Left</td>
      </tr>
      <tr class="bold_mov_50">
        <td height="110" align="center" class="bold_mov_60" id="td_days"><? print $days; ?></td>
        <td align="center">&nbsp;</td>
        <td align="center" class="bold_mov_60" id="td_hours"><? print $hours; ?></td>
        <td align="center">&nbsp;</td>
        <td align="center" class="bold_mov_60" id="td_min"><? print $minutes; ?></td>
        <td align="center">&nbsp;</td>
        <td align="center" class="bold_mov_60" id="td_sec"><? print $sec; ?></td>
      </tr>
      <tr>
        <td height="70" colspan="7" align="center" valign="bottom">
       
        
        <form id="form_update" name="form_update" method="post" action="index.php?act=update">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="75%"><input id="txt_email" name="txt_email" class="form-control" placeholder="Keep me updated (email address)"/></td>
            <td width="25%" align="right"><img src="but.png" width="176" height="39" style="cursor:pointer" onclick="javascript:$('#form_update').submit()" /></td>
          </tr>
        </table>
        </form>
        
        </td>
        </tr>
    </table>
      <table width="780" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="238" height="40">&nbsp;</td>
          <td width="303">&nbsp;</td>
          <td width="239">&nbsp;</td>
        </tr>
        <tr>
          <td height="110" align="left" valign="top"><span class="bold_shadow_white_14">About the project</span><br /><span class="simple_shadow_white_12">chainrepublik is the first social trading game. Its a political and financial simulator where real securities can be traded. Get ready to buy and sell real world securities using virtual brokers.</span></td>
          <td>&nbsp;</td>
          <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="60" valign="top"><span class="bold_shadow_white_14">Testers Login</span><br /><span class="simple_shadow_white_12">If your are already a tester, login here. If you want to become a tester, contact us.</span></td>
            </tr>
            <tr>
              <td height="45" style="cursor:pointer" onclick="javascript:window.location='../../account/login/main.php'">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
</body>
</html>