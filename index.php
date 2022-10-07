<?php
require_once(__DIR__.'/src/Supervisor/IXR_Library.php');
require_once(__DIR__.'/src/Supervisor/API.php');

$php_script = $_SERVER['SCRIPT_NAME'];
$url_root = dirname($_SERVER['SCRIPT_NAME']);
if ($url_root != '/') $url_root .= '/';

$s = require(__DIR__.'/config.php');
$servs = [];
foreach ($s as $srv) {
  $servs[$srv['name']] = $srv['ip'];
}
unset($s, $srv);


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
  case 'reload':
  case 'restart':
    if (!isset($_GET['ip'])) return 'Missing IP from request';
    $ip = $_GET['ip'];

    $api = new \Supervisor\API;
    switch ($_GET['cmd']) {
      case 'reload': $res = $api->reloadConfig($ip); break;
      case 'restart': $res = $api->restart($ip); break;
      default: $res = false;
    }
    return $_GET['cmd'] .' of '.$ip.' '.($res ? 'succesful' : 'failed');
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
  if (is_array($status[$name]['pid'])) {
    // It is broken!
    $status[$name]['supervisor'] = 'FAILED';
    $status[$name]['pid'] = 'N/A';
    $status[$name]['services'] = [];
  }

  $status[$name]['running_services'] = 0;
  $status[$name]['total_services'] = 0;
  foreach ($status[$name]['services'] as &$srv) {
    $srv['running'] = $srv['state'] == $api::STATE_RUNNING;
    if ($srv['running']) ++$status[$name]['running_services'];
    ++$status[$name]['total_services'];
  }
}

include(__DIR__.'/views/supervisorui.html');

