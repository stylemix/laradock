## Prerequisites

You must install Docker and Docker compose:

- MacOS: https://docs.docker.com/docker-for-mac/install/
- Windows: https://docs.docker.com/docker-for-windows/install/
- Ubuntu: https://docs.docker.com/install/linux/docker-ce/ubuntu/

Docker compose: https://docs.docker.com/compose/install/

## Installation

Git clone to preferred directory
```bash
git clone https://github.com/stylemix/laradock.git ~/laradock && cd laradock
```

then run init command to create `config.yml` and initial config files:
```bash
# Mac / Linux...
bash init.sh

# Windows...
init.bat
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
    to: /var/laradock/project1/public
```

### Hostname Resolution

Add the "domains" for your web sites to the hosts file on your machine. 
On Mac and Linux, this file is located at `/etc/hosts`.
On Windows, it is located at `C:\Windows\System32\drivers\etc\hosts`.
The lines you add to this file will look like the following:

```
127.0.0.1 project1.loc
```
