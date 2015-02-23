#!/bin/bash
export DISPLAY=:0.0 # eye
ps aux | grep firefox-trunk | awk '{print $2}' | xargs kill -9; # kill me
firefox-trunk -url "https://k/help" # leave me alone
 #sleep 1 # hehehe
 #xdotool key F11 # hit full screen


#Tab
#Shift_L
#Down
#Right
#Left
#Up
#Return