<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\IniFile;

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

        $paths = IniFile::open('config/program.ini')->get('main')['projects_search_path'] ?? '{root_dir}\home';
        [$searchPath] = explode(';', $paths, 2);

        $defaultStartDirectory = str_replace(['/', '{root_dir}'], ['\\', ROOT_DIR], $searchPath);

        if (!$dir) {
            return $defaultStartDirectory;
        }

        $protect = 0;
        while (!file_exists($dir) && $protect++ < 100) {
            $dir = dirname($dir);
        }

        if (strlen($dir) < 4) return $defaultStartDirectory;

        return $dir;
    }
}
