<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;

class FilesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $directory = $this->findDirectory($request->input('directory'));
        $showFiles = (bool)$request->input('files');

        return Response::json([
            'directory' => $directory,
            'list'      => $this->readFs($directory, $showFiles),
        ]);
    }

    public function create(Request $request): Response
    {
        $directory = $request->input('directory');
        $showFiles = (bool)$request->input('files');
        $newDir = $request->input('newDir');

        $result = @mkdir($directory.'/'.$newDir);

        if ($result) {
            return Response::json([
                'directory' => $directory,
                'list'      => $this->readFs($directory, $showFiles),
            ]);
        }

        return Response::json()
            ->status(500)
            ->message('Не удалось создать папку');
    }

    private function readFs(string $directory, bool $showFiles): array
    {
        $result = [];
        $dir = new \DirectoryIterator($directory);

        if ($directory !== dirname($directory)) {
            $result[dirname($directory)] = '..';
        }

        /** @var \DirectoryIterator $file */
        foreach ($dir as $file) {
            if ($file->isDot()) continue;
            if (!$showFiles && !$file->isDir()) continue;

            $result[$file->getPathname()] = $file->getFilename();
        }

        ksort($result);

        return $result;
    }

    private function findDirectory(string $dir): string
    {
        if (file_exists($dir)) return $dir;

        if (!$dir) {
            return str_replace('/', '\\', ROOT_DIR.'/home');
        }

        while (!file_exists($dir)) {
            $dir = dirname($dir);
        }

        if (strlen($dir) < 4) return str_replace('/', '\\', ROOT_DIR.'/home');

        return $dir;
    }
}
