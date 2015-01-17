<?php

//NOTE: THIS SCRIPT ONLY SUPPORTS ONE SERVER FOR NOW. IF YOU WANT TO CHECK TWO OR MORE SERVERS, PLEASE DUPLICATE THE PROCESS OF SETTING THIS UP.

require_once "listener.php"; //lets load the listener script :D

/////////////////////////
// DETAILS TO FILL IN: //
/////////////////////////

$pocketmine_server_port = "19132"; // must be compatible with 
$screen_id = ""; // google how to get screen id (screen name also works if u have set it). dont ask me :P
$mysql_port = "3306"; //if your mysql port is different, then figure it out.
$mysql_hostname = "localhost"; //you can use IP if you want.
$mysql_username = "";
$mysql_password = "";
$mysql_database = ""; //MUST BE THE SAME AS ASR_LOGGER TABLE!!! (table name is different than database :P)

////////////////////////
// OPTIONAL SETTING/S //
////////////////////////
// LOG PER MINUTE TO CHECK SERVER STATUS
$enableLog = false; //if set to true, you can see a lot of spammed messages every minute.

////////////////////////////
// DO NOT TOUCH BELOW PLS //
////////////////////////////

$run = new Listener();
$run->start($pocketmine_server_port, $screen_id, $mysql_hostname, $mysql_username, $mysql_password, $mysql_database, $mysql_port, $enableLog);

?>