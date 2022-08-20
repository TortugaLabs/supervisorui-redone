# supervisorui-redone

A reworked version of [supervisorui](https://github.com/Tabcorp/supervisorui).

# Supervisor multi-server dashboard


## Introduction

This is a simple, quick and dirty dashboard that gives you an
at-a-glance look at the state of all your [supervisor](http://supervisord.org/)
using webservers. Also provides the ability to stop and start individual
processes. It uses

* [The Incutio XML-RPC Library](http://scripts.incutio.com/xmlrpc/)
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/)

## Configuration

Copy `config.php.dist` as `config.php` and edit as appropriate.

Supervisor also needs to be configured to allow XML-RPC access.


Supervisor (`/etc/supervisord.conf`) changes to enable XML-RPC access:

```ini
[inet_http_server]         ; inet (TCP) server disabled by default
port=*:9001
```

## Screenshot



## Authors

* supervisorui-redone: [Alejandro Liu](https://github.com/alejandroliu)
* supervisorui: [Marcus Gatt](https://github.com/mrgatt)

## License

* supervisorui-redone: &copy; 2020 [Alejandro Liu](https://github.com/alejandroliu)
* supervisorui: &copy; 2012 Luxbet Pty Ltd.

Released under [The BSD 3 clause License](http://www.opensource.org/licenses/BSD-3-Clause)


# TODO

- [supervisord API](http://supervisord.org/api.html#process-control)
- reloadConfig()
- restart()
- send signal:
  - USR1, graceful restart (apache)
  - HUP, restart now (apache)
  - signalProcess(name, signal)
    - Send an arbitrary UNIX signal to the process named by name
    - @param string name Name of the process to signal (or ‘group:name’)
    - @param string signal Signal to send, as name (‘HUP’) or number (‘1’)
    - @return boolean
