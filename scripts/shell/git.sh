#!/usr/bin/env bash

if tty -s; then echo interactive; fi

if which php > /dev/null; then
    php "$@"
else
    fin code-sniff "$@"
fi
