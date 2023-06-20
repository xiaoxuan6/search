#!/usr/bin/env sh

check_php() {
    if [ ! "$(command -v php)" ]; then
        echo
        printf "\033[0;31m未安装 php 环境，请安装完重新执行改脚本"
        echo
        printf "\033[0;31m是否继续安装 php 环境（yes or no）？"
        read disable

        if [ "$disable" = "yes" ]; then
            apt-get install php
            printf "\033[0;32m php 安装成功, 继续安装 Composer"
            echo
        else
            exit 1;
        fi
    fi
}

install_composer() {
    if uname | grep -q "MINGW"; then
        printf "\033[0;31m请手动下载 Composer 安装包并运行安装，安装成功之后重新执行该脚本，下载地址：https://getcomposer.org/Composer-Setup.exe"
        exit 1;
    fi

    check_php
    check_php
    curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
    mv composer.phar /usr/local/bin/composer
    echo
    printf "\033[0;32m安装成功，并更改 Composer 镜像源为阿里云"
    echo
    export COMPOSER_ALLOW_SUPERUSER=1;
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
    composer config -g --list | grep "repositories.packagist.org.url"
    echo
}

if [ ! "$(command -v composer)" ]; then
    printf "\033[0;31mComposer 未安装，正在安装中……"
    install_composer
    echo
fi

printf "\033[0;32m开始安装 james.xue/search"
echo
composer global require james.xue/search
