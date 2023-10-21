#!/bin/sh
set -e

echo "Running docker-entrypoint.sh"
echo "ENV: $ENV"
echo "SYMFONY_ENV: $SYMFONY_ENV"
echo "DEBUGMODE: $DEBUGMODE"

exec $@