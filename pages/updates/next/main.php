<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $ads=new CAds($db, $template);
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
<link href="../../home/overview/style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/png" href="../../template/GIF/favico.png"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
</head>

<body background="./GIF/back.jpg" style="background-size:cover; background-repeat:no-repeat; background-color:#000000">
<center>
<br /><br />
<div class="panel panel-default" style="width:800px" align="center">
  <div class="panel-body">
  
  <table width="100%">
  <tr><td valign="top">
  
  
  
 <iframe width="770" height="433" src="https://www.youtube.com/embed/9v9Ipk1Oc9A" frameborder="0" allowfullscreen></iframe>

  
  </td></tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr><td><table width="100%" border="0" cellpadding="0" cellspacing="5">
    <tbody>
      <tr>
        <td align="center"><a href="GIF/2_1.jpg" target="_blank"><img src="GIF/2_1.jpg" width="100" height="76" alt=""/></a></td>
        <td align="center"><a href="GIF/2_2.jpg" target="_blank"><img src="GIF/2_2.jpg" width="100" height="75" alt=""/></a></td>
        <td align="center"><a href="GIF/2_3.jpg" target="_blank"><img src="GIF/2_3.jpg" width="100" height="68" alt=""/></a></td>
        <td align="center"><a href="GIF/2_4.jpg" target="_blank"><img src="GIF/2_4.jpg" width="100" height="75" alt=""/></a></td>
        <td align="center"><a href="GIF/2_5.jpg" target="_blank"><img src="GIF/2_5.jpg" width="100" height="75" alt=""/></a></td>
        <td align="center"><a href="GIF/2_6.jpg" target="_blank"><img src="GIF/2_6.jpg" width="100" height="75" alt=""/></a></td>
        <td align="center"><a href="GIF/2_7.jpg" target="_blank"><img src="GIF/2_7.jpg" width="100" height="74" alt=""/></a></td>
      </tr>
    </tbody>
  </table></td></tr>
  
  <tr>
    <td class="font_20">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_20"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td width="62%" align="left" class="font_10">The demo versions are not connected to our servers and they show only a small feature set. All progress will be lost when the first beta version goes online.</td>
          <td width="2%" align="right">&nbsp;&nbsp;</td>
          <td width="19%" align="right"><a href="win.zip" class="btn btn-success">Win Demo Download</a></td>
          <td width="1%" align="right">&nbsp;&nbsp;</td>
          <td width="19%" align="right"><a href="mac.zip" class="btn btn-warning">OSX Demo Download</a></td>
        </tr>
      </tbody>
    </table></td>
  </tr>
  <tr>
    <td class="font_20">&nbsp;</td>
  </tr>
  <tr><td class="font_20"><strong>What's next</strong></td></tr>
  <tr><td><hr /></td></tr>
  <tr>
    <td class="font_16">In October and November we have launched 2 surveys where new players were invited to rate chainrepublik 3D area. Mostly were negative. The main complaint was that the time spent on map was way too short and the gameplay  (shooting zombies in a linear fasion) was boring. Another complaint was the high bullets price that made difficult to get any direct profit from looting. The multiplayer received a better review but the same issues were mentioned (short time on map / expensive bullets / slow energy recovery).</td></tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16">So we have redesigned everything from top to bottom to meat the player's requirements. As a result, on April, 5, 2017 we will launch version 2.0 of chainrepublik 3D area. Let's review what's new for version 2.0</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>General gameplay changes</strong></td>
  </tr>
  <tr>
    <td class="font_14">First of all we have moved from a linear shooting game to a complex town construction / survival simulation game. The players will be able to explore a large mountain wood area where they will hunt, try to survive and build a city. There will be NPCs (zombies) to kill but not in a linear way. Players will be able to gain up to 15% energy by hunting / eating fruits / vegetables or from farming. As a result you can spend hours / days on the single player map / multiplayer area. Also arrows / bullets will be produced by players using specialized buildings. Energy is still important because bonuses from looting zombies or hitting players on multiplayer map will be paid based on energy. So you will still need clothes / drinks to get your energy over 15%.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>Citybuilding</strong></td>
  </tr>
  <tr>
    <td class="font_14">You will start from a couple of buildings and then expand your town. In the final version, over 20 building types will be available from blacksmiths to churches. You will need mines in order to extract raw materials like iron ore, stone, coal and so on. Some company types like stone quaries or iron companies will be &quot;moved&quot; from web area to 3D. Some products will be produced in the old fashion while others like arrows / bullets will be produced by towns.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>Hunting / exploring / fighting zombies</strong></td>
  </tr>
  <tr>
    <td class="font_14">In order to recover your energy fast (up to 15%) players can hunt or eat forest plants / fruits. We have implemented 5 forest animals (rabbits, foxes, deers, boars and bears). Hunting will provide players with meat and skin (leather). Arrows can be produced by armories using basic raw materials so &quot;bullets price&quot; will not be an issue anymore.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>Multiplayer area</strong></td>
  </tr>
  <tr>
    <td class="font_14">The basic gameplay will remain the same ( every minute a new ressuply box is parachutted). We have implemented anti-cheat measures and now it's impossible to move at high speed or &quot;teleport&quot; on long distances. In case you think a player cheats there is the posibility of &quot;quick ban&quot; (if at least 5 players flags a player he / she will be banned from using multiplayer unti a team member reviews player's activity).</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>Graphics</strong></td>
  </tr>
  <tr>
    <td class="font_14">In the old version graphics was rated 4.2 / 5 but we keep improving this aspect. We used professionally scanned assets for environment and improved the post-processing effects. We used PBR materials for buildings / props to further increase the realism. Watch the video to see the end result.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14">Stay tuned. We have many suprises for the  months to come. Dont forget to use the contact us form for reporting any bug / performance issue. This is still a beta product.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_12">chainrepublik team.</td>
  </tr>
  </table>
  
  </div>
</div>
</center>
</body>
</html>