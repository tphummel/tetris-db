<?php
function doRanks($players)
{
		/*
		0 - user
		1 - lines
		2 - min
		3 - sec
		4 - time
		5 - winner
		6 - lps
		7 - wrank
		8 - erank
		*/
		
	$effs = array(); //array of efficiencies
	$times = array(); //array of times
	foreach ($players as $p)
	{
		$times[] = $p[4];
		$effs[] = $p[6];
	}
	
	rsort($times);
	rsort($effs);
	
	
	$eCount = 1;
	$tCount = 2;
	foreach ($times as $t)
	{
		foreach ($players as $p)
		{
			if($p[4] == $t)
			{
				p[7] = $tCount;						
			}	
		}
		$tCount++;
	}
	
	

}

?>