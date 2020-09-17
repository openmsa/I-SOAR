<?php
require_once '/opt/fmc_repository/Process/Reference/Common/common.php';

function list_args()
{
  create_var_def('url_IP_provider', 'String');
}

global $CURL_CMD;

$ip_list_string = '';
$previous_ip_list = '';
$previous_ip_list_array = array();

if(isset($context['ip_list_string'])){
$previous_ip_list = $context['ip_list_string'];
$previous_ip_list_array = explode(" ", $previous_ip_list);

logToFile('previous IP list : ' . $previous_ip_list);
}
$context['ip_list_string'] = '';


$url_IP_provider = $context['url_IP_provider'];
 
$curl_cmd = "{$CURL_CMD} -k -s -XGET ".$url_IP_provider;
logToFile('Curl Request : ' . $curl_cmd);


$response = perform_curl_operation($curl_cmd, "LIST IP");

$response = json_decode($response, true);
$wo_newparams = $response['wo_newparams'];


$ip_list_downloaded = $wo_newparams['response_raw_headers'];
$ip_list_array = explode("\n", $ip_list_downloaded);
$ip_list_count = count($ip_list_array);
foreach($ip_list_array as $ip) {
  $ip_list_string = $ip_list_string.' '.$ip;
}

$ip_list_string = trim($ip_list_string);
$context['ip_list_string'] = $ip_list_string;

$list_of_new_ip = get_new_element($previous_ip_list_array, $ip_list_array);
logToFile(debug_dump($list_of_new_ip, 'RESULT'));

$list_of_new_ip_string = '';
foreach($ip_list_array as $ip) {
  $list_of_new_ip_string = $list_of_new_ip_string.' '.$ip;
}
$list_of_new_ip_string = trim($list_of_new_ip_string);
$context['new_ip_list_string'] = $list_of_new_ip_string;

task_success($ip_list_count . ' IP addresses to blacklist');




/**
* returns the array element that are in array2 but not in array1
*/
function get_new_element($array1, $array2) {

  $result=array();
  foreach($array2 as $ip) {
    if (!in_array($ip, $array1)) {
      array_push($result, $ip);
    }
  }

  return $result;

}



?>