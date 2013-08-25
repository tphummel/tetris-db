--quick delete
delete from tntmatch where matchid = 13609 limit 1;
delete from playermatch where matchid = 13609 limit 4;

CREATE VIEW `tmext` AS 
select `tm`.`matchid` AS `matchid`,`tm`.`matchdate` AS `matchdate`,`tm`.`inputstamp` AS `inputstamp`,`tm`.`enteredby` AS `enteredby`,`tm`.`location` AS `location`,`tm`.`note` AS `note`,`tm`.`universe` AS `universe`,count(`pm`.`playerid`) AS `pCt`,sum(`pm`.`lines`) AS `mLi`,sum(`pm`.`time`) AS `mTi`,(sum(`pm`.`lines`) / sum(`pm`.`time`)) AS `mLps` from (`tntmatch` `tm` join `playermatch` `pm`) where (`pm`.`matchid` = `tm`.`matchid`) group by `tm`.`matchid`;

--count all 2player matches
--select m.*
select count(m.matchid)
from tntmatch m
where (select count(playerid) from playermatch where matchid = m.matchid) = 2

--count 2p performances
select p.*
from playermatch p
where (select count(playerid) from playermatch where matchid = p.matchid) = 2

-- all matches for player today
SELECT pm.lines, pm.time, pm.wrank as wrank, pm.erank as erank, pm.pCt as pCt
	FROM	pmext pm, tmext m, player p 
	WHERE   pm.matchid = m.matchid 
	and p.playerid = pm.playerid 
	and p.username = user 
	and m.matchdate = today

select pm.lines, pm.time, pm.wrank, pm.erank, 
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt
from playermatch pm, tntmatch m, player p
where pm.matchid = m.matchid 
	and p.playerid = pm.playerid 
	and p.username = user 
	and m.matchdate = today

-- player overall summary
select pm.playerid, max(p.username) as username, 
count(pm.matchid) as totgames, sum(pm.time) as tottime, sum(pm.lines) as totlines
from playermatch pm, player p, tntmatch m
where pm.playerid = " . $player . "
AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "' 
and pm.playerid = p.playerid
and pm.matchid = m.matchid
group by pm.playerid

-- player summary by matchtype(# players) 
select pm.playerid, max(p.username) as username, 
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt, 
count(pm.matchid) as totgames, sum(pm.time) as tottime, sum(pm.lines) as totlines
from playermatch pm, player p, tntmatch m
where pm.playerid = " . $player . "
AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "' 
and pm.playerid = p.playerid
and pm.matchid = m.matchid
group by pm.playerid, (select count(playerid) from playermatch where matchid = pm.matchid)
