# Node.js, nvm, npm, yarn

## Versions

To install specific Node.js version pass a list of versions in `config.yml`.
In workspace [Node Version Manager](https://github.com/nvm-sh/nvm) (nvm) will be installed to provide this feature.

```yaml
nodejs:
  versions:
    # Any version notation supported by nvm
    - "node"
    - "10"
    - "6.14.4"
```

The first version will be set as default via `nvm alias default <version>`.

## Yarn

Latest Yarn version is installed along with Node.js. To install specific version:

```yaml
nodejs:
  yarn: "1.2.3"
```
