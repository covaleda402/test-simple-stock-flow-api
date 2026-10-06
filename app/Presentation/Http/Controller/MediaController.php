<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\GetMediaPort;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class MediaController extends Controller
{
    public function __construct(
        private GetMediaPort $getMediaPort
    ) {}

    public function show(string $key): Response
    {
        $path = $this->getMediaPort->execute($key);
        if ($path === null || !file_exists($path)) {
            // E-15: 404 vacío si la clave no existe
            return response('', 404);
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        $content = (string) file_get_contents($path);

        return response($content, 200, [
            'Content-Type' => $mime,
        ]);
    }
}
