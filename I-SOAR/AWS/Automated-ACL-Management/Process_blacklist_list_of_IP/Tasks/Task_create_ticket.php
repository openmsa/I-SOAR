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
 
  create_var_def('ticketing_ip', 'String');
  create_var_def('ticketing_login', 'String');
  create_var_def('ticketing_pwd', 'String');

}

if (!isset($context['ticketing_ip'])) {
  task_warning("No IP address provided for the JIRA server");
}


/*
{
	"fields": {
		"project": {
			"id": "10902"
		},
		"summary": "test ticket",
		"issuetype": {
			"id": "10003"
		},
		"priority": {
			"id": "1"
		},
		"description": "description"
	}

}
*/

$new_ip_list_string = $context['new_ip_list_string'];
$new_ip_list_string = str_replace(" ", "\n", $new_ip_list_string);

$field_array = 
	array ("fields" =>
		array (
			"project" => array ("id" => "10902"), 
			"issuetype" => array ("id" => "10003"),
			"priority" => array ("id" => "1"),
			"description" => "$new_ip_list_string",
			"summary" => "new IP blacklisted")
	
);


$json_body = json_encode($field_array);

$curl_cmd = "{$CURL_CMD} -s -X POST  -H 'Content-Type: application/json' -u ".$context['ticketing_login'].":".$context['ticketing_pwd']." http://".$context['ticketing_ip']."/rest/api/2/issue "." -d '".pretty_print_json($json_body)."'";
logToFile($curl_cmd);

$response = perform_curl_operation($curl_cmd, "EXECUTE COMMAND");
$response = json_decode($response, true);

$wo_newparams = $response['wo_newparams'];

$ticket_info = $wo_newparams['response_raw_headers'];

logToFile("** " . $ticket_info);
$ticket_info_array = json_decode($ticket_info, true);
//logToFile(debug_dump($ticket_info_array));

$ticket_key = $ticket_info_array['key'];
logToFile("Ticket Id: " . $ticket_key);

task_success("Created ticket: " . $ticket_key);

?>