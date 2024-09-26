<?php

namespace Tests;

use App\Core\Request;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;

class RequestTest extends TestCase
{
    protected Request $request;
    private String $uri;
    public function setUp(): void
    {
        $this->request = new Request;
    }
    public function testPathScenarios()
    {
        $paths = ['/home?id=25', '/about', '/home?name=<script>alert(1)</script>'];
        $expectedPath = ['/home', '/about', '/home'];
        foreach ($paths as $key => $value) {
            $_SERVER['REQUEST_URI'] = $value;
            $p = $this->request->path();
            $this->assertSame($expectedPath[$key], $p);
        }
        unset($_SERVER['REQUEST_URI']);
        $this->assertSame('/', $this->request->path());
    }
    public function testGetAndPostmethod()
    {
        $methods = ['get', 'post'];
        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $this->assertSame($method, $this->request->method());

            $this->assertTrue($this->request->isMethod($method), 'Failed asserting that the method is $method');
            foreach ($methods as $otherMethods) {
                if ($method !== $otherMethods) {
                    $this->assertFalse($this->request->isMethod($otherMethods));
                }
            }
        }
    }
    public function testAllInput()
    {
        $methods = ['put', 'patch'];
        $data = ['name' => 'armin', 'age' => '32', 'test' => '<script>alert(\'XSS Attack\');</script>'];
        $exportResult = ['name' => 'armin', 'age' => '32', 'test' => '&lt;script&gt;alert(&#039;XSS Attack&#039;);&lt;/script&gt;'];
        $input = http_build_query($data);

        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $this->request->setInput($input);
            $result = $this->request->body();
            $this->assertSame($exportResult, $result);
        }
    }
    public function testHeadAndOptionsReturnEmptyBody()
    {
        $methods = ['head', 'options'];
        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $result = $this->request->body();
            $this->assertSame([], $result, "Expected empty body for {$method} request.");
        }
    }

    public function testIsHeadAndIsOptions()
    {
        $methods = ['head', 'options'];
        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $this->assertTrue($this->request->{'is' . ucfirst($method)}(), "Expected true for {$method} request.");
        }
    }
}
