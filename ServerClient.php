<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


##
# So this file serves as a storage space for the individual functions the
# website uses to enable the user to log in, perform actions on the 
# DMZ API, etc. 
#
# This amounts to specifying in the request array the function code
# being performed, taking the necessary  data in variables and adding 
# them into the request array as well, sending the request and 
# passing back the response for the user to see. 
#
# All of the requests are  packaged in this manner and passed on by
# the rabbitMQ Broker service with a switch case for the swrequest['type']
# var to determine where it's going, passing the whole request along to the
# intended recipient and then passing it's response back to the sender. 

$sockerr = [
	'message' => "Could not send message to RabbitMQ Server, check the configuration.",
	];

function dologin($a, $b) {
	$client = new rabbitMQClient("rabbit.ini","testServer");
	$req = [
		"type" => "login",
		"username" => $a,
		"password" => $b,
	];
	return $client->send_request($req);

	}



function getcash ($userid) {

	$client = new rabbitMQClient("rabbit.ini", "testServer");
	$req = [
	"type" => "dmz",
	"uid" => $userid,
	"action" => "cash",
	];
	
	$response = $client->send_request($req);
       	
	return isset($reponse['message']) ? $response : $sockerr;
	}



function getpos($userid) {

	$client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        "type" => "dmz",
        "uid" => $userid,
	"action" => "pos",
	];

	$response = $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
	}



function putorder($userid, $symbol, $number) {

	$client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        "type" => "dmz",
        "uid" => $userid,
	"sym" => $symbol,
	"num" => $number,
	"action" => "order",
	];

	$response =  $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
	}	



function callBot0($uid, $symbol) {

	$client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        "type" => "dmz",
        "uid" => $uid,
        "action" => "bot0",
	"botsym" => $symbol,
		];

	$response = $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
}



function callBot1($uid, $symbol) {

        $client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        "type" => "dmz",
        "uid" => $uid,
        "action" => "bot1",
	"botsym" => $symbol,
		];
	
	$response = $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
	}



function callBot2($uid, $symbol) {
	
	$client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        	"type" => "dmz",
        	"uid" => $uid,
		"action" => "bot2",
		"botsym" => $symbol,
		];
	$response = $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
	}



function newWatchedStock($uid, $new_stock, $new_price) {
	
	$client = new rabbitMQClient("rabbit.ini", "testServer");
	$req = [
        	"type" => "dmz",
		"uid"	=> $uid,
		"action" => "add",
		"symbol" => $new_stock,
		"price" => $new_price,
		];
	$response = $client->send_request($req);

	return isset($reponse['message']) ? $response : $sockerr;
	}



function checkWatchedStocks($uid) {

	$client = new rabbitMQClient("rabbit.ini", "testServer");
        $req = [
        "type" => "dmz",
        "uid" => $uid,
	"action" => "watch",
		];

	$response = $client->send_request($req);
	return isset($reponse['message']) ? $response : $sockerr;
	}


