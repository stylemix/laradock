## Prerequisites

You must install Docker and Docker compose:

- MacOS: https://docs.docker.com/docker-for-mac/install/
- Windows: https://docs.docker.com/docker-for-windows/install/
- Ubuntu: https://docs.docker.com/install/linux/docker-ce/ubuntu/

Docker compose: https://docs.docker.com/compose/install/

## Installation

Git clone to preferred directory

#### Mac / Linux

```bash
git clone https://github.com/stylemix/laradock.git ~/laradock && cd ~/laradock
```

#### Windows

```bash
git clone https://github.com/stylemix/laradock.git C:\laradock && cd C:\laradock
```

then run init command to create `config.yml` and initial config files:
```bash
# Mac / Linux...
./laradock init

# Windows...
laradock.bat init
```

## Configuring

### Configuring Shared Folders

You should always map individual projects to their own folder mapping instead of mapping your entire `~/code` folder.
When you map a folder the virtual machine must keep track of all disk IO for every file in the folder. This leads to performance issues if you have a large number of files in a folder.

```yaml
folders:
  - map: ~/code/project1
    to: /home/laradock/project1
  - map: ~/code/project2
    to: /home/laradock/project2
```

### Configuring Nginx Sites

```yaml
sites:
  - map: project1.loc
    to: /home/laradock/project1/public
```

### Database Connection

#### Example for Laravel:
```dotenv
# MySQL
DB_HOST=mariadb
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Example for WordPress:
```php
<?php
define( 'DB_HOST', 'mariadb' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'secret' );
```

### Hostname Resolution

Add the "domains" for your web sites to the hosts file on your machine. 
On Mac and Linux, this file is located at `/etc/hosts`.
On Windows, it is located at `C:\Windows\System32\drivers\etc\hosts`.
The lines you add to this file will look like the following:

```
127.0.0.1 project1.loc
```

### Accessing Laradock Globally

You may want run commands on your Laradock from anywhere on your filesystem.
You can do this on Mac / Linux systems by adding a Bash function to your Bash profile.
On Windows, you may accomplish this by adding a "batch" file to your PATH.
These scripts will allow you to run any Laradock command from anywhere on your system
and will automatically point that command to your Laradock installation:

#### Mac / Linux

```bash
function laradock() {
    (cd ~/laradock && ./laradock $*)
}
```

Make sure to tweak the ~/laradock path in the function to the location of your actual Laradock installation.
Once the function is installed, you may run commands like `laradock up` or `laradock ssh` from anywhere on your system.

#### Windows

Create a `laradock.bat` batch file anywhere on your machine with the following contents:

```
@echo off

set cwd=%cd%
set laradockDir=C:\laradock

cd /d %laradockDir% && laradock.bat %*
cd /d %cwd%

set cwd=
set laradockDir=
```

## Start containers

```bash
laradock up
```

It triggers regenerating `docker-compose.yml` and starting services.

## SSH into workspace

Login as `laradock` user:
```bash
laradock ssh
```

## SUDO

You can execute `sudo` command being logged in as `laradock` user. 
Password is **`secret`**.

## Persistence

Data generated in Docker containers are not persistent except those 
that are mapped to your host filesystem.
Read more about it here https://docs.docker.com/storage/.

Home directory `/home/laradock` in workspace container is mapped as local volume (https://docs.docker.com/storage/volumes/), 
so all data in that directory are persistent.

## More

- [PHP versions](docs/php_versions.md)
- [PHP Xdebug](docs/xdebug.md)
- [Node.js, Nvm, Npm, Yarn](docs/nodejs.md)
