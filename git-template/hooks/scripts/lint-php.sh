#!/bin/sh

IS_ERROR=0

# .php構文チェック
for FILE in `git diff-index --name-status HEAD | grep -E '*\.php$' | cut -c3-`; do
    if php -l $FILE; then
        :
    else
        IS_ERROR=1
    fi
done

exit $IS_ERROR
