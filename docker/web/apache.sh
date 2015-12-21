#!/bin/bash
source /etc/apache2/envvars && /usr/sbin/apache2 -DFOREGROUND >> /var/log/apache2/apache.log 2>&1
