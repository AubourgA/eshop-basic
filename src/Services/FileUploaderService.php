<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Service pour gérer l'upload de fichiers.
 * 
 * Ce service permet de sécuriser et déplacer un fichier uploadé
 * vers un répertoire donné, en renommant le fichier pour éviter
 * les collisions et les caractères problématiques.
 */
class FileUploaderService
{
    public function __construct(private string $uploadDir, 
                                private SluggerInterface $slugger)
    { }

    public function uploadFile(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->uploadDir, $newFilename);
        } catch (FileException $e) {
            throw new FileException('Le fichier n\'a pas pu être déplacé.');
        }
      

        return $newFilename;
    }

}