#!/bin/bash
echo "START DOCKER SERVER:"

docker-compose -f docker-compose.yml up -d --no-deps
