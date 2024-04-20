[title]: # (API Documentation)

# API

We offer an API to get info about an account. You can search by accountid or rank.

## Season 7 (New)

### Solo

Path variables:

region = eu | us | ap

Query string

accountid = {name} (case-insensitive, although the official accountids are with first letter capital)

OR

rank = {number} (integer)

``` http
GET https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/{region}/get?accountid={name}
```

OR

``` http
GET https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/{region}/get?rank={integer}
```

Examples by accountid:

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/eu/get?accountid=QuilboarTTV>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/us/get?accountid=FastEddieHS>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/ap/get?accountid=buhuidatuan>

Examples by rank:

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/eu/get?rank=1>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/us/get?rank=1>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/solo/ap/get?rank=1>

### Duos

Path variables:

region = eu | us | ap

Query string

accountid = {name} (case-insensitive, although the official accountids are with first letter capital)

OR

rank = {number} (integer)

``` http
GET https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/{region}/get?accountid={name}
```

``` http
GET https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/{region}/get?rank={name}
```

Examples by accountid:

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/eu/get?accountid=DayWeen>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/us/get?accountid=Pocky>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/ap/get?accountid=patience>

Examples by rank:

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/eu/get?rank=1>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/us/get?rank=1>

<https://hearthstone-bg-leaderboard.gamerz-bg.com/api/7/duos/ap/get?rank=1>

## Season 6 (last season)

Last season's API is available for historical use. It works the same as the new one just without the solo/duos in the path

Path variables:

region = eu | us | ap

Query string

accountid = {name} (case-insensitive, although the official accountids are with first letter capital)

``` http
GET https://hearthstone-bg-leaderboard.gamerz-bg.com/api/6/{region}/get?accountid={name}
```

Examples:

https://hearthstone-bg-leaderboard.gamerz-bg.com/api/6/us/get?accountid=jeef

https://hearthstone-bg-leaderboard.gamerz-bg.com/api/6/eu/get?accountid=XQN

https://hearthstone-bg-leaderboard.gamerz-bg.com/api/6/ap/get?accountid=patience

## How this works

As simple as the rest of the 3rd party tools out there. We query the community API for the Hearthstone leaderboards and collect the data so we can present it. We solve the issue that the official leaderboard does not provide a search so people can find themselves or other people and they rank and rating. Also, the community API does not provide a way to do this too with an API call but we do so this opens up great functionality for bots, ingame addons and etc.

## Disclaimer

Our API is currently operating as a public non-authenticated open API which means that you should care for our resources as they are paid from our own pocket. We do not enforce rate limiting but we monitor the access logs so if we see an IP abusing or scraping us, we will block it. We reserve the right to close off the API and put it under authentication if we see that this needs to happen and will provide a mechanism for people to ask for API keys or other forms of authentication, if they want to use it.

## Accuracy of the data

We try to very gently query the Hearthstone community API so we respect them as well, which means that the records returned by our API might not be considered LIVE but should be as close to LIVE as possible.
