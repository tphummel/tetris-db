# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.hostname = "app-base-lemp"

  config.vm.box = "precise64"
  config.vm.box_url = "http://dl.dropbox.com/u/1537815/precise64.box"

  config.vm.network :private_network, ip: "33.33.33.10"
  
  config.vm.synced_folder ".", "/home/vagrant/the-new-tetris"

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "512"]
  end

  config.ssh.max_tries = 40
  config.ssh.timeout   = 120
  config.ssh.forward_agent = true

  # config.berkshelf.enabled = true

  # config.vm.provision :chef_solo do |chef|
  #  chef.json = {
  #    :mysql => {
  #      :server_root_password => 'rootpass',
  #      :server_debian_password => 'debpass',
  #      :server_repl_password => 'replpass'
  #    }
  #  }
  #  chef.run_list = [
  #     "recipe[app-base-lemp::default]"
  #  ]
  # end
end
