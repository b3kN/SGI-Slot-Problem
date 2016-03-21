# SGI-Slot-Problem

### Description
Slot Machine Spin Results is our server end point that updates all player data and features when a spin is completed on the client. We do hundreds of millions of these requests per day, and we would like to see you make a very basic MySQL driven version.

### Data Storage
Create a MySQL database that contains a player table with the following fields:
- Player ID
- Player Name
- Credits
- Lifetime Spins
- Salt Value

### Code
Your code should validate the following request data:
- Hash
- Coins Won
- Coins Bet
- Player ID

Update the player data in MySQL if the data is valid.
Generate a JSON respons with the following data:
- Player ID
- Player Name
- Credits
- Lifetime Spins
- Lifetime Average Return

You can assume that the client making the request has the salt value to make the hash.
