# Search

This package is meant to be used in your terminal. And allows
working with lots of subdirectories containing git repositories.

## Installation

```bash
composer global require james.xue/search
```

> Make sure to place the ~/.composer/vendor/bin directory in your PATH so the search executable can be located by your system.

## Usage

You can now globally use the command `search`, eg:

```bash
search pin 我是汉字
```

## 涉及镜像

里面涉及到 `config` 中配置的信息，需要启动镜像 [xiaoxuan6/free_api_server](https://hub.docker.com/repository/docker/xiaoxuan6/free_api_server/general) , 接口[文档地址](https://github.com/xiaoxuan6/FreeApiServer)

## Help

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
  help         Display help for a command
  id_card      身份证信息查询
  list         List commands
  pin          汉字转拼音
  proxy        设置 git 本地代理
  translation  [t] 汉字翻译成英文
 un
  un:proxy     删除 git 本地代理
```