<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../template/CTemplate.php";
  include "CTemp.php";
  include "CProdTypes.php";
  
  $db=new db();
  $template=new CTemplate();
  $temp=new CTemp($db);
  $prod=new CProdTypes($db, $temp);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Product Types</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="../../../dd.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body>
<center>    
    
	<?
	  $query="SELECT *,
	                 tpt.name AS tools_name,
					 tpb.name AS build_name
	            FROM tipuri_companii AS tc
				JOIN tipuri_produse AS tpt ON tpt.prod=tc.utilaje
				JOIN tipuri_produse AS tpb ON tpb.prod=tc.cladire
			   WHERE tc.ID>?";
	
	  $result=$db->execute($query, "i", 0);
	  
	  $db->begin();
	  
	  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
      {
		 // Market ID
		 $mktID=rand(10000, 10000000);
		  
		 // Insert tools market
		 $query="INSERT INTO assets_mkts 
		                 SET adr=?, 
				             asset=?, 
					         cur=?,
				       	     name=?, 
					         description=?, 
					         decimals=?, 
					         block=?, 
					         expires=?, 
					         last_price=?, 
					         ask=?, 
					         bid=?,
							 mktID=?";
		  
		  $db->execute($query, 
					   "sssssiiiiiii", 
						"default", 
					    $row['utilaje'], 
						"CRC", 
						$row['tools_name']." Market", 
						$row['tools_name']." Market", 
						0,
						0,
						0,
						1,
						1,
						0,
						$mktID);
		  
		  // Pos ID
		  $posID=rand(10000, 10000000);
		  
		 // Insert tools market pos
		 $query="INSERT INTO assets_mkts_pos 
		                 SET adr=?, 
						     mktID=?, 
							 tip=?, 
							 qty=?, 
							 price=?, 
							 block=?, 
							 orderID=?, 
							 expires=?, 
							 cost=?";
		  
		  $db->execute($query, 
					   "sisiiiiii",
						"default",
						$mktID,
					    "ID_SELL",
						25,
						1,
						0,
					    $posID,
					    0,
						0);
		  
		 // ---------------------------- BUILDINGS ----------------------------------------
		  
		  // Market ID
		 $mktID=rand(10000, 10000000);
		  
		 // Insert tools market
		 $query="INSERT INTO assets_mkts 
		                 SET adr=?, 
				             asset=?, 
					         cur=?,
				       	     name=?, 
					         description=?, 
					         decimals=?, 
					         block=?, 
					         expires=?, 
					         last_price=?, 
					         ask=?, 
					         bid=?,
							 mktID=?";
		  
		  $db->execute($query, 
							   "sssssiiiiiii", 
							   "default", 
							   $row['cladire'], 
							   "CRC", 
							   $row['build_name']." Market", 
							   $row['build_name']." Market", 
							   0,
							   0,
							   0,
							   1,
							   1,
							   0,
							   $mktID);
		  
		  // Pos ID
		  $posID=rand(10000, 10000000);
		  
		 // Insert tools market pos
		 $query="INSERT INTO assets_mkts_pos 
		                 SET adr=?, 
						     mktID=?, 
							 tip=?, 
							 qty=?, 
							 price=?, 
							 block=?, 
							 orderID=?, 
							 expires=?, 
							 cost=?";
		  
		  $db->execute($query, 
					   "sisiiiiii",
						"default",
						$mktID,
					    "ID_SELL",
						25,
						1,
						0,
					    $posID,
					    0,
						0);
		  
	  }
	  
	  $db->commit();
	  print "Done.";
	?>

   
  
  
</center>
  </body>
</html>