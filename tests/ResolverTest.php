<?php

namespace Bref\LayerArns\Tests;

use Bref\LayerArns\Resolver;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public function test_it_throws_if_layer_missing()
    {
        $this->expectException(\RuntimeException::class);
        Resolver::resolve(['non-existent-layer'], 'ap-northeast-1');
    }

    public function test_it_returns_empty_when_no_layers()
    {
        $this->assertSame([], Resolver::resolve([], 'ap-northeast-1'));
    }

    public function test_it_resolves_insights_layer()
    {
        $arns = Resolver::resolve(['insights'], 'ap-northeast-1');
        $this->assertCount(1, $arns);
        $this->assertMatchesRegularExpression(
            '/^arn:aws:lambda:ap-northeast-1:580247275435:layer:LambdaInsightsExtension:\d+$/',
            $arns[0]
        );
    }

    public function test_it_resolves_arm_insights_layer()
    {
        $arns = Resolver::resolve(['arm-insights'], 'ap-northeast-1');
        $this->assertCount(1, $arns);
        $this->assertMatchesRegularExpression(
            '/^arn:aws:lambda:ap-northeast-1:580247275435:layer:LambdaInsightsExtension-Arm64:\d+$/',
            $arns[0]
        );
    }

    public function test_it_resolves_multiple_layers()
    {
        $arns = Resolver::resolve(['php-84-fpm', 'gd-php-84'], 'ap-northeast-1');
        $this->assertCount(2, $arns);
        foreach ($arns as $arn) {
            $this->assertMatchesRegularExpression(
                '/^arn:aws:lambda:ap-northeast-1:\d{12}:layer:[a-zA-Z0-9-_]+:\d+$/',
                $arn
            );
        }
    }

    public function test_it_resolves_mixed_layers()
    {
        $arns = Resolver::resolve(['php-84-fpm', 'insights'], 'ap-northeast-1');
        $this->assertCount(2, $arns);
        $this->assertMatchesRegularExpression(
            '/^arn:aws:lambda:ap-northeast-1:580247275435:layer:LambdaInsightsExtension:\d+$/',
            $arns[1]
        );
    }

    public function test_it_throws_if_region_not_supported()
    {
        $this->expectException(\RuntimeException::class);
        Resolver::resolve(['php-84-fpm'], 'unknown-region-1');
    }
}
