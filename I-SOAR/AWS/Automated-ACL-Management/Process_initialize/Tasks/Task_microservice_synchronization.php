<?php

/**
 * This file is necessary to include to use all the in-built libraries of /opt/fmc_repository/Reference/Common
 */
require_once '/opt/fmc_repository/Process/Reference/Common/common.php';

/**
 * List all the parameters required by the task
 */
function list_args()
{
  
}



$device_id = substr($context['aws_region'], 3);
/**
* call to Microservice IMPORT to synchronize the MSA database with the managed AWS VIM
*/
$response = synchronize_objects_and_verify_response($device_id);

$response = _device_read_by_id($device_id);
$response = json_decode($response, true);
if ($response['wo_status'] !== ENDED) {
	$response = json_encode($response);
	echo $response;
	exit;
}
$key = $response['wo_newparams']['login'];
$context["key"] = $key;
$secret = $response['wo_newparams']['password'];
$context["secret"] = $secret;


$response = _device_get_hostname_by_id($device_id);
$response = json_decode($response, true);
if ($response['wo_status'] !== ENDED) {
	$response = json_encode($response);
	echo $response;
	exit;
}
$region = $response['wo_newparams']['hostName'];
$context["region"] = $region;


task_exit(ENDED, "Synchronisation with AWS ".$region." successful");

?>