<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CAccountant.php";
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
</head>

<body>
<center>    
    
	<?
	  $query="SELECT * 
	            FROM tipuri_produse 
			   WHERE prod LIKE '%BUILD_COM%'";
	
	  $result=$db->execute($query);
	  
	  $db->begin();
	  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
      {
		  $query="INSERT INTO allow_trans 
		                  SET receiver_type=?, 
						      prod=?, 
							  can_buy=?, 
							  can_sell=?, 
							  can_rent=?, 
							  can_donate=?, 
							  max_hold=?, 
							  is_limited=?, 
							  buy_split=?";	
		  
		  $db->execute($query, 
		              "sssssssss", 
					   "ID_COM_CONSTRUCTION", 
					   $row['prod'],
				       "NO",
					   "YES", 
					   "NO", 
					   "NO", 
					   0, 
					   "NO", 
					   "NO");
	  }
	  
	  $db->rollback();
	  print "Done.";
	?>

   
  
  
</center>
  </body>
</html>