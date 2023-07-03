# Search

This package is meant to be used in your terminal. And allows working with lots of subdirectories containing git
repositories.

## Installation

### A、Composer

```bash
composer global require james.xue/search
```

### B、Shell Ubuntu

```bash
curl -O https://ghproxy.com/https://raw.githubusercontent.com/xiaoxuan6/search/main/install.sh && chmod +x ./install.sh && ./install.sh
```

> Make sure to place the ~/.composer/vendor/bin directory in your PATH so the search executable can be located by your system.

## Env system variable

Copy the `config.json` file in the root directory to the current directory and modify the configuration file, And
execute the following command：

```bash
search env:init ./config.json
```

Can also a value can be set individually

```shell
search config set --key=xxx --value=xxx
```

## List

```phpregexp
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
  brew                 ubuntu 安装可执行文件
  completion           Dump the shell completion script
  config               设置配置信息
  help                 Display help for a command
  install              [i] 下载安装包
  list                 List commands
  new                  Create a new Laravel application
  ocr                  图片文字提取
  proxy                设置代理
  send                 给公众号发送消息
  tag                  git 设置版本号并推送远程
  update               更新 search 版本到最新版本
  upload               上传本地图片/文件到远程
  user                 生成用户信息
 actions
  actions:go:push      [agp] 收藏 go 开源第三方包
  actions:push         [ap] 随记提交到 github
  actions:screen-shot  根据url截图并生成图片链接
 env
  env:init             初始化配置信息
 file
  file:upload          将本地文件上传到github release
 git
  git:push             [gh] git 提交数据
  git:workdir          [gw] 设置git工作目录
 proxy
  proxy:local          [local] 将本地网址代理到外网
 un
  un:proxy             删除本地代理
 wechat
  wechat:send          给微信测试号发送消息
```
