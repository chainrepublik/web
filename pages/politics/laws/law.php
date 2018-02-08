<?
  session_start(); 
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPolitics.php";
  include "CLaws.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  include "../../../kernel/CDisqus.php";
  
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $pol=new CPolitics($db, $acc, $template);
  $laws=new CLaws($db, $acc, $template, $data);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>chainrepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="../../../utils.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body background="../../template/GIF/back.png">
<? 
   $template->showTop(); 
   $template->showMainMenu(7);
   $template->showTicker();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="700" align="center" valign="top" background="../../template/GIF/main_middle.png"><table width="1020" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="210" align="right" valign="top">
        
        <?
		  $pol->showMenu(1);
		  $template->showWorkPanel();
		  $template->showFxAcademy(); 
		?>
        
          <script>
		    function menu_clicked(panel)
		    {
				$('#div_debate').css('display', 'none');
				$('#div_votes_yes').css('display', 'none');
				$('#div_votes_no').css('display', 'none');
				
				switch (panel)
				{
					case "Debate" : $('#div_debate').css('display', 'block'); break;
					case "Voted Yes" : $('#div_votes_yes').css('display', 'block'); break;
					case "Voted No" : $('#div_votes_no').css('display', 'block'); break;
				}
		    }
		  </script>
          
          </td>
        <td width="601" height="500" align="center" valign="top">
        
         <?
		   $template->showHelp("Below are displayed details about this law. After proposal, the laws can be voted for 24 hours. Only players having both the energy and equity minimum 5 can propose new laws and only players having both the energy and equity minimum 1 can vote a law. The law is aproved if minimum 51% of vote points aprove it. Not all votes are equal. The vote points are calculated by multiplying the energy and equity. The maximum voting power is 100. For example if you have energy 5 and equity 18, your voting power will be 80.");		   
		   
		   // Vote
		   if ($_REQUEST['act']=="vote_yes" || $_REQUEST['act']=="vote_no")
		      $laws->vote($_REQUEST['act'], $_REQUEST['ID']);
		   
		   // Laws
		   $laws->showLawPanel($_REQUEST['ID']);
		?>
         
         <br /><br />
         <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td align="right">
             <? $template->showSmallMenu("Debate", "Voted Yes", "", "", "Voted No", 3); ?>
             </td>
           </tr>
         </table>
         <br />
         
         <?
		   $laws->showDisqus();
		   $laws->showVotes($_REQUEST['ID'], "yes", false);
		   $laws->showVotes($_REQUEST['ID'], "no", false);
		 ?>
         
         
         
         </td>
        <td width="209" valign="top">
		<?
		   $template->showRightPanel();
		   $template->showAds(); 
		?>
          
          </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="75" background="../../template/GIF/main_bottom.png">&nbsp;</td>
  </tr>
</table>
<br />
<br />
<?
  $template->showBottomMenu();
?>

<script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'chainrepublik'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    
</body>
</html>