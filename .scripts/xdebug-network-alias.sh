#!/bin/bash

 if ifconfig | grep -q "10.254.254.254"; then
     echo "Network alias already exist. Skipping..."
 else
     echo "Setting network alias..."
     sudo ifconfig lo0 alias 10.254.254.254
 fi

