#!/bin/sh

IS_ERROR=0

#.py構文チェック
for FILE in `git diff-index --name-status HEAD | grep -E '*\.py$' | cut -c3-`; do
    if pyflakes $FILE; then
        :
    else
        IS_ERROR=1
    fi
done

exit $IS_ERROR
