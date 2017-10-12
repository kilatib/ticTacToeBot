#!/bin/bash

SEARCH_PATTERN=$1
echo "TRY FIND DOCKER CONTAINER like $SEARCH_PATTERN:"

set -- $(docker ps | grep $SEARCH_PATTERN)
CONTAINER_ID=$1
CONTAINER_NAME=$2
if [ ! -z $CONTAINER_ID ]; then
    echo "GET INTO $CONTAINER_NAME"
    docker exec -it $CONTAINER_ID bash
else
    echo "DOCKER CONTAINER like $SEARCH_PATTERN NOT FOUND"
fi
