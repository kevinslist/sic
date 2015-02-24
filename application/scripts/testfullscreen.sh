#!/bin/bash
WINDOW=$(echo $(xwininfo -id $(xdotool getactivewindow) -stats | \
                egrep '(Width|Height):' | \
                awk '{print $NF}') | \
         sed -e 's/ /x/')
SCREEN=$(xdpyinfo | grep -m1 dimensions | awk '{print $2}')
if [ "$WINDOW" != "$SCREEN" ]; then
    echo '0'
else
    echo $SCREEN
fi