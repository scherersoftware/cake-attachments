<?php

namespace Attachments\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class CleanTempShell extends Shell
{
    /**
     * main method
     *
     * @param  string $tempDir an other directory to clear of all folders and files, if desired
     * @return void
     */
    public function main($tempDir = null)
    {
        if (empty($tempDir)) {
            $tempDir = Configure::read('Attachments.tmpUploadsPath');
        }

        if (!Folder::isAbsolute($tempDir)) {
            $this->out('The path must be absolute, "' . $tempDir . '" given.');
            exit;
        }

        $Folder = new Folder();
        if ($Folder->cd($tempDir) === false) {
            $this->out('Path "' . $tempDir . '" doesn\'t seem to exist.');
            exit;
        }

        $dir = new Folder($tempDir);
        $folders = $dir->read();
        $files = $dir->findRecursive();
        $deletedFiles = 0;
        $deletedFolders = 0;

        $this->out('Found ' . count($folders[0]) . ' folders and ' . count($files) . ' files');

        foreach ($files as $filePath) {
            $file = new File($filePath);
            // only delete if last change is longer than 24 hours ago
            if ($file->lastChange() < (time() - 24 * 60 * 60) && $file->delete()) {
                $deletedFiles++;
            }
            $file->close();
        }

        foreach ($folders[0] as $folderName) {
            $folder = new Folder($dir->pwd() . $folderName);
            // only delete if folder is empty
            if ($folder->dirsize() === 0 && $folder->delete()) {
                $deletedFolders++;
            }
        }


        $this->out('Deleted ' . $deletedFolders . ' folders and ' . $deletedFiles . ' files.');
    }
}
