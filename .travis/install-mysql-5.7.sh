#!/usr/bin/env bash

set -ex

echo "Installing MySQL 5.7..."

sudo docker run \
    -d \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    -p 33306:3306 \
    --name mysql57 \
    mysql:5.7
