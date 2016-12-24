# Matchmaking
A php server where 2 people on the internet can coordonate for a match of [age of empires2](https://en.wikipedia.org/wiki/Age_of_Empires_II).

## Using
A client simply has to connect to the server which hosts this software. Afterwards, that user can create a lobby, only requiring the 2 names. The 2 persons are allocated a code, which grants them access to modify the game. The first player picks the map, which is made public as soon as the change occurs. The player nation pick can only be made once, and it is revealed to your opponent once both players have picked.

For security reasons, the access code of both players is changed once they log in, and a warning message is displayed afterwards, notifying the users of wether their connection is compromised or not - we wouldn't want the host to know what we pick, cheeky devil that he is.

## Installing

This project requires a sql server on the local host(source can be changed to accommodate for external database) and a server supporting PHP with the mysqli module.

To configure the database, run the `db_init` script as such: `mysql -p -u root < db_init` . This script will create the database, tables as well as the user required to access the database.

The files under the `src/` directory should be transferred to the server for viewing.

## Somewhat relevant links

Project made for a scrub-level tournament organised by [Hydronum](https://www.twitch.tv/hydronum).

Come play along for our weekly games - all information [here](https://docs.google.com/spreadsheets/d/1scP9VmK15n-cxTyfVw2GAnVjvLPmBdSu_XsvoAsxaFY/edit)
