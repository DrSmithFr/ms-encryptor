# Installation

  - Install [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html)
  - Intall PHP and needed library `make install`
  - Enable hooks using `git_hooks`

# Generate your JWT keys

Encryptor need public/private key to generate JWT tokens for 
API for authentication mechanisms.

Execute the following commands:

    mkdir -p config/jwt
    
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
    
    mv config/jwt/private2.pem config/jwt/private.pem
    chmod 700 config/jwt/*
    
# Create nginx vHost

## fix permission issues

 - Edit `/etc/php/7.4/fpm/pool.d/www.conf` and replace :
 
       user = ww-data
       group = ww-data
 
 - with
 
       user = {YOUR_LINUX_USERNAME}
       group = {YOUR_LINUX_USERNAME}

## Create self signed certificate

 - Generate cert using `sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt`
 - Copy configuration using `sudo cp config/nginx/snippets /etc/nginx/snippets`

## Create vHost

- Create the file `/etc/nginx/sites-available/ms-encryptor` according to the template `config/vhost.conf`
- Enable the vHost using `ls -s /etc/nginx/sites-available/ms-encryptor /etc/nginx/sites-enabled/ms-encryptor`
- Reload nginx to apply configuration : `sudo service nginx reload`
