#!/bin/bash
export DISPLAY=:0.0
ps aux | grep firefox-trunk | awk '{print $2}' | xargs kill -9;
firefox-trunk -url "https://k/help" > /dev/null 2>&1 &
sleep 1 

WINDOW=$(echo $(xwininfo -id $(xdotool getactivewindow) -stats | \
                egrep '(Width|Height):' | \
                awk '{print $NF}') | \
         sed -e 's/ /x/')
SCREEN=$(xdpyinfo | grep -m1 dimensions | awk '{print $2}')
if [ "$WINDOW" != "$SCREEN" ]; then
    xdotool key F11 # hit full screen
    echo 'do make fullscreen'
fi








#xdotool key F11 # hit full screen


#Tab
#Shift_L
#Down
#Right
#Left
#Up
#Return