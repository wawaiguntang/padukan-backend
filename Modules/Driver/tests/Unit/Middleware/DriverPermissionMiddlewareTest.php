<?php

namespace Modules\Driver\Tests\Unit\Http\Middleware;

use Tests\TestCase;
use Modules\Driver\Http\Middleware\DriverPermissionMiddleware;
use App\Shared\Authorization\Services\IPermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class DriverPermissionMiddlewareTest extends TestCase
{
    protected $permissionService;
    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissionService = Mockery::mock(IPermissionService::class);
        $this->middleware = new DriverPermissionMiddleware($this->permissionService);
    }

    /** @test */
    public function it_allows_request_when_user_has_permission()
    {
        // Arrange
        $request = Request::create('/test', 'GET');
        $user = new class {
            public $id = 1;
        };
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->permissionService->shouldReceive('userHasPermission')
            ->with(1, 'driver.profile.view')
            ->once()
            ->andReturn(true);

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        }, 'driver.profile.view');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_denies_request_when_user_lacks_permission()
    {
        // Arrange
        $request = Request::create('/test', 'GET');
        $user = new class {
            public $id = 1;
        };
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->permissionService->shouldReceive('userHasPermission')
            ->with(1, 'driver.profile.view')
            ->once()
            ->andReturn(false);

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        }, 'driver.profile.view');

        // Assert
        $this->assertEquals(403, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('driver::auth.insufficient_permissions', $responseData['message']);
    }

    /** @test */
    public function it_denies_request_when_user_is_not_authenticated()
    {
        // Arrange
        $request = Request::create('/test', 'GET');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        }, 'driver.profile.view');

        // Assert
        $this->assertEquals(401, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('driver::auth.user_not_authenticated', $responseData['message']);
    }

    /** @test */
    public function it_allows_request_when_user_has_any_of_multiple_permissions()
    {
        // Arrange
        $request = Request::create('/test', 'GET');
        $user = new class {
            public $id = 1;
        };
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->permissionService->shouldReceive('userHasPermission')
            ->with(1, 'driver.profile.view')
            ->once()
            ->andReturn(false);

        $this->permissionService->shouldReceive('userHasPermission')
            ->with(1, 'driver.profile.update')
            ->once()
            ->andReturn(true);

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        }, 'driver.profile.view', 'driver.profile.update');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
