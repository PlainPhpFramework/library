<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use pp\AppRouter;
use pp\Router;

final class RouterTest extends TestCase
{

    static AppRouter $appRouter;
    static Router $router;

    public function setUp(): void
    {

        $appRouter = new AppRouter;
        $router = new Router;

        self::$appRouter = $appRouter;
        self::$router = $router;

    }

    public function testDefaultDispatch(): void
    {

        $this->assertEquals(
            ['default', 'hello'],
            self::$router->dispatch('hello')
        );
        $this->assertEquals(
            ['default', 'hello'],
            self::$router->dispatch('/hello')
        );
        $this->assertEquals(
            ['default', 'index'],
            self::$router->dispatch('/')
        );
        $this->assertEquals(
            ['default', 'index'],
            self::$router->dispatch('')
        );
        $this->assertEquals(
            ['default', 'index'],
            self::$router->dispatch('/index')
        );
        $this->assertEquals(
            ['controller', 'index'],
            self::$router->dispatch('/controller/')
        );
        $this->assertEquals(
            ['controller', 'index'],
            self::$router->dispatch('controller/')
        );
        $this->assertEquals(
            ['controller', 'index'],
            self::$router->dispatch('controller/index')
        );

    }

    public function testDefaultReverse(): void
    {

        $this->assertEquals(
            '',
            self::$router->reverse('default/index')
        );

        $this->assertEquals(
            '',
            self::$router->reverse('index')
        );

        $this->assertEquals(
            'hello',
            self::$router->reverse('default/hello')
        );

        $this->assertEquals(
            'hello',
            self::$router->reverse('hello')
        );

        $this->assertEquals(
            'controller/',
            self::$router->reverse('controller/index')
        );

        $this->assertEquals(
            'controller/',
            self::$router->reverse('controller/')
        );

        $this->assertEquals(
            'controller/?page=1',
            self::$router->reverse('controller/', ['page' => 1])
        );

    }

    public function testMap()
    {

        self::$router->map->add('path/from/pathinfo', 'path/to/internal/uri');
        self::$router->map->add('blog/index', 'myblog/controller/index');

        $this->assertEquals(
            ['path/to/internal', 'uri'],
            self::$router->dispatch('path/from/pathinfo')
        );

        $this->assertEquals(
            'path/from/pathinfo',
            self::$router->reverse('path/to/internal/uri')
        );

        $this->assertEquals(
            ['myblog/controller', 'index'],
            self::$router->dispatch('blog/index')
        );

        $this->assertEquals(
            'blog/',
            self::$router->reverse('blog/index')
        );

    }

    public function testRoutes()
    {
        
        self::$router->routes->add('blog/{id:\d+}', 'blog/show_by_id');
        self::$router->routes->add('blog/{slug}', 'blog/show');

        $this->assertEquals(
            ['blog', 'show'],
            self::$router->dispatch('blog/hello-world', $params)
        );

        $this->assertEquals(
            'blog/hello-world',
            self::$router->reverse('blog/show', ['slug' => 'hello-world'])
        );

        $this->assertEquals(
            ['slug' => 'hello-world'],
            $params
        );

        $this->assertEquals(
            ['blog', 'show_by_id'],
            self::$router->dispatch('blog/15')
        );

    }

    public function testMapController()
    {
        self::$router->map->add('blog/', 'my/blog/homepage');
        self::$router->controllerMap->add('blog', 'my/blog/namespace/controller');

        $this->assertEquals(
            ['my/blog', 'homepage'],
            self::$router->dispatch('blog/')
        );

        $this->assertEquals(
            ['my/blog/namespace/controller', 'show'],
            self::$router->dispatch('blog/show')
        );

        $this->assertEquals(
            'blog/',
            self::$router->reverse('my/blog/homepage')
        );
        $this->assertEquals(
            'blog/show',
            self::$router->reverse('my/blog/namespace/controller/show')
        );

    }

    
    public function testMapNamespace()
    {
        self::$router->controllerMap->add('admin', 'my/backend_controller');
        self::$router->namespaceMap->add('admin', 'my/backend/namespace');


        $this->assertEquals(
            ['default', 'admin'],
            self::$router->dispatch('admin')
        );

        $this->assertEquals(
            ['my/backend_controller', 'index'],
            self::$router->dispatch('admin/')
        );
        
        $this->assertEquals(
            ['my/backend_controller', 'index'],
            self::$router->dispatch('admin/index')
        );

        $this->assertEquals(
            ['my/backend/namespace/controller', 'index'],
            self::$router->dispatch('admin/controller/')
        );

        $this->assertEquals(
            ['my/backend/namespace/controller', 'index'],
            self::$router->dispatch('admin/controller/index')
        );

        $this->assertEquals(
            'admin/controller/',
            self::$router->reverse('my/backend/namespace/controller/index')
        );

        $this->assertEquals(
            'admin/controller/',
            self::$router->reverse('my/backend/namespace/controller/')
        );
        $this->assertEquals(
            'admin/',
            self::$router->reverse('my/backend_controller/index')
        );

    }

    function testAppRouter()
    {
        $this->assertEquals(
            ['\\App\\Controller\\my\\hello_controller', 'world_http'],
            self::$appRouter->dispatch('my/hello/world')
        );
        $this->assertEquals(
            'my/hello/world',
            self::$appRouter->reverse([\App\Controller\my\hello_controller::class, 'world'])
        );
    }

}