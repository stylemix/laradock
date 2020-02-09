#!/bin/bash

set -xe;

if type "tee" 2>/dev/null && [ -n "${UBUNTU_SOURCE}" ]; then
    SOURCE_PATH="/etc/apt/sources.list"
    cp ${SOURCE_PATH} ${SOURCE_PATH}.bak && rm -rf ${SOURCE_PATH}
    case "${UBUNTU_SOURCE}" in
        "uz")
            tee ${SOURCE_PATH} <<-'EOF'
deb http://uz.archive.ubuntu.com/ubuntu/ xenial main restricted universe multiverse
deb http://uz.archive.ubuntu.com/ubuntu/ xenial-security main restricted universe multiverse
deb http://uz.archive.ubuntu.com/ubuntu/ xenial-updates main restricted universe multiverse
deb http://uz.archive.ubuntu.com/ubuntu/ xenial-proposed main restricted universe multiverse
deb http://uz.archive.ubuntu.com/ubuntu/ xenial-backports main restricted universe multiverse
deb-src http://uz.archive.ubuntu.com/ubuntu/ xenial main restricted universe multiverse
deb-src http://uz.archive.ubuntu.com/ubuntu/ xenial-security main restricted universe multiverse
deb-src http://uz.archive.ubuntu.com/ubuntu/ xenial-updates main restricted universe multiverse
deb-src http://uz.archive.ubuntu.com/ubuntu/ xenial-proposed main restricted universe multiverse
deb-src http://uz.archive.ubuntu.com/ubuntu/ xenial-backports main restricted universe multiverse
EOF
;;
        *)
            echo "Please check whether there is 'uz' in the parameter"
            exit 1;;
    esac
fi
