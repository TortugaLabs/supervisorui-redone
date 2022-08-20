
function ffToggle(cur,next) {
  document.getElementById(next).style.display = 'block';
  document.getElementById(cur).style.display = 'none';
}

function srvcmd(running,superv,srvname) {
  console.log('php_script: '+ PHP_SCRIPT);
  console.log('running: '+running);
  console.log('superv: ' + superv);
  console.log('srvname: ' + srvname);

  window.location.href = PHP_SCRIPT + '?cmd=stopstart' +
	'&mode=' + running +
	'&ip=' + superv +
	'&name=' + srvname;
}
