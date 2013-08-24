set :application, "tetris-db"

set :scm, :git
set :scm_verbose, true

set :repository, "git@github.com:tphummel/#{application}.git"
set :branch, "master"

set :user, "tom"                               # user to ssh in as
set :use_sudo, false
set :ssh_options, { :forward_agent => true }

set :deploy_to, "/home/#{user}/#{application}" 
set :deploy_via, :remote_cache
set :keep_releases, 5

set :sub_domain, 'tnt'

default_run_options[:pty] = true

task :giambi do
  set :branch, "deploy"
  set :top_level_task, "giambi"
  set :app_host, "tphum.us"
  role :app, "192.241.192.107"
end

namespace :deploy do

  task :create_deploy_to, :roles => :app do
    run "mkdir -p #{deploy_to}"
  end

  desc "writes the nginx config for app"
  task :write_nginx_config, :roles => :app do
    nginx_config = <<-NGINX
server {
        listen   80;

        root /home/#{user}/#{application}/current;
        index index.php index.html index.htm;

        server_name #{sub_domain}.#{app_host};

        location / {
                try_files $uri $uri/ /index.html /index.php;
        }

        error_page 404 /404.html;

        error_page 500 502 503 504 /50x.html;
        location = /50x.html {
              root /home/#{user}/#{application}/current;
        }

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        location ~ \\.php$ {
                #fastcgi_pass 127.0.0.1:9000;
                # With php5-fpm:
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;

        }

}
NGINX
    put nginx_config, "/tmp/#{application}_nginx_config"
    run "sudo mv /tmp/#{application}_nginx_config /etc/nginx/sites-enabled/#{application}"
  end

  desc "create deployment directory"
  task :create_deploy_to, :roles => :app do
    run "mkdir -p #{deploy_to}"
  end

end

namespace :config do
  desc "create shared/config directory"
  task :create_dir, :roles => :app do
    run "mkdir -p #{shared_path}/config"
  end

  desc "touch shared/config db.php file"
  task :touch_db_conf, :roles => :app do
    run "touch #{shared_path}/config/db.php"
  end

  desc "make symlink for config/creds.coffee"
  task :create_symlink, :roles => :app do
    run "ln -nfs #{shared_path}/config/db.php #{release_path}/config/db.php"
  end
end

namespace :git do

  desc "Delete remote cache"
  task :delete_remote_cache do
    run "rm -rf #{shared_path}/cached-copy"
  end

end

before 'deploy:setup', 'deploy:create_deploy_to'
after 'deploy:setup', 'config:create_dir', 'config:touch_db_conf', 'deploy:write_nginx_config'

after "deploy:finalize_update", "config:create_symlink"