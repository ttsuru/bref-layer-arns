<?php

namespace Bref\LayerArns\Tests;

use Bref\LayerArns\Resolver;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public function test_resolves_real_bref_layer()
    {
        $layers = ['php-84-fpm'];
        $arns = Resolver::resolve($layers, 'ap-northeast-1');

        $this->assertCount(1, $arns);
        $this->assertMatchesRegularExpression(
            '/^arn:aws:lambda:ap-northeast-1:534081306603:layer:php-84-fpm:\d+$/',
            $arns[0]
        );
    }

    public function test_resolves_real_extra_php_extension_layer()
    {
        $layers = ['gd-php-84'];
        $arns = Resolver::resolve($layers, 'ap-northeast-1');

        $this->assertCount(1, $arns);
        $this->assertMatchesRegularExpression(
            '/^arn:aws:lambda:ap-northeast-1:403367587399:layer:gd-php-84:\d+$/',
            $arns[0]
        );
    }

    public function test_throws_when_layer_does_not_exist()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Layer 'no-such-layer' not found");

        Resolver::resolve(['no-such-layer'], 'ap-northeast-1');
    }

    public function test_mixed_layers_throws_on_missing()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Layer 'no-such-layer' not found");

        // 'php-84-fpm' exists, 'no-such-layer' does not
        Resolver::resolve(['php-84-fpm', 'no-such-layer'], 'ap-northeast-1');
    }
}
