<?php

namespace Tests;

use App\Controllers\TestController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase

{
    protected Request $request;
    protected Response $response;
    public Router $router;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(Request::class)->onlyMethods(['path', 'httpMethod'])->disableOriginalConstructor()->getMock();
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

    public function testGetRouterView()
    {
        $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/home');
        $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn('get');
        $this->router->get('/home', function () {
            $view = new View;
            return $view->renderViews('home');
        });
        $response = $this->router->resolve();
        $this->assertStringContainsString('<h1>Hello, world!</h1>', $response);
    }
    public function testPostRequestWithData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = "/submit";
        $_POST['username'] = 'Armin';
        $_POST['email'] = 'armin@example.com';

        $this->request->expects($this->atLeastOnce())->method('path')->willReturn('/submit');
        $this->request->expects($this->atLeastOnce())->method('httpMethod')->willReturn('post');
        $this->router->post('/submit', function () {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            return "Username: $username, Email: $email";
        });
        $response = $this->router->resolve();
        $this->assertEquals('Username: Armin, Email: armin@example.com', $response);
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
    public function testAllInvalidRouteMethods()
    {

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
