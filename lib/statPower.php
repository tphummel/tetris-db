<?php
function computePower($wpts, $epts, $lps)
{
	return number_format($wpts + (.5 * $epts) + $lps,3);
}
?>
