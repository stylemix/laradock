# Using Xdebug

## Configuration

Use `etc/xdebug.ini` in your Laradock directory.
It mapped to all PHP versions CLI and FPM.

### Mac/Windows
```ini
xdebug.remote_host=host.docker.internal
```

### Ubuntu
```ini
xdebug.remote_host=172.17.0.1
```

## Starting/Stopping in PHP-FPM

Since xDebug affects PHP performance it is switched off in all fpm services by default.

Use the following commands:

- `laradock xdebug start <php-version>` - start xDebug
- `laradock xdebug stop <php-version>` - stop xDebug
- `laradock xdebug status <php-version>` - check status of xDebug (prints php -v)

where `<php-version>` is short version reference: `56`, `70`, `71`, `72`, `73`, `74`
indicated for which php-fmp version service that command is applied.
