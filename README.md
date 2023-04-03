# Search

This package is meant to be used in your terminal. And allows
working with lots of subdirectories containing git repositories.

## Installation

```bash
composer global require james.xue/search
```

> Make sure to place the ~/.composer/vendor/bin directory in your PATH so the search executable can be located by your system.

## Env system variable

Copy the `config.json` file in the root directory to the current directory and modify the configuration file, And execute the following command：

```bash
search env:init ./config.json
```

Can also a value can be set individually

```shell
search config set --key=xxx --value=xxx
```

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
  completion           Dump the shell completion script
  config               设置配置信息
  help                 Display help for a command
  install              [i] 下载安装包
  list                 List commands
  openai               使用 ChatGPT 搜索
  proxy                设置代理
  send                 给公众号发送消息
  upload               上传本地图片/文件到远程
  user                 生成用户信息
 actions
  actions:go:push      收藏 go 开源第三方包
  actions:push         随记提交到 github
  actions:screen-shot  根据url截图并生成图片链接
  actions:upload       上传本地图片到 github
 env
  env:init             初始化配置信息
 git
  git:push             [gh] git 提交数据
 un
  un:proxy             删除本地代理
```
