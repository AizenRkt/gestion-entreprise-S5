<?php

namespace app\utils;

use Flight;

class FileUtils {

    /**
     * Upload d'un fichier
     *
     * @param string $dossier Destination relative ou absolue
     * @param array $file Le fichier de $_FILES (ex: $_FILES['file'])
     * @param array|null $allowedTypes Types MIME autorisés (ex: ['image/jpeg', 'application/pdf'])
     * @param int|null $maxSize Taille max en octets (ex: 5*1024*1024 pour 5MB)
     * @return string|null Chemin relatif du fichier uploadé ou null en cas d'erreur
     */
    public static function upload(string $dossier, array $file, ?array $allowedTypes = null, ?int $maxSize = null): ?string
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($allowedTypes && !in_array($file['type'], $allowedTypes)) {
            return null;
        }

        if ($maxSize && $file['size'] > $maxSize) {
            return null;
        }

        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nomFichier = uniqid() . '.' . $ext;

        $cheminComplet = rtrim($dossier, '/') . '/' . $nomFichier;

        if (move_uploaded_file($file['tmp_name'], $cheminComplet)) {
            return $nomFichier;
        }

        return null;
    }
}
