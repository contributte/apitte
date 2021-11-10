# Console

Console commands for Apitte.

## Content

- [Setup](#setup)
- [Commands](#commands)

## Setup

Install and register console plugin.

```neon
api:
    plugins:
        Apitte\Console\DI\ConsolePlugin:
```

You also need setup an integration of [symfony/console](https://symfony.com/doc/current/components/console.html), try [contributte/console](https://github.com/contributte/console/)

## Commands

### Route dump

List all endpoints and their details

```bash
php bin/console apitte:route:dump
```
