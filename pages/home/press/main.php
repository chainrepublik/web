<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../home/messages/CMessages.php";
  include "../CHome.php";
  include "CPress.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $mes=new CMessages($db, $acc, $template);
  $press=new CPress($db, $acc, $template, $mes);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
</head>

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(1);
	  ?>
      </td>
    </tr>
    <tr>
      <td><img src="../../template/GIF/bar.png" width="1020" height="20" alt=""/></td>
    </tr>
    <tr>
      <td height="500" align="center" valign="top" background="../../template/GIF/back_panel.png"><table width="1005" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td width="204" align="right" valign="top">
            <?
			   $home->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
           
			<?
			   $template->showHelp("Publishing articles is one of the easiest ways to <strong>make money</strong> in chainrepublik. Every 24 hours, the network <strong>rewards content creators</strong> depending on the votes they receive. And not only those who write articles are rewarded, but also those who <strong>voted</strong> for articles or those who comment or vote on comments. You can approach any subject you want. Publishing an article costs 0.003 CRC and 5 points of energy. Below are listed top articles.");
			   
			   									   
			   switch ($_REQUEST['act'])
			   {
				   case "new_tweet" :  $press->newTweet($_REQUEST['txt_tweet_title'], 
						                               $_REQUEST['txt_tweet_mes'],
													   $_REQUEST['dd_categ'],
													   $_REQUEST['dd_cou'], 
						                               $_REQUEST['dd_days'], 
						                               0, 
						                               $_REQUEST['txt_tweet_pic']);
						               break;
									   
				      case "vote" : $press->vote($_REQUEST['vote_target_type'], 
				                                 $_REQUEST['vote_targetID'], 
				                                 $_REQUEST['vote_type']);
				                break;
			 
			      
			       case "follow" : $press->follow($_REQUEST['adr'], 
											      $_REQUEST['dd_months']); 
			                       break;
			 
			       case "unfollow" : $press->unfollow($_REQUEST['unfollow_adr']); 
			                        break;
			 
			       
				}
			   
			   
			   // Target
			   if (!isset($_REQUEST['target']))
			      $_REQUEST['target']="ID_LOCAL";
				  
			   // Type
			   if (!isset($_REQUEST['type']))
			      $_REQUEST['type']="ID_TRENDING";
				  
			   // Action
			   if (!isset($_REQUEST['page']))
			      $_REQUEST['page']="show_tweets";
				  
			   // Time
			   if (!isset($_REQUEST['time']))
			      $_REQUEST['time']=24;
				  
			   // Selection
			   switch ($_REQUEST['target'])
			   {
				   // Local press
				   case "ID_LOCAL" : $sel=1; break;
				   
				   // International press
				   case "ID_GLOBAL" : $sel=2; break;
				   
				   // My articles
				   case "ID_MINE" : $sel=3; break;
				   
				   // Write article
				   case "ID_WRITE" : $sel=4; break;
			   }
			   
			   // Menu
			   if ($_REQUEST['ud']['ID']>0)
			   $template->showImgsMenu($sel, 
			                           "menu_label_local_off.png", "menu_label_local_on.png", "Local Press", "main.php?target=ID_LOCAL",
									   "menu_label_global_off.png", "menu_label_global_on.png", "International Press", "main.php?target=ID_GLOBAL",
									   "menu_label_mine_off.png", "menu_label_mine_on.png", "My Articles", "main.php?target=ID_MINE",
									   "menu_label_write_off.png", "menu_label_write_on.png", "Write Article", "main.php?target=ID_WRITE");
			   
			   else
			   $template->showImgsMenu($sel, 
			                           "menu_label_local_off.png", "menu_label_local_on.png", "Local Press", "main.php?target=ID_LOCAL",
									   "menu_label_global_off.png", "menu_label_global_on.png", "International Press", "main.php?target=ID_GLOBAL");			   
			   // Sub menus
			   if ($sel==1 || $sel==2)
			   {
				   // Sub menu
				   switch ($_REQUEST['type'])
				   {
					   // Home
					   case "ID_HOME" : $sel_2=1; 
						                break;   
					   
					   // Trending
					   case "ID_TRENDING" : $sel_2=2; 
						                    break;
					   
					   // Last
					   case "ID_LAST" : $sel_2=3; 
						                break;   
				   }
				   
				   // Small menu
				   $template->showSmallMenu($sel_2, 
				                            "Following", "main.php?target=".$_REQUEST['target']."&type=ID_HOME", 
				                            "Top 24 Hours", "main.php?target=".$_REQUEST['target']."&type=ID_TRENDING", 
											"Last Articles", "main.php?target=".$_REQUEST['target']."&type=ID_LAST");
					
					// Country
					if ($sel==1) 
					    $cou=$_REQUEST['ud']['loc'];
				    else
					   	$cou="EN";
						
					// Local press
					switch ($_REQUEST['page']) 
					{
						// Show tweets
						case "show_tweets" : if ($sel_2==1) 
						                     $press->showTweets("ID_HOME", 
						                                       $cou);
															   
											 else if ($sel_2==2) 
											 $press->showTweets("ID_TRENDING", 
						                                       $cou);
															   
											 else if ($sel_2==3) 
											 $press->showTweets("ID_LAST", 
						                                       $cou);
						                     break;
											 
						// Show tweet
						case "tweet" : // Show post
						               $press->showPost($_REQUEST['tweetID']);
						               
									   // Comments
									   $press->showComments("ID_TWEET", $_REQUEST['tweetID']);
						               break;
					}
			   }
			   
			   // My articles
			   if ($sel==3)
			       $press->showTweets("ID_ADR", 
						              $cou,
									  $_REQUEST['ud']['adr']);
			   
			   // Write article
			   if ($sel==4)
			      $press->showNewTweetPanel();
			?>
            
            </td>
            <td width="206" align="center" valign="top">
            
			<?
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
      </table>       </td></tr></tbody> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="300" align="center" valign="top" bgcolor="#3b424b">
              <br />
              
			  <?
			     $template->showBottomMenu(false);
			  ?>
              
              <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b"><hr /></td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2018, ANNO1777 Labs, All Rights Reserved</td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
    </table>

</body>
</html>