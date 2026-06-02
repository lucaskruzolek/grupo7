<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImageOptimizerService
{
    /**
     * Valida, reescala y convierte una imagen subida al formato WebP.
     *
     * @param UploadedFile $file El archivo subido desde el request.
     * @param int $maxDimension El tamaño máximo de ancho/alto para la imagen.
     * @param int $quality Calidad de compresión WebP (1-100).
     * @return string Ruta absoluta del archivo WebP temporal generado.
     * @throws \Exception Si el archivo no es una imagen válida o falla la conversión.
     */
    public function convertToWebp(UploadedFile $file, int $maxDimension = 1200, int $quality = 80): string
    {
        $filePath = $file->getRealPath();
        
        // Obtener información de la imagen
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            throw new \Exception('El archivo proporcionado no es una imagen válida.');
        }

        list($width, $height, $type) = $imageInfo;

        // Crear recurso de imagen según el tipo de archivo original
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($filePath);
                // Preservar transparencia para PNG si es necesario (aunque WebP la soporta)
                imagealphablending($srcImage, true);
                imagesavealpha($srcImage, true);
                break;
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = imagecreatefromwebp($filePath);
                break;
            default:
                // Intentar cargar como string si el tipo no es detectado por getimagesize
                $fileContent = file_get_contents($filePath);
                $srcImage = imagecreatefromstring($fileContent);
                if (!$srcImage) {
                    throw new \Exception('Formato de imagen no soportado. Use JPG, PNG, WEBP o GIF.');
                }
        }

        // Calcular nuevas dimensiones manteniendo la relación de aspecto
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($height * $maxDimension) / $width);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($width * $maxDimension) / $height);
            }
        }

        // Crear una nueva imagen con las dimensiones deseadas
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Si es transparente (PNG/WEBP), preservar transparencia en la imagen de destino
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);

        // Re-muestrear / Redimensionar
        imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        // Crear carpeta temporal si no existe
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Definir ruta del archivo WebP temporal
        $tempFileName = uniqid('img_', true) . '.webp';
        $tempFilePath = $tempDir . '/' . $tempFileName;

        // Guardar la imagen como WebP
        $success = imagewebp($dstImage, $tempFilePath, $quality);

        // Liberar memoria
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if (!$success) {
            throw new \Exception('No se pudo guardar la imagen procesada en formato WebP.');
        }

        return $tempFilePath;
    }
}
