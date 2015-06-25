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

There are multiple machines defined in the Vagrantfile for this project. They
are intended to be used for development purposes and for testing that the API
application will work on common configurations as found on most server providers.
As of the last update of this documentation, the two machines are CentOS 6 (el6)
and CentOS 7 (el7). The el7 machine is currently the "default".

To start up a VM, open up a terminal window or command prompt and navigate to
the project directory. Once there, run "vagrant up _machinename_". If you have
installed the necessary tools correctly, you'll soon see the Vagrant tool begin
to download the reference VM and then configure it with the tools needed to set
up the API. Once the VM is up, you can run "vagrant ssh _machinename_" to connect
to it and follow the instructions in the README to get the API running.

Note that if you omit the machine name for the "up" command, Vagrant will start
all configured boxes. You will usually want to only start one VM unless you're
testing for functionality across both reference environments.

For both VMs, the API files will be located at /var/www/html on the VM. When the
VM is running, it will forward ports 80 (http) and 22 (ssh) to your local system.
The SSH port will be picked by Vagrant based on what is usable, while the http
forward will depend on the VM you are running. For the el6 image, it will be
forwarded on port 8086. For the el7 image, it will be on port 8080. You can then
access the VM webserver by going to a browser on the local machine and using the
URL http://localhost:8080/ or http://localhost:8086/.

When you're done with the VM, run "vagrant destroy _machinename_" in the same
directory to completely remove the configured VM or "vagrant halt _machinename_"
to suspend it without deleting it. If you do not specify the machine name,
vagrant will attempt to destroy or halt both VMs. This should normally work
without issue.

Resources
---------
* [Vagrant Documentation on Multi-Machine Environments](http://docs.vagrantup.com/v2/multi-machine/index.html)
