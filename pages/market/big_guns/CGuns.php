<?php
class CGuns
{
    function CGuns($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showDestroyerDD()
	{
		?>
            
            <br>
            <table width="550px">
			<tr><td align="right">
            <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Category<span class="caret"></span></button>
            <ul class="dropdown-menu">
	        <li><a href="main.php?trade_prod=ID_NAVY_DESTROYER">Nay Destroyers</a></li>
			<li><a href="main.php?trade_prod=ID_MISSILE_SOIL_SOIL">Surface to surface missiles</a></li>						   
			</ul>
            </div>
			</td></tr>
            </table>

        <?php
	}
	
	function showJetsDD()
	{
		?>
            
            <br>
            <table width="550px">
			<tr><td align="right">
            <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Category<span class="caret"></span></button>
            <ul class="dropdown-menu">
	        <li><a href="main.php?trade_prod=ID_JET_FIGHTER">Jet Fighters</a></li>
			<li><a href="main.php?trade_prod=ID_MISSILE_AIR_SOIL">Air to surface missiles</a></li>						   
			</ul>
            </div>
			</td></tr>
            </table>

        <?php
	}
	
	function showTanksDD()
	{
		?>
            
            <br>
            <table width="550px">
			<tr><td align="right">
            <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Category<span class="caret"></span></button>
            <ul class="dropdown-menu">
	        <li><a href="main.php?trade_prod=ID_TANK">Tanks</a></li>
			<li><a href="main.php?trade_prod=ID_TANK_ROUND">Tank Rounds</a></li>						   
			</ul>
            </div>
			</td></tr>
            </table>

        <?php
	}
	
	function showBalisitcDD()
	{
		?>
            
            <br>
            <table width="550px">
			<tr><td align="right">
            <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Category<span class="caret"></span></button>
            <ul class="dropdown-menu">
	        <li><a href="main.php?trade_prod=ID_MISSILE_BALISTIC_SHORT">Short Range Balistic Missiles</a></li>
			<li><a href="main.php?trade_prod=ID_MISSILE_BALISTIC_MEDIUM">Medium Range Balistic Missiles</a></li>	
			<li><a href="main.php?trade_prod=ID_MISSILE_BALISTIC_LONG">Long Range Balistic Missiles</a></li>	
			<li><a href="main.php?trade_prod=ID_MISSILE_BALISTIC_INTERCONTINENTAL">Intercontinental Range Balistic Missiles</a></li>	
			</ul>
            </div>
			</td></tr>
            </table>

        <?php
	}
}
?>