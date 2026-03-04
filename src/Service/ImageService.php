<?php

declare(strict_types=1);

namespace Service;

class ImageService
{
   
    public function upload(array $file, string $dossier): ?string
    {
        
        // Vérif type image
        $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $typesAutorises)) {
            throw new \InvalidArgumentException('Format d\'image non autorisé. Utilisez JPG, PNG ou WebP.');
        }

        // Vérif taille (max 2Mo)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \InvalidArgumentException('L\'image ne doit pas dépasser 2Mo.');
        }

        // Génére nom unique
        $extension = match($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };
        $nomFichier = uniqid() . '.' . $extension;

       // Déplace fichier

        $cheminComplet = '/var/www/html/public/' . $dossier . '/' . $nomFichier;
        if (!move_uploaded_file($file['tmp_name'], $cheminComplet)) {
            throw new \RuntimeException('Erreur lors de l\'upload de l\'image.');
        }

        return $dossier . '/' . $nomFichier;
        }}