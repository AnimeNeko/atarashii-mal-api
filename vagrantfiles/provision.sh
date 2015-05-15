#! /bin/sh

# Required minimum packages to use the software
/usr/bin/sudo /usr/bin/yum -y install httpd mod_php php-xml php-intl php-mbstring git

# Virtual host configuration for Symfony use
/usr/bin/cp /var/www/html/vagrantfiles/virtualhost.conf /etc/httpd/conf.d

# Enable and run Apache HTTPd
/usr/bin/sudo /usr/bin/systemctl enable httpd
/usr/bin/sudo /usr/bin/systemctl start httpd

# Disable and make sure firewalld is stopped. We don't need it for a local vagrant.
/usr/bin/sudo /usr/bin/systemctl disable firewalld
/usr/bin/sudo /usr/bin/systemctl stop firewalld

# Install Composer to make it easier to set up the code inside the VM
/bin/curl -sS https://getcomposer.org/installer | /bin/php -- --filename=composer --install-dir=/usr/local/bin
