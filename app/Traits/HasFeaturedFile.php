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

    public function getCvFileSaveFolder()
    {
        if (! isset($this->useTypeForFileFolderName)) {
            $useTypeForFileFolderName = false;
        }

        if ($this->useTypeForFileFolderName == true) {
            if (! isset($this->type)) {
                throw new Exception('Using type for folder name, but type is not defined in the model.');
            }

            $uploadDir = $this->type;
        } else {
            $uploadDir = $this->getTable().'/cv_files';
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
        $folder = $this->getFileSaveFolder();

        // Delete previous file if exists
        if ($this->file) {
            $previousFile = str_replace(Storage::url('/'), '', $this->file);
            Storage::disk(config('filesystems.default', 'public'))->delete($previousFile);
        }

        // Store new file with original name
        $fileName = $file->getClientOriginalName();
        $path = $file->storeAs(
            $folder,
            $fileName,
            ['disk' => config('filesystems.default', 'public')]
        );

        $this->fill(['file' => $path])->save();
    }

    public function updateCvFile(UploadedFile $file)
    {
        $folder = $this->getCvFileSaveFolder();

        // Delete previous file if exists
        if ($this->file) {
            $previousFile = str_replace(Storage::url('/'), '', $this->file);
            Storage::disk(config('filesystems.default', 'public'))->delete($previousFile);
        }

        // Store new file with original name
        $fileName = $file->getClientOriginalName();
        $path = $file->storeAs(
            $folder,
            $fileName,
            ['disk' => config('filesystems.default', 'public')]
        );

        $this->fill(['cv_file' => $path])->save();
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

    public function deleteCvFile()
    {
        if (is_null($this->cv_file) || $this->cv_file === 'null') {
            return;
        }

        $file = str_replace(Storage::url('/'), '', $this->cv_file);
        Storage::disk(config('filesystems.default', 'public'))->delete($file);

        $this->fill(['cv_file' => null])->save();
    }
}
