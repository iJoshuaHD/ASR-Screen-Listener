<?php
class Listener{
	public $temp = array();
	public $temp_port = array();
	public function start($port, $screen_name, $host, $user, $password, $database, $mysql_port, $goingToLogPerMinute){
		if(!isset($this->temp_port[0])) $this->temp_port[0] = $port;
		$this->loadStartup();
		$this->checkDatabase($host, $mysql_port);
		$db = @new \mysqli($host, $user, $password, $database, $mysql_port);
		if(!$db->connect_error){
			$exists_table_asr = $db->query("SELECT * FROM asr_logger LIMIT 0");
			if($exists_table_asr){
				$this->logger("\x1b[32;1mScreen Listener Enabled!");
				$db->close();
				return $this->loop($port, $screen_name, $host, $user, $password, $database, $mysql_port, $goingToLogPerMinute);
			}else{
				$this->logger("\x1b[0mYou don't have ASR_Logger table in your database!");
				$this->logger("\x1b[0mGet the latest ASR from http://github.com/ijoshuahd/ASR");
				$this->logger("and run it!");
				$this->logger("\x1b[31;1mExiting ...\x1b[0m");
				die();
			}
		}else{
			$this->logger($db->connect_error);
			$this->logger("\x1b[31;1mExiting ...\x1b[0m");
			die();
		}
	}
	public function loop($port, $screen_name, $host, $user, $password, $database, $mysql_port, $goingToLogPerMinute){
		if(!isset($this->temp_port[0])) $this->temp_port[0] = $port;
		$this->checkDatabase($host, $mysql_port);
		$db = @new \mysqli($host, $user, $password, $database, $mysql_port);
		if(!$db->connect_error){
			$exists_table_asr = $db->query("SELECT * FROM asr_logger LIMIT 0");
			if($exists_table_asr){
				$req = $db->query("SELECT processid, timestamp, port FROM asr_logger WHERE port='$port' LIMIT 1")->fetch_assoc();
				$interval_time = 1;
				$restart_allocation = $interval_time + 2;
				$algo_time = ((time() - $req['timestamp']) /60 % 60);
				$loop_time = abs($interval_time - ((time() - $algo_time)));
				$now = intval(time());
				if($req){
					while($req){
						if($algo_time > $restart_allocation){
							if(!isset($this->temp[$port]['frozen_times'])) $this->temp[$port]['frozen_times'] = 0;
							else $this->temp[$port]['frozen_times'] = $this->temp[$port]['frozen_times'] + 1;
							$this->logger("Server was frozen for " . abs($algo_time - $restart_allocation) . " minute.");
							shell_exec("screen -p 0 -S $screen_name -X stuff 'save-all\n'");
							sleep(1);
							shell_exec("kill -Kill " . $req['processid']);
							$this->logger("Server forcedly Restarted.");
							$db->close();
							return $this->loop($port, $screen_name, $host, $user, $password, $database, $mysql_port, $goingToLogPerMinute);
						}else{
							if($goingToLogPerMinute){
								if($algo_time > 1 and $algo_time < 3){
									$this->logger("It seems the server is frozen.");
									$this->logger("Lets give it another more minute.");
								}else{
									$this->logger("Server is running fine.");
								}
							}
							$db->close();
							sleep(60);
							return $this->loop($port, $screen_name, $host, $user, $password, $database, $mysql_port, $goingToLogPerMinute);
						}
					}
				}else{
					$this->logger("\x1b[0m$port doesn't exist in the database.");
					$this->logger("\x1b[31;1mExiting ...\x1b[0m");
					die();
				}
			}else{
				$this->logger("\x1b[0mYou don't have ASR_Logger table in your database!");
				$this->logger("\x1b[0mGet the latest ASR from http://github.com/ijoshuahd/ASR");
				$this->logger("and run it!");
				$this->logger("\x1b[31;1mExiting ...\x1b[0m");
				die();
			}
		}else{
			$this->logger($db->connect_error);
			$this->logger("\x1b[31;1mExiting ...\x1b[0m");
			die();
		}
	}
	public function checkDatabase($host, $mysql_port){
		$db_check = @fsockopen($host, $mysql_port, $errno, $errstr, 5);
		if (!$db_check){
			$this->logger("Cant find MySQL Server running.");
			$this->logger("\x1b[31;1mExiting ...\x1b[0m");
			fclose($db_check);
			die();
		}
	}
	public function logger($msg){
		if(isset($this->temp_port[0])){
			if(!isset($this->temp[0]['frozen_times'])){
				$this->temp[$this->temp_port[0]]['frozen_times'] = 0;
			}
			$now = intval(time());
			echo "\x1b[36;1m" . date("H:i:s", $now) . " \x1b[33;1m[". $this->temp_port[0] ."] [".$this->temp[$this->temp_port[0]]['frozen_times']."]\x1b[0m $msg\n";
		}else{
			echo "\x1b[36;1m" . date("H:i:s", $now) . " \x1b[33;1m[00000] [0]\x1b[0m $msg\n";
		}
	}
	public function loadStartup(){
		echo "\n";
		echo "\x1b[35;1mTake Note: This script is false positive. This only helps you restart \nyour server back from stuck on polka or being frozen. I can't guaranty \nif the server really froze or not as we base these events via timestamp.\nI am not held responsible to any data losses or anything that will \nhappen on your server by using this script. Thank you. -iJoshuaHD\n\n";
		$this->logger("\x1b[0;31m===========================");
		$this->logger("\x1b[34;1mASR Screen Listener \x1b[35;1mv1.5.0");
		$this->logger("\x1b[0;31m===========================");
		$this->logger("\x1b[0;36mInitializing ...");
	}
}
?>