<?php

namespace Tests;

use App\Controllers\TestController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
class RouterTest extends TestCase

{
    protected Request $request;
    protected Response $response;
    public Router $router;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(Request::class)->onlyMethods(['path', 'httpMethod'])->getMock();
        $this->response = $this->getMockBuilder(Response::class)->getMock();

        $this->router = new Router($this->request, $this->response);
    }

    public function testAllMethodRegisterFunctinally()
    {
        $methods = ['get', 'post', 'patch', 'put', 'delete'];
        foreach ($methods as $method) {
            $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/');
            $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn($method);

            $this->router->{$method}('/', function () {
                return 'home';
            });
            $response = $this->router->resolve();
            $this->assertEquals('home', $response);
        }
    }
    public function testDynamicMethodRegistration()
    {
        $methods = ['get', 'post', 'patch', 'put', 'delete'];
        foreach ($methods as $method) {
            $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/home');
            $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn($method);

            $this->router->{$method}('/home', [TestController::class, 'index']);
            $response = $this->router->resolve();
            $this->assertEquals('home', $response);
        }
    }

    public function testDynamicMethodRegistrationWithParams()
    {

        $methods = ['get', 'post', 'patch', 'put', 'delete'];
        foreach ($methods as $method) {
            $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/test/12');
            $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn($method);

            $this->router->{$method}('/test/{id}', [TestController::class, 'test']);
            $response = $this->router->resolve();
            $this->assertEquals('12', $response);
        }
    }
    public function testAllInvalidRouteMethods(){
        
        $methods = ['get', 'post', 'patch', 'put', 'delete'];
        foreach ($methods as $method) {
            $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/invalid');
            $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn($method);

            $this->router->{$method}('/invalid', [TestController::class, 'index']);
            $response = $this->router->resolve();
            $this->assertNotEquals('Not Found Url', $response);
        }
    }
}
