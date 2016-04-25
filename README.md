# ta_db
Site to gather information from online game tiberiumalliances.com

Site was written on php+javascript and uses MySQL database.

## Features
* load alliances list from game server
* load players list from game server
* load attacks list of player's alliance from game server
* maintain and admin list of players for Forgotten Fortress attack
* statistics about users attacks activity in your alliance

Look [screenshots](https://github.com/sharpensteel/ta_db/tree/master/screenshots) to have an idea. 


## Setup
1. Create database from [sql dump](https://github.com/sharpensteel/ta_db/tree/master/install/db_dump.sql).<br />
  Dump contains example data to give you an idea.   
  Therefore, before real use, you should truncate those tables: `alliance`, `attack`, `player`, `player_update_history`, `user`
  
2. Copy `protected/config/local_sample.php` to `protected/config/local.php`<br />
   This file will be part of configuration. You should edit it, change values `DB_NAME_HERE`, `USERNAME_HERE`, `PASSWORD_HERE`, `SECRET_PASSWORD_HERE` to actual values.

3. Open in browser sub-page `/ta_stuff`, read it and install client script, it will help you to update "game world" data convenient and regularly   

4. If you successed at step 3, you will see panel in game. Update world data from panel
 
5. Open your MySql database and setup tables `global_data` and `alliance`.<br />
  Look at [screenshot](https://github.com/sharpensteel/ta_db/blob/master/screenshots/10.table_'alliance'_needs_setup.JPG) to have an idea how to setup `alliance` table.
  


 
