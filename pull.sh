#!/usr/bin/zsh

PULL=$(git pull);
RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    pa send:error "Failed git pull: $PULL";
fi

echo $PULL;

source ./post-pull.sh