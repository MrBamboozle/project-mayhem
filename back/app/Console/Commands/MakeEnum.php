<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

class MakeEnum extends GeneratorCommand implements PromptsForMissingInput
{
    private const STUB_OPTIONS = ['int', 'string'];

    private const STUB_NAME = 'enum.stub';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:enum {name} {--int} {--string}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a enum class';

    public function getStub(): string
    {
        $options = $this->options();
        $stubName = self::STUB_NAME;

        foreach ($options as $option => $set) {
            if ($set && in_array($option, self::STUB_OPTIONS)) {
                $stubParts = explode('.', self::STUB_NAME);
                array_splice($stubParts, 1, 0, $option);
                $stubName = implode('.', $stubParts);

                break;
            }
        }

        return base_path() . '/stubs/' . $stubName;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/Enums/'.str_replace('\\', '/', $name).'.php';
    }
}
