#!/usr/bin/env bash

set -ex

sudo docker version

echo "Installing Postgres 11"
sudo service postgresql stop || true

sudo docker pull postgres:11
sudo docker run -d --name postgres11 -e POSTGRES_HOST_AUTH_METHOD=trust -p 5432:5432 postgres:11
sudo docker exec -i postgres11 bash <<< 'until pg_isready -U postgres > /dev/null 2>&1 ; do sleep 1; done'
