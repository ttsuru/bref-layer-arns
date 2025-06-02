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
}
