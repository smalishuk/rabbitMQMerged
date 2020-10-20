#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');




function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  $pubkey = $request['pubkey'];
  $privkey = $request['privkey'];
  $op = "error in processing";
  switch ($request['action']) {

		# this was my hacky way of getting php to pass the variables
	# correctly when its moved to a shell command (python)
	case "cash": 
		$start = '/home/matt/git/DMZ/IT490/Deliverable_1_script_1.py ';
		$start .= $pubkey;
	        $start .= ' ' . $privkey;	
		$cmd = escapeshellcmd($start);
		$op = shell_exec($cmd);
		break;

	case "positions":
		$start = '/home/matt/git/DMZ/IT490/Deliverable_1_script_2.py';
                $start .= $pubkey;
                $start .= ' ' . $privkey;
                $cmd = escapeshellcmd($start);
                $op = shell_exec($cmd);
                break;

	
	default:
		$emsg = "no valid action for user account given";
	
		$op = "error in processing";

}


  return array("returnCode" => '0', 'message' => $op);
}

$server = new rabbitMQServer("dmzServer.ini","dmzServer");

echo "DMZ Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DMZ Server END".PHP_EOL;
exit();
?>

