#!/bin/bash

# turn on bash's job control
set -m

# Start the primary process and put it in the background
/sbin/my_init &

# Start the supervisor process
/usr/bin/supervisord -n -c /etc/supervisord.conf

# now we bring the primary process back into the foreground
# and leave it there
fg %1
