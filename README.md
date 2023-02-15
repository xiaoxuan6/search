# Search

This package is meant to be used in your terminal. And allows
working with lots of subdirectories containing git repositories.

## Installation

```bash
composer global require james.xue/search
```

> Make sure to place the ~/.composer/vendor/bin directory in your PATH so the search executable can be located by your system.

## List

```shell
$ search list
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  config          设置配置信息
  help            Displays help for a command
  list            Lists commands
  send            给公众号发送消息
  user            生成用户信息
 proxy
  proxy:composer  设置 composer 本地代理
  proxy:git       设置 git 本地代理
 un
  un:proxy        删除本地代理
```