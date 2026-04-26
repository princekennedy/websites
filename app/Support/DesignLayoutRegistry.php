<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DesignLayoutRegistry
{
    /**
     * @return array<int, string>
     */
    public function options(): array
    {
        $designPath = resource_path('views/designs');

        if (! is_dir($designPath)) {
            return ['default'];
        }

        $layouts = collect(['default'])
            ->merge(collect(File::directories($designPath))->map(fn (string $path): string => basename($path)))
            ->merge(
                collect(File::files($designPath))
                    ->filter(fn ($file): bool => Str::endsWith($file->getFilename(), '.blade.php'))
                    ->map(fn ($file): string => Str::before($file->getFilename(), '.blade.php'))
            )
            ->map(fn (string $layout): string => trim($layout))
            ->filter()
            ->unique()
            ->values();

        return $layouts->all();
    }
}