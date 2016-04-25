# ta_db
Site to gather information from online game tiberiumalliances.com

Site was written on php+javascript and uses MySQL database.

## Site can do:
* load alliances list from game server
* load players list from game server
* load attacks list of player's alliance from game server
* maintain and admin list of players for ff-run
* statistics about user attacks activity in your alliance

Look [screenshots](https://github.com/sharpensteel/ta_db/tree/master/screenshots) to have an idea. 


## Setup
1. Create database from [sql dump](https://github.com/sharpensteel/ta_db/tree/master/install/db_dump.sql).<br />
  Dump contains example data to give you an idea.<br />   
  Therefore, before real use, you should truncate those tables: `alliance`, `attack`, `player`, `player_update_history`, `user`
  
2. Copy `protected/config/local_sample.php` to `protected/config/local.php`<br />
   this file will be part of configuration<br />
   you should change values `DB_NAME_HERE`, `USERNAME_HERE`, `PASSWORD_HERE`, `SECRET_PASSWORD_HERE` to actual values

3. open in browser sub-page `/ta_stuff`, read it and install client script, it will help you to update "game world" data convenient and regularly   

4. if you successed at step 3, you will see panel in game. Update world data from panel
 
5. Go to database and setup tables `global_data` and `alliance`<br />
  look at [screenshot]("https://github.com/sharpensteel/ta_db/tree/master/screenshots/10. database table 'alliance' needs setup.JPG") to have an idea how to setup `alliance` table.
  


 
