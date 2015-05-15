Using Vagrant
=============

To make things easier for developers and to provide a reference test
environment, we have provided a configuration for the Vagrant tool. This
configuration will allow on-demand creation and configuration of a virtual
machine that matches our reference deployment configuration.

Using Vagrant for the API
-------------------------

This document is not intended to cover installing the Vagrant tool or the
VirtualBox virtualization product. For instructions on that, see
[Vagrant Getting Started](http://docs.vagrantup.com/v2/getting-started/index.html)
and [VirtualBox User Manual](https://www.virtualbox.org/manual/UserManual.html).
Once you have those tools installed, you can proceed.

To start up the VM, open up a terminal window or command prompt and navigate to
the project directory. Once there, just run "vagrant up". If you've installed
the necessary tools correctly, you'll soon see the Vagrant tool begin to
download the reference VM and then configure it with the tools needed to set up
the API. Once the VM is up, you can run "vagrant ssh" to connect to it and
follow the instructions in the README to get the API running.

The API files will be located at /var/www/html on the VM. When the VM is
running, it will forward ports 80 (http) and 22 (ssh) to your local system at
8080 and 2222. If those ports are in use, you may need to edit the Vagrantfile
to choose an unused port on your system. Additionally, the project files will
be located 

When you're done with the VM, run "vagrant destroy" in the same directory to
completely remove the configured VM or "vagrant halt" to suspend it without
deleting it.
