<?php

namespace App\Utils;

class Finder
{
    private function getClass(string $directoryPath, string $namespace)
    {
        $subscribers = [];
        $files = scandir($directoryPath, SCANDIR_SORT_ASCENDING);

        foreach ($files as $file) {
            // Ignore directories and abstract classes.
            if (is_dir($file) || 0 === stripos($file, 'Abstract')) {
                continue;
            }

// Get the name of the file without the suffix.
            $file = explode('.', $file);
            $file = $file[0];

            $subscribers[] = $namespace . $file;
        }
        return $subscribers;
    }

    /**
     * Non utilisé pour l'instant (Et je pense pas l'utilisé lol)
     * @return array
     */
    public function getRepositories(): array
    {
        return $this->getClass(__DIR__ . '/../Repository/', 'App\\Repository\\');
    }

    public function getEntities(){
        return $this->getClass(__DIR__ . '/../Entity/', 'App\\Entity\\');
    }
}