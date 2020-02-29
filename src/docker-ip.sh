#!/usr/bin/env bash
ping -q -c1 host.docker.internal > /dev/null 2>&1
if [[ $? -eq 0 ]]; then
  HOST_IP=$(dig +short host.docker.internal)
else
  HOST_IP=$(ip route | awk 'NR==1 {print $3}')
fi
echo $HOST_IP
