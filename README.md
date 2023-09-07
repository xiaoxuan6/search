# Search

[![Latest Stable Version](https://poser.pugx.org/james.xue/search/version.png)](https://packagist.org/packages/james.xue/search)
[![Total Downloads](https://poser.pugx.org/james.xue/search/d/total.png)](https://packagist.org/packages/james.xue/search)
[![GitHub license](https://img.shields.io/github/license/xiaoxuan6/search)](https://github.com/xiaoxuan6/search)

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

## Init configuration

```bash
search init
```

## Env system variable

Copy the `config.json` file in the root directory to the current directory and modify the configuration file, And
execute the following command：

```bash
search env -f ./config.json
```

Can also a value can be set individually

```shell
search config set --key=xxx --value=xxx
```

## More

<details>
<summary><b>ocr</b></summary>

```bash
ocr ./16a7067.jpg
```

</details>

<details>
<summary><b>qrcode</b></summary>

```bash
qrcode ./16a7067.jpg
```

</details>

# Api

```bash
$app = new Vinhson\Search\Api\Application();
```

<details>
<summary><b>OCR</b></summary>

```bash
$app->ocr->handle("./a.png");
```

</details>

<details>
<summary><b>Qrcode</b></summary>

```bash
// 生成二维码
$app->qrcode->generate("https://github.com/xiaoxuan6/search");
// 生成带背景的二维码
$app->qrcode->generate("https://github.com/xiaoxuan6/search", "https://background.com/a.png");
// 解析二维码
$app->qrcode->decode("./qrcode.png");
```

</details>

<details>
<summary><b>image</b></summary>

```bash
$app->image
```

</details>
