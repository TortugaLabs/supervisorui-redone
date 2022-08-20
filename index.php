<?php
require_once(__DIR__.'/src/Supervisor/IXR_Library.php');
require_once(__DIR__.'/src/Supervisor/API.php');

$php_script = escapeshellarg($_SERVER['SCRIPT_NAME']);
$url_root = dirname($_SERVER['SCRIPT_NAME']);
if ($url_root != '/') $url_root .= '/';

$servs = require(__DIR__.'/config.php');

$id_cnt = 0;
function idgen(): int {
  global $id_cnt;
  return ++$id_cnt;
}

function run_cmds() {
  if (!isset($_GET['cmd'])) return;
  switch ($_GET['cmd']) {
  case 'stopstart':
    if (!(isset($_GET['mode']) && isset($_GET['ip']) && isset($_GET['name']))) return 'Missing arguments for stopstart command';
    list($mode,$ip,$name) = [$_GET['mode'],$_GET['ip'],$_GET['name']];

    $api = new \Supervisor\API;
    if ($mode) { // Is running...
      $res = $api->stopProcess($ip,$name);
      return $res ? ('Succesfully stopped '.$name.' on '.$ip) :
		    ('Error stoping '.$name.' on '.$ip);
    } else {
      $res = $api->startProcess($ip,$name);
      return $res ? ('Succesfully started '.$name.' on '.$ip) :
		    ('Error starting '.$name.' on '.$ip);
    }
    break;
  default:
    return 'Unknown command: '.$_GET['cmd'];
  }
}


$msg = run_cmds();

$status = [];
foreach ($servs as $name => $ip) {
  $api = new \Supervisor\API;
  $status[$name] = [
    'ip' => $ip,
    'supervisor' => $api->getIdentification($ip)
		.' '.$api->getSupervisorVersion($ip)
		.' (API:'.$api->getAPIVersion($ip).')',
    'pid' => $api->getPID($ip),
    'services' => $api->getAllProcessInfo($ip),
  ];
  foreach ($status[$name]['services'] as &$srv) {
    $srv['running'] = $srv['state'] == $api::STATE_RUNNING;
  }
}

include(__DIR__.'/views/supervisorui.html');

