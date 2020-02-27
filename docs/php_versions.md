# Multiple PHP Versions

## Sites
Laradock supports for multiple versions of PHP.
You may specify which version of PHP to use for a given site within your `config.yml` file.
The available PHP versions are: "5.6", "7.0", "7.1", "7.2", "7.3", and "7.4" (the default):

```yaml
sites:
  - map: project1.loc
    to: /home/laradock/project1/public
    php: "7.1"
```

## Cli

By default there are PHP "7.4", "7.3", "7.2" versions installed in workspace. 
In addition, you may install any of the supported PHP versions,
by uncommenting desired PHP version in your `config.yml`:

```yaml
php:
  versions:
#    - "5.6"
#    - "7.0"
    - "7.1"
```

and run the following command to reload containers with provisioning:

```bash
laradock rebuild && laradock up
```

### Use specific PHP version

```bash
php5.6 artisan list
php7.0 artisan list
php7.1 artisan list
php7.2 artisan list
php7.3 artisan list
php7.4 artisan list
```

### Default PHP version

You may also update the default CLI version by issuing the following commands from within your Laradock:

```bash
php56
php70
php71
php72
php73
php74
```

?> Default PHP version will be reset after recreating workspace container
and you should rerun one of commands above. Recreating could happen when changes:
mounted directories, port mappings, installing new software, rebuilding, upgrading.
