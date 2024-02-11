<?php

namespace App\Console\Commands;

use Dedoc\Scramble\Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OpenApiExportCommand extends Command
{
    protected $signature = 'openapi:export {--path= : The path where to save the exported json file}';

    protected $description = 'Export the OpenAPI specifications to a json file.';

    public function handle(Generator $generator): void
    {
        $specifications = json_encode($generator());

        $filename = $this->option('path') ?? 'openapi.json';

        File::put($filename, $specifications);

        $this->info("OpenAPI specifications exported to {$filename}.");
    }
}
