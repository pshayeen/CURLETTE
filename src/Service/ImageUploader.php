<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Saves an uploaded picture into assets/images/<folder>/ and returns the new file name.
 */
class ImageUploader
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/assets/images')]
        private string $imagesDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = (string) $this->slugger->slug($originalName)->lower();
        $extension = $file->guessExtension() ?? 'jpg';
        $newName = $safeName . '-' . uniqid() . '.' . $extension;

        $file->move($this->imagesDirectory . '/' . $folder, $newName);

        return $newName;
    }
}