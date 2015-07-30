#! /bin/sh

# Required minimum packages to use the software
yum -y install httpd mod_php php-xml php-intl php-mbstring git

# Virtual host configuration for Symfony use
cp /var/www/html/vagrantfiles/virtualhost.conf /etc/httpd/conf.d

# Adjust user for httpd to allow file writes
sed -ibak -r -e 's/(User|Group) apache/\1 vagrant/g' /etc/httpd/conf/httpd.conf

# Enable and run Apache HTTPd
systemctl enable httpd
systemctl start httpd

# Disable and make sure firewalld is stopped. We don't need it for a local vagrant.
systemctl disable firewalld
systemctl stop firewalld

# Install Composer to make it easier to set up the code inside the VM
curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin
