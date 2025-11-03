<?php

namespace OpenKit\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use OpenKit\Tests\TestCase;

class DocsRouteTest extends TestCase
{
    public function test_it_generates_json_file_via_command()
    {
        $jsonFile = config('openkit.json_file_name', 'openapi.json');
        $path = public_path($jsonFile);

        if (File::exists($path)) {
            File::delete($path);
        }

        $this->assertFalse(File::exists($path));

        Artisan::call('openkit:generate');

        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertEquals('3.0.3', $data['openapi']);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('paths', $data);

        File::delete($path);
    }

    public function test_it_can_access_the_docs_ui_route()
    {
        $uiPath = config('openkit.path', 'openkit') . '/docs';

        $response = $this->get($uiPath);

        $response->assertStatus(200);
        $response->assertViewIs('openkit::index');
        $response->assertSee('swagger-ui');
    }
}