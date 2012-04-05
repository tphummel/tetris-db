<?php
/*
Function: rankToPts($rank)
Accepts a rank value and returns the number of pts that are associated with it.
use with efficiency rank and win rank

ARGS - 
$rank = player's rank in previous match
$pCount = # of players in previous match
*/

function rankToPts($rank, $pCount)
{
$pts = -9999;
switch ($pCount)
{
	case 4: //4 players
			switch ($rank)
			{
				case 1:
					$pts = 4;
					break;
				case 2:
					$pts = 3;
					break;
				case 3:
					$pts = 2;
					break;
				case 4:
					$pts = 1;
					break;
			}
		break;
	
	case 3: //3 players
			switch ($rank)
			{
				case 1:
					$pts = 3;
					break;
				case 2:
					$pts = 2;
					break;
				case 3:
					$pts = 1;
					break;
			}
		break;
	
	case 2: //2 players
			switch ($rank)
			{
				case 1:
					$pts = 2;
					break;
				case 2:
					$pts = 1;
					break;
			}
		break;
}

return $pts;
}
?>