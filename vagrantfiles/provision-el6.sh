#! /bin/sh

# Install EPEL as it has XDebug packaged
yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm

# Required minimum packages to use the software
yum -y install httpd mod_php php-xml php-intl php-mbstring php-pecl-xdebug git

# Virtual host configuration for Symfony use
cp /var/www/html/vagrantfiles/virtualhost.conf /etc/httpd/conf.d

# Make sure PHP has a timezone set.
cp /var/www/html/vagrantfiles/php-timezone.ini /etc/php.d

# Adjust user for httpd to allow file writes
sed -ibak -r -e 's/(User|Group) apache/\1 vagrant/g' /etc/httpd/conf/httpd.conf

# Enable and run Apache HTTPd
chkconfig httpd on
service httpd start

# Disable and make sure firewall is stopped. We don't need it for a local vagrant.
chkconfig iptables off
chkconfig ip6tables off
service iptables stop
service ip6tables stop

# Install Composer to make it easier to set up the code inside the VM
curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Download PHPUnit
curl -L -sS -o /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-4.8.phar
chmod +x /usr/local/bin/phpunit
