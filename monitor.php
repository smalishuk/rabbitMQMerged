<?php
require_once('/home/matt00/git/WebFrontEnd/path.inc');
require_once('/home/matt00/git/WebFrontEnd/get_host_info.inc');
require_once('/home/matt00/git/WebFrontEnd/rabbitMQLib.inc');
require_once('/home/matt00/git/WebFrontEnd/logPing.inc')
# accessory function for the one below
# hits the backup DB with a ref  call, just to see if its there. 
# if the call goes through the function is valid, if not its false
# pings backup database rabbit exchange, returns false is no hit. 
	
	
	function pingbackupdb() {
		
		$clientB = new rabbitMQClient(
			"/home/matt00/git/WebFrontEnd/rabbit.ini",
			"failServer");


		$reqB = [ 

        		"type" => "ref",
			"refid" => "QA",
			];
			
	if  ($clientB->send_request($reqB)) {
		logPing("Backup RDB hit");
		return true;		
	} 

	else {	
		logPing("Backup RDB not hit");
		return false;	
	}
} # end pingbackupdb()


# first ping the main db, if thats not working see if the backup is up and 
# has not been set to active, if its set to active dont do anything, otherwise
# there is a major error. 

function pingrabbitdb() {
	
      $clientA = new rabbitMQClient("/home/matt00/git/WebFrontEnd/rabbit.ini",
	      "testServer");

        $reqA = [
        "type" => "ref",
	"refid" => "QA",
	];

	# get the failover flag status
	$ini = parse_ini_file('/home/matt00/git/WebFrontEnd/rabbit.ini', true);
        $backupflag =  $ini["failServer"]["FLAG"];
	
	logPing('inside pingrabbitdb script');
	# ping main DB
	if ($clientA->send_request($reqA)) {
		
		logPing("Main RDB hit");
	
		# if the server was down and is now up, 
		# reset the files to their original state.
		if ($ini["testServer"]["FLAG"] == -1) {  	
		$cmd = '/home/matt00/git/WebFrontEnd/putBackINI.sh';
		shell_exec($cmd);
		}

		return;	# otherwise just return
	}

	# if main is down...
	elseif (pingbackupdb()) {  #  if the backup is online
		
	# if the backup is up but has not been switched to yet.
		if ($backupflag !== 1) { 
	# run script to replace ini file reference in /var/...php files	
			logPing("Main RDB down, switching to backup");
		      shell_exec('/home/matt00/git/WebFrontEnd/replaceINI.sh');
		return;
		}
		else {  # if the backup is online and has been switched to
			logPing("Backup RDB hit and switched to");
			return;
		}
	} 

	else {   	# if neither are up
		logPing("Neither RDB are up"));
		return;
       	}
	
	
} 



pingrabbitdb();
