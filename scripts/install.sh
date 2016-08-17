#!/bin/bash
#
# Ariane Installer
#
# Version 0.1

apt-get -y install curl && cron



mkdir /etc/ariane_agent/
cd /etc/ariane_agent/
useradd ariane_agent -r -d /etc/ariane_agent -s /bin/false
wget https://yourdomain.net/agent.sh
sed -i "s/KEY='INSERT_KEY_HERE'/KEY='${1}'/g" agent.sh
chown -R ariane_agent:ariane_agent /etc/ariane_agent/
chmod -R 700 /etc/ariane_agent/
crontab -u ariane_agent -l 2>/dev/null | { cat; echo "*/1 * * * *  /etc/ariane_agent/agent.sh"; } | crontab -u ariane_agent -
cd
rm install.sh
