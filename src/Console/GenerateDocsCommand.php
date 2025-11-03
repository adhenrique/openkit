<?php

namespace OpenKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use OpenKit\Facades\OpenKit;

class GenerateDocsCommand extends Command
{
    protected $signature = 'openkit:generate';
    protected $description = 'Gera o arquivo openapi.json com base nas definições.';

    public function handle()
    {
        $this->info('Gerando documentação OpenAPI...');

        $spec = OpenKit::generate();
        $fileName = config('openkit.json_file_name', 'openapi.json');
        $path = public_path($fileName);
        $json = json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        try {
            File::put($path, $json);
            $this->info("Documentação salva com sucesso em: {$path}");
        } catch (\Exception $e) {
            $this->error("Não foi possível salvar o arquivo: " . $e->getMessage());
        }

        return 0;
    }
}