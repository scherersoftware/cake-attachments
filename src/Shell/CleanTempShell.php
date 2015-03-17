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
        $files = $dir->find();

        $this->out('Found ' . count($folders[0]) . ' folders and ' . count($files) . ' files');
        $this->out();

        foreach ($folders[0] as $folderName) {
            $folder = new Folder($dir->pwd() . $folderName);
            $folder->delete();
        }
        $this->out($folder->errors());

        foreach ($files as $filePath) {
            $file = new File($filePath);
            $file->delete();
            $file->close();
        }

        $this->out('Completed');
    }
}
