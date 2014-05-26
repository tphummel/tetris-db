- output match_id and player_id to redis

- 2 player, only require entire time once
future report ideas:
  - head to head breakdown
    - player vs player
      - summary by game type
    - single player
      - vs each other player

- wins in "ugly" rounds. 'ugly wins'

- perf rarity
  - how many times has the line total been done ever, this match type

- global time-line matrix
  lines (y), time (x)

- statistics in R

- add player and location drop-down to perf report
- redis pub/sub on match creation

"lines won"

- move reports to sub dir
  rpPerf: - add date range option
    - refactor queries to reduce duplication
    - move queries to own file
    - field list always the same?
    - add date range to all if set
    - add player dropdown
- match console
  - zero initial power values
  - more rows for trailing averages
    - should be able to abstract each of these rows and make them take less space
    - last 10 games
    - last 10 vs each opp
  - test functionality in lib/ and match/. biz logic only. game state, game scoring, tie breaking
    - so we might be able to refactor someday. split apart match console into directory w/ files

- eff rank. tie goes to the longer round. if still tied, then tied
- naturals +10 seconds, +20 seconds

- 2 player head to head report (dedicated 2player only section)
  - double naturals
  - elo ratings

- individual player profile
  - collecting one of every line # round
  - collecting one of every second # round

- match transcript screen
  - date, timestamp, order, sums
  - with delete button
  - with edit button
