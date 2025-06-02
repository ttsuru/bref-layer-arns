# Bref Layer ARNs Resolver

This library provides a CLI tool and PHP API to resolve the latest Lambda Layer ARNs published by [Bref](https://bref.sh), including layers from `bref/bref` and optionally from `bref/extra-php-extensions`.

## Installation

Install via Composer:

```
composer require ttsuru/bref-layer-arns
```

If you also use layers from `bref/extra-php-extensions`, you should require it as well:

```
composer require bref/extra-php-extensions
```

## Usage

### CLI

Use the CLI to output a comma-separated list of ARNs for the specified layers:

```
vendor/bin/bref-layer-arns php-84-fpm gd-php-84 insights --region=ap-northeast-1
```

You can omit the region if you set the `AWS_REGION` environment variable:

```
export AWS_REGION=ap-northeast-1
vendor/bin/bref-layer-arns php-84-fpm gd-php-84 insights
```

The output will be something like:

```
arn:aws:lambda:ap-northeast-1:534081306603:layer:php-84-fpm:24,arn:aws:lambda:ap-northeast-1:403367587399:layer:gd-php-84:2,arn:aws:lambda:ap-northeast-1:580247275435:layer:LambdaInsightsExtension:14
```


To use ARM64-specific layers, pass `arm-insights` instead of `insights`:

```
vendor/bin/bref-layer-arns arm-php-84-fpm arm-insights --region=ap-northeast-1
```

> [!WARNING]
> `bref/extra-php-extensions` does not provide ARM64 layers. Only x86_64 is supported.

### PHP API

You can also use it programmatically:

```
use Bref\LayerArns\Resolver;

$arns = Resolver::resolve(['php-84-fpm', 'gd-php-84'], 'ap-northeast-1');
```

## Features

- Resolves layers from `bref/bref`
- Supports optional `bref/extra-php-extensions`
- Automatically determines correct AWS account ID
- CLI output ready for GitHub Actions or SAM parameter overrides

## Testing

Install dev dependencies and run PHPUnit:

```
composer install
vendor/bin/phpunit
```

## Usage with AWS SAM


You can use the resolved ARNs in your `template.yaml` with `Parameters` and `Globals` like this (supporting multiple layers):

```
Globals:
  Function:
    Layers: !Ref BrefLayerArns

Parameters:
  BrefLayerArns:
    Type: CommaDelimitedList
    Default: >-
      arn:aws:lambda:ap-northeast-1:534081306603:layer:php-84-fpm:24,
      arn:aws:lambda:ap-northeast-1:403367587399:layer:gd-php-84:2
```

And pass in the latest ARNs dynamically from GitHub Actions:

```
sam deploy --parameter-overrides BrefLayerArns=$(vendor/bin/bref-layer-arns php-84-fpm --region=$AWS_REGION)
```

## Usage with GitHub Actions

You can dynamically resolve and inject the latest Bref Layer ARNs in your GitHub Actions workflow using this tool.

Example:

```yaml
env:
  AWS_REGION: ap-northeast-1
  BREF_LAYERS: php-84-fpm gd-php-84 insights

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'

      - name: Install dependencies
        run: composer install --no-dev

      - name: Resolve Bref Layer ARNs
        run: |
          BREF_LAYER_ARNS=$(vendor/bin/bref-layer-arns "$BREF_LAYERS")
          if [ -z "$BREF_LAYER_ARNS" ]; then
            echo "Failed to resolve Bref layers." >&2
            exit 1
          fi
          echo "BREF_LAYER_ARNS=$BREF_LAYER_ARNS" >> $GITHUB_ENV

      - name: Deploy with SAM
        run: |
          sam deploy \
            --parameter-overrides BrefLayerArns=$BREF_LAYER_ARNS
```

## Changelog

### v1.1.0

- Added support for `insights` and `arm-insights` layers via new `layers-insights.json`
- Automatically maps these to appropriate `LambdaInsightsExtension` ARNs per region
- CLI and API updated to support these identifiers transparently

## License

MIT License – see [LICENSE](LICENSE) for full text.
