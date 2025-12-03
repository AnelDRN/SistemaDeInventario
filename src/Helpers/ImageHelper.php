<?php
declare(strict_types=1);

namespace App\Helpers;

class ImageHelper
{
    /**
     * Valida y mueve una imagen subida a su destino final.
     *
     * @param array $fileData El array de la imagen de $_FILES.
     * @param string $destinationDir El directorio donde se guardará la imagen.
     * @param array $allowedTypes Tipos MIME permitidos.
     * @param int $maxSize Tamaño máximo en bytes.
     * @return string|null La ruta relativa del archivo guardado o null si hay error.
     */
    public static function uploadImage(array $fileData, string $destinationDir, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], int $maxSize = 5 * 1024 * 1024): ?string
    {
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            // Manejar errores de subida
            return null;
        }

        $fileType = mime_content_type($fileData['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            // Tipo de archivo no permitido
            return null;
        }

        if ($fileData['size'] > $maxSize) {
            // Archivo demasiado grande
            return null;
        }

        // Crear un nombre de archivo único
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('img_', true) . '.' . strtolower($extension);
        $destinationPath = rtrim($destinationDir, '/') . '/' . $newFileName;
        
        $absolutePath = ROOT_PATH . '/' . ltrim($destinationPath, '/');

        if (move_uploaded_file($fileData['tmp_name'], $absolutePath)) {
            return $destinationPath; // Devuelve la ruta relativa para guardarla en la BD
        }

        return null;
    }

    /**
     * Crea un thumbnail de una imagen.
     *
     * @param string $sourcePath Ruta relativa de la imagen original.
     * @param string $destPath Ruta relativa donde se guardará el thumbnail.
     * @param int $thumbWidth Ancho deseado para el thumbnail.
     * @return bool True si se creó con éxito, false si no.
     */
    public static function createThumbnail(string $sourcePath, string $destPath, int $thumbWidth = 150): bool
    {
        $absoluteSource = ROOT_PATH . '/' . ltrim($sourcePath, '/');
        $absoluteDest = ROOT_PATH . '/' . ltrim($destPath, '/');

        if (!file_exists($absoluteSource)) {
            return false;
        }

        [$sourceWidth, $sourceHeight, $type] = getimagesize($absoluteSource);
        if ($sourceWidth === 0) return false;

        $sourceImage = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($absoluteSource),
            IMAGETYPE_PNG => imagecreatefrompng($absoluteSource),
            IMAGETYPE_GIF => imagecreatefromgif($absoluteSource),
            default => false,
        };

        if (!$sourceImage) {
            return false;
        }

        $thumbHeight = (int)floor($sourceHeight * ($thumbWidth / $sourceWidth));
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Conservar transparencia para PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($thumbImage, false);
            imagesavealpha($thumbImage, true);
        }

        imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);

        $success = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($thumbImage, $absoluteDest, 90),
            IMAGETYPE_PNG => imagepng($thumbImage, $absoluteDest, 9),
            IMAGETYPE_GIF => imagegif($thumbImage, $absoluteDest),
            default => false,
        };

        imagedestroy($sourceImage);
        imagedestroy($thumbImage);

        return $success;
    }
}
