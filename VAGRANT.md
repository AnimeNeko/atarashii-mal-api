Using Vagrant
=============

To make things easier for developers and to provide a reference test
environment, we have provided a configuration for the Vagrant tool. This
configuration will allow on-demand creation and configuration of a virtual
machine that matches our reference deployment configuration.

**Note:** Due to a bug in Vagrant 1.8.5, it will not properly deploy the testing
image, please use 1.8.4 until the 1.8.6 release of Vagrant, or make the manual
patch detailed in ([Vagrant bug #7610](https://github.com/mitchellh/vagrant/issues/7610)).

Using Vagrant for the API
-------------------------

This document is not intended to cover installing the Vagrant tool or the
VirtualBox virtualization product. For instructions on that, see
[Vagrant Getting Started](https://www.vagrantup.com/docs/getting-started/index.html)
and [VirtualBox User Manual](https://www.virtualbox.org/manual/UserManual.html).
Once you have those tools installed, you can proceed.

The current Vagrant reference machine is CentOS 7 (el7) running PHP 5.5. It is
intended to be a somewhat representative setup as could be found on most hosting
providers.

To start up a VM, open up a terminal window or command prompt and navigate to
the project directory. Once there, run "vagrant up". If you have installed the
necessary tools correctly, you'll soon see the Vagrant tool begin to download
the reference VM and then configure it with the tools needed to set up the API.
Once the VM is up, you can run "vagrant ssh" to connect to it and follow the
instructions in the README to get the API running.

In the VM, the API files will be located at `/var/www/html`. When the VM is
running, it will forward ports 80 (http) and 22 (ssh) to your local system.
The SSH port will be picked by Vagrant based on what is usable, and the HTTP
port will default to port 8080. You can then access the VM webserver by going
to a browser on the local machine and using the URL http://localhost:8080/.

When you're done with the VM, run "vagrant destroy" in the same directory to
completely remove the configured VM or "vagrant halt" to suspend it without
deleting it. If you are planning to make use of the VM in the near future,
halting is the recommended action.
