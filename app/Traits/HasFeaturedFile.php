<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

trait HasFeaturedFile
{
    public function getFileSaveFolder()
    {
        if (! isset($this->useTypeForFileFolderName)) {
            $useTypeForFileFolderName = false;
        }

        if ($this->useTypeForFileFolderName == true) {
            if (! isset($this->type)) {
                throw new Exception('Using type for folder name, but type is not defined in the model.');
            }

            $uploadDir = $this->type.'/files';
        } else {
            $uploadDir = $this->getTable().'/files';
        }

        if (config('filesystems.default') == 'do') {
            return sprintf(
                '%s/%s',
                config('filesystems.disks.do.folder', config('app.name').'-folder'),
                $uploadDir,
            );
        }

        return $uploadDir;
    }

    public function updateFile(UploadedFile $file)
    {
        tap($this->file, function ($previousFile) use ($file) {
            $folder = $this->getFileSaveFolder();
            $this->fill([
                'file' => $file->storePublicly(
                    $folder,
                    ['disk' => config('filesystems.default', 'public')]
                ),
            ])->save();

            if ($previousFile) {
                $previousFile = str_replace(Storage::url('/'), '', $previousFile);
                Storage::disk(config('filesystems.default', 'public'))->delete($previousFile);
            }
        });
    }

    public function deleteFile()
    {
        if (is_null($this->file) || $this->file === 'null') {
            return;
        }

        $file = str_replace(Storage::url('/'), '', $this->file);
        Storage::disk(config('filesystems.default', 'public'))->delete($file);

        $this->fill(['file' => null])->save();
    }

}
