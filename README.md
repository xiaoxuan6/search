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