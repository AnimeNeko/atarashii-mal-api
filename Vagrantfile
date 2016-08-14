# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.define "el7", primary: true do |el7|
    el7.vm.box = "bento/centos-7.2"
    el7.vm.provision "shell", path: "vagrantfiles/provision-el7.sh"
    el7.vm.network "forwarded_port", guest: 80, host: 8080
  end

  config.vm.synced_folder ".", "/var/www/html"

end
