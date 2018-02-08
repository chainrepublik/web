<?
class CDropDown
{
	function CDropDown($db, $ID="test", $width=100, $height=250)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$lines=0;
		$this->ID=$ID;
		$this->width=$width;
		$this->height=$height;
		$this->data="var ddBasic = [";
	}
	
	function addLine($txt, $val, $selected, $img="", $description="", $class_big="bold_mov_18", $class_small="simple_gri_12", $last=false)
	{
		$this->lines++;
		
		if ($this->lines>1) $this->data=$this->data.",";
		
        $this->data=$this->data."{ text: \"<span class='".$class_big."'>&nbsp;&nbsp;".$txt."</span>\","; 
	    $this->data=$this->data."value: \"".$val." \","; 
	    
		if ($selected==true)
		   $this->data=$this->data."selected:true,"; 
		else
		    $this->data=$this->data."selected:false,";
	    
		if ($img!="") 
		{
			$this->data=$this->data."imageSrc: \"".$img."\","; 
	        $this->data=$this->data."description : \"<span class='".$class_small."'>&nbsp;&nbsp;".$description."</span>\"}";
		}
		else $this->data=$this->data."}";
	}
	
	function fillMarketType()
	{
		$this->addLine("Global", "ID_GLOBAL", true, "../../GIF/global_small.png", "Global market");
		$this->addLine("Local", "ID_LOCAL", false, "../../GIF/local_small.png", "Local market");
	}
	
	function fillCountries()
	{
		$query="SELECT * FROM countries"; 
		
		$result=$this->kern->execute($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$this->addLine($row['country'], 
			                $row['code'], 
							true, 
							"../../GIF/flags/35/".$row['code'].".gif", 
							"Global market");
							
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	         $this->addLine($row['country'], 
			                $row['code'], 
							false, 
							"../../GIF/flags/35/".$row['code'].".gif", 
							"Global market");
	}
	
	function generate()
	{
		?>
        
		     <div id="div_<? print $this->ID; ?>">
             <select name="<? print $this->ID; ?>" id="<? print $this->ID; ?>">     
             </select>
             </div>
             
             <input id="h_<? print $this->ID; ?>" name="h_<? print $this->ID; ?>" value="dd" type="hidden"/>
                  
             <script>
			 <?
			   $this->data=$this->data."];"; 
			   print $this->data; 
			 ?>

              $('#div_<? print $this->ID; ?>').ddslick({data: ddBasic,
                                        width: <? print $this->width; ?>,
										height: <? print $this->height; ?>,
	                                    imagePosition: "left",
                                        selectText: "<span class='simple_gri_14'>Recipient</span>",
                                        onSelected: function (data) {
                                           $('#h_<? print $this->ID; ?>').val(data.selectedData.value);
										   dd_changed('<? print $this->ID; ?>', data.selectedData.value);
                                        }});
                </script>
                
                <?
	
	}
}
?>