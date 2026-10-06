<?php

declare(strict_types=1);

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\TestCase;

final class OnionArchitectureTest extends TestCase
{
    public function test_domain_does_not_import_illuminate_or_infrastructure(): void
    {
        $domainDir = realpath(__DIR__ . '/../../../app/Domain');
        $this->assertNotFalse($domainDir, "El directorio Domain debe existir");

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($domainDir));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getRealPath());

                $this->assertStringNotContainsString(
                    'use Illuminate',
                    $content,
                    "Violación Onion: El archivo de Dominio {$file->getFilename()} no debe importar Illuminate."
                );

                $this->assertStringNotContainsString(
                    'use App\\Infrastructure',
                    $content,
                    "Violación Onion: El archivo de Dominio {$file->getFilename()} no debe importar Infrastructure."
                );
            }
        }
    }

    public function test_presentation_controllers_do_not_inject_outbound_ports(): void
    {
        $controllerDir = realpath(__DIR__ . '/../../../app/Presentation/Http/Controller');
        $this->assertNotFalse($controllerDir, "El directorio Controller debe existir");

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($controllerDir));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getRealPath());

                $this->assertStringNotContainsString(
                    'use App\\Application\\Ports\\Outbound',
                    $content,
                    "Violación Onion: El controlador {$file->getFilename()} no debe inyectar puertos Outbound (repositorios)."
                );

                $this->assertStringNotContainsString(
                    'use App\\Infrastructure',
                    $content,
                    "Violación Onion: El controlador {$file->getFilename()} no debe importar clases de Infrastructure."
                );
            }
        }
    }
}
