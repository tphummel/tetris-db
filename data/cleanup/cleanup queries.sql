/* zero lines in a round where erank is not equal to number of players in round */ 
select p.*, (select count(matchid) from playermatch where matchid = p.matchid) as plyrs 
from playermatch p 
where p.lines = 0 
and erank != (select count(matchid) from playermatch where matchid = p.matchid)

/* match records for zero line performances above */
select * from tntmatch 
where matchid in ( 
	select matchid 
	from playermatch p 
	where p.lines = 0 
	and erank != (select count(matchid) from playermatch where matchid = p.matchid)) 
order by matchdate


/* WRANK TIES */
SELECT c.matchid, c.matchdate, (SELECT COUNT(playerid) FROM playermatch WHERE matchid = c.matchid) AS plyrs, 
(SELECT username FROM player WHERE playerid = a.playerid) AS plyr1, a.wrank AS wrank1, a.time AS time1, a.lines AS lines1, 
(SELECT username FROM player WHERE playerid = b.playerid) AS plyr2, b.wrank AS wrank2, b.time AS time2, b.lines AS lines2  
FROM playermatch a, playermatch b, tntmatch c 
WHERE a.matchid = b.matchid 
AND b.matchid = c.matchid 
AND a.playerid != b.playerid 
AND a.wrank = b.wrank 
AND a.playerid > b.playerid 
ORDER BY a.matchid 

/* ERANK TIES */
SELECT c.matchid, c.matchdate, (SELECT COUNT(playerid) FROM playermatch WHERE matchid = c.matchid) AS plyrs, 
(SELECT username FROM player WHERE playerid = a.playerid) AS plyr1, a.erank AS wrank1, a.time AS time1, a.lines AS lines1, 
(SELECT username FROM player WHERE playerid = b.playerid) AS plyr2, b.erank AS wrank2, b.time AS time2, b.lines AS lines2  
FROM playermatch a, playermatch b, tntmatch c 
WHERE a.matchid = b.matchid 
AND b.matchid = c.matchid 
AND a.playerid != b.playerid 
AND a.erank = b.erank 
AND a.playerid > b.playerid 
ORDER BY a.matchid 

/* LIST ALL MATCHES, HUMAN READABLE, ORDER BY DATE, ID - IS THE SEQUENCE OK?
see purple flag in code book #5. I found that matchid sequence is good within all matchtypes. 
overall the matchids jump all over the place though so, overall sequence will never be possible for matches already completed. 
*/