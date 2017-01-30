#!/bin/sh

branch="$(git symbolic-ref HEAD 2>/dev/null)" || \
       "$(git describe --contains --all HEAD)"

if [ "${branch##refs/heads/}" = "master" ]; then
    echo "Do not commit to master branch!!!"
    exit 1
fi
if [ "${branch##refs/heads/}" = "staging" ]; then
    echo "Do not commit to staging branch!!!"
    exit 1
fi
if [ "${branch##refs/heads/}" = "develop" ]; then
    echo "Do not commit to develop branch!!!"
    exit 1
fi
