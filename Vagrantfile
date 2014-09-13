# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"
  config.vm.box = "trusty64"

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "512"]
  end

  config.ssh.forward_agent = true

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "ansible/servers.yml"
    ansible.inventory_path = "ansible/local-inventory"
    ansible.verbose = "vvvv"
  end

  config.vm.define "tetris-db", autostart: false do |node|
    node.vm.network :private_network, ip: "44.33.33.11"

    node.vm.synced_folder ".", "/home/vagrant/the-new-tetris", type: "nfs"
    node.vm.hostname = "tnt.dev"
  end

end
