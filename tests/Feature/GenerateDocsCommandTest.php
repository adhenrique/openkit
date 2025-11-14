<?php

namespace OpenKit\Tests\Feature;

use Illuminate\Support\Facades\File;
use OpenKit\Tests\TestCase;

class GenerateDocsCommandTest extends TestCase
{
    /** @var string O caminho completo para o arquivo JSON */
    private string $jsonPath;

    /** @var string O caminho do arquivo com as definições */
    private string $definitionsPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Pega o nome do arquivo da config (Testbench carrega o config padrão)
        $fileName = config('openkit.json_file_name', 'openapi.json');

        // Define o caminho (public_path() funciona no Testbench)
        $this->jsonPath = public_path($fileName);

        $this->definitionsPath = config('openkit.definitions', base_path('routes/openkit.php'));

        // Garante que o arquivo não exista antes do teste
        if (File::exists($this->jsonPath)) {
            File::delete($this->jsonPath);
        }

        if (File::exists($this->definitionsPath)) {
            File::delete($this->definitionsPath);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->jsonPath)) {
            File::delete($this->jsonPath);
        }

        if (File::exists($this->definitionsPath)) {
            File::delete($this->definitionsPath);
        }

        parent::tearDown();
    }

    public function test_it_generates_the_openapi_json_file_with_correct_content()
    {
        // 1. ARRANGE
        // Garante que o arquivo não existe
        $this->assertFalse(File::exists($this->jsonPath));

        File::copy(__DIR__.'/../stubs/test_definitions.php', $this->definitionsPath);

        // 2. ACT
        // Roda o comando e captura a saída do console
        $this->artisan('openkit:generate')
            ->expectsOutputToContain("Documentação salva com sucesso em: {$this->jsonPath}")
            ->assertExitCode(0); // 0 = sucesso

        // 3. ASSERT

        // O arquivo foi criado?
        $this->assertTrue(File::exists($this->jsonPath));

        // O conteúdo é JSON válido?
        $content = File::get($this->jsonPath);
        $this->assertJson($content);

        // O conteúdo está correto?
        $data = json_decode($content, true);

        // Verifica o 'info' (que vem do 'tests/TestCase.php')
        $this->assertEquals('API de Teste', $data['info']['title']);
        $this->assertEquals('1.0.0', $data['info']['version']);

        // Verifica a 'tag' que definimos
        $this->assertEquals('TestTag', $data['tags'][0]['name']);

        // Verifica o 'path' que definimos
        $this->assertArrayHasKey('/api/new', $data['paths']);
        $pathData = $data['paths']['/api/new']['get'];
        $this->assertEquals('Nova Rota', $pathData['summary']);
        $this->assertEquals(['TestTag'], $pathData['tags']);
        $this->assertEquals('Rota OK', $pathData['responses']['200']['description']);
    }

    public function test_it_overwrites_an_existing_file()
    {
        // 1. Arrange: Cria um arquivo 'fake' primeiro
        File::put($this->jsonPath, '{"info": {"title": "Antigo"}}');
        $this->assertTrue(File::exists($this->jsonPath));

        // Define uma nova rota
        File::copy(__DIR__.'/../stubs/test_definitions.php', $this->definitionsPath);

        // 2. Act
        $this->artisan('openkit:generate')->assertExitCode(0);

        // 3. Assert
        $content = File::get($this->jsonPath);
        $data = json_decode($content, true);

        // O conteúdo antigo foi sobrescrito?
        $this->assertNotEquals('Antigo', $data['info']['title']);
        $this->assertEquals('API de Teste', $data['info']['title']); // (do TestCase)

        // O novo conteúdo existe?
        $this->assertArrayHasKey('/api/new', $data['paths']);
        $this->assertEquals('Nova Rota', $data['paths']['/api/new']['get']['summary']);
    }
}
