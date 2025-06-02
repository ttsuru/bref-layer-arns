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
vendor/bin/bref-layer-arns php-84-fpm,gd-php-84 ap-northeast-1
```

You can omit the region if you set the `AWS_REGION` environment variable:

```
export AWS_REGION=ap-northeast-1
vendor/bin/bref-layer-arns php-84-fpm,gd-php-84
```

The output will be something like:

```
arn:aws:lambda:ap-northeast-1:534081306603:layer:php-84-fpm:24,arn:aws:lambda:ap-northeast-1:403367587399:layer:gd-php-84:2
```

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

You can use the resolved ARNs in your `template.yaml` with `Parameters` and `Globals` like this:

```
Globals:
  Function:
    Layers:
      - !Ref BrefLayerArns

Parameters:
  BrefLayerArns:
    Type: String
    Default: arn:aws:lambda:ap-northeast-1:534081306603:layer:php-84-fpm:24
```

And pass in the latest ARNs dynamically from GitHub Actions:

```
sam deploy --parameter-overrides BrefLayerArns=$(vendor/bin/bref-layer-arns php-84-fpm)
```

## License

MIT License – see [LICENSE](LICENSE) for full text.
