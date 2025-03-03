![minecraft_title](https://github.com/user-attachments/assets/3534513d-790d-4bd6-9251-73f83247cb22)

## Description
Duel Plugin is a Minecraft server plugin that allows players to challenge each other in PvP duels in custom arenas. Administrators can create and manage arenas, while players can send and accept duel requests with simple commands.

**Note:** This plugin is not 100% finished, and there may be bugs that will be fixed in future updates.

## Commands :spades:

| Command | Description | Permission |
| --- | --- | --- |
| `/duel create <arena name> <world name>` | Creates a new duel arena in the specified world | `duels.arena` |
| `/duel delete <arena name>` | Deletes an existing arena | `duels.arena` |
| `/duel setpos 1/2 <arena name>` | Sets the spawn positions of players in the arena | `duels.arena` |
| `/duel <player name>` | Sends a duel request to another player | `none` |
| `/duel accept <player name>` | Accepts a duel request | `none` |
| `/duel decline <player name>` | Declines a duel request | `none` |

## Installation
1. Download the `.phar` file of the plugin.
2. Place it in the `plugins` folder of your Minecraft server.
3. Restart the server!

### Future Updates:
- [x] Messages via config
- [ ] Add a UI to the /duel command and there will be arenas available with queue
- [ ] Add Stats saved with Database
- [ ] Add a Scoreboard to Duels and even Duel Game Time
- [ ] Add check that arena locations can only be set in set worlds
- [ ] Make a config to set the kits for each Duel

## Contribute
If you want to contribute to the development of the plugin, feel free to submit a pull request or report any issues in the repository's Issues section.
