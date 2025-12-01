<?php

namespace App\Services;

use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThumbnailService
{
    /**
     * Generate a thumbnail for a PDF document
     * 
     * @param string $pdfPath Full path to the PDF file in storage
     * @param int $collectionId Collection ID
     * @param int $documentId Document ID
     * @return bool Success status
     */
    public function generateThumbnail($pdfPath, $collectionId, $documentId)
    {
        try {
            // Ensure the PDF file exists
            if (!file_exists($pdfPath)) {
                Log::error("PDF file not found: {$pdfPath}");
                return false;
            }

            // Create collection-level thumbnail directory if it doesn't exist
            $thumbnailDir = storage_path("app/public/doc-thumbnails/{$collectionId}");
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = "{$thumbnailDir}/{$documentId}_thumb.jpg";

            // Try Imagick first, then fall back to Ghostscript + GD
            if (extension_loaded('imagick')) {
                return $this->generateWithImagick($pdfPath, $thumbnailPath, $documentId);
            } else {
                return $this->generateWithGhostscript($pdfPath, $thumbnailPath, $documentId);
            }

        } catch (\Exception $e) {
            Log::error("Failed to generate thumbnail for document {$documentId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate thumbnail using Imagick
     */
    private function generateWithImagick($pdfPath, $thumbnailPath, $documentId)
    {
        try {
            $pdf = new Pdf($pdfPath);
            $pdf->setPage(1)
                ->setOutputFormat('jpg')
                ->setCompressionQuality(80);
            
            $pdf->saveImage($thumbnailPath);

            Log::info("Thumbnail generated with Imagick for document {$documentId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Imagick thumbnail generation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate thumbnail using Ghostscript
     */
    private function generateWithGhostscript($pdfPath, $thumbnailPath, $documentId)
    {
        try {
            // Find Ghostscript executable
            $gsPath = $this->findGhostscript();
            
            if (!$gsPath) {
                Log::warning('Ghostscript not found. Thumbnail generation skipped for document ' . $documentId);
                return false;
            }

            // Convert first page to PNG using Ghostscript
            $tempPng = sys_get_temp_dir() . '/pdf_thumb_' . $documentId . '.png';
            
            $command = sprintf(
                '"%s" -dSAFER -dBATCH -dNOPAUSE -dFirstPage=1 -dLastPage=1 -sDEVICE=png16m -r150 -sOutputFile="%s" "%s" 2>&1',
                $gsPath,
                $tempPng,
                $pdfPath
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($tempPng)) {
                Log::error("Ghostscript failed for document {$documentId}: " . implode("\n", $output));
                return false;
            }

            // Resize using GD library
            $this->resizeImageWithGD($tempPng, $thumbnailPath, 200, 200);

            // Clean up temp file
            if (file_exists($tempPng)) {
                unlink($tempPng);
            }

            Log::info("Thumbnail generated with Ghostscript for document {$documentId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Ghostscript thumbnail generation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find Ghostscript executable path
     */
    private function findGhostscript()
    {
        // Common Ghostscript paths on Windows
        $possiblePaths = [
            'C:/Program Files/gs/gs*/bin/gswin64c.exe',
            'C:/Program Files (x86)/gs/gs*/bin/gswin32c.exe',
            'gswin64c', // If in PATH
            'gswin32c',
            'gs',
        ];

        foreach ($possiblePaths as $path) {
            if (strpos($path, '*') !== false) {
                // Handle wildcard paths
                $matches = glob($path);
                if (!empty($matches)) {
                    return $matches[0];
                }
            } else {
                // Check if executable exists or is in PATH
                $check = shell_exec("where {$path} 2>nul");
                if (!empty($check)) {
                    return trim(explode("\n", $check)[0]);
                }
            }
        }

        return null;
    }

    /**
     * Resize image using GD library
     */
    private function resizeImageWithGD($sourcePath, $destPath, $maxWidth, $maxHeight)
    {
        list($width, $height, $type) = getimagesize($sourcePath);

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create image from source
        switch ($type) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        // Create new image
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Resize
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save as JPEG
        imagejpeg($thumb, $destPath, 80);

        // Free memory
        imagedestroy($source);
        imagedestroy($thumb);
    }

    /**
     * Get the URL for a document's thumbnail
     * 
     * @param int $collectionId Collection ID
     * @param int $documentId Document ID
     * @return string|null Thumbnail URL or null if not exists
     */
    public function getThumbnailUrl($collectionId, $documentId)
    {
        $thumbnailPath = "doc-thumbnails/{$collectionId}/{$documentId}_thumb.jpg";

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset("storage/{$thumbnailPath}");
        }

        return null;
    }    /**
     * Check if a thumbnail exists for a document
     * 
     * @param int $collectionId Collection ID
     * @param int $documentId Document ID
     * @return bool
     */
    public function thumbnailExists($collectionId, $documentId)
    {
        $thumbnailPath = "doc-thumbnails/{$collectionId}/{$documentId}_thumb.jpg";
        return Storage::disk('public')->exists($thumbnailPath);
    }

    /**
     * Delete a document's thumbnail
     * 
     * @param int $collectionId Collection ID
     * @param int $documentId Document ID
     * @return bool
     */
    public function deleteThumbnail($collectionId, $documentId)
    {
        try {
            $thumbnailPath = "doc-thumbnails/{$collectionId}/{$documentId}_thumb.jpg";

            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete thumbnail for document {$documentId}: " . $e->getMessage());
            return false;
        }
    }    /**
     * Regenerate thumbnail for a document
     * 
     * @param \App\Document $document
     * @return bool
     */
    public function regenerateThumbnail($document)
    {
        // Get the PDF path from document
        $collection = \App\Collection::find($document->collection_id);
        $storageDrive = empty($collection->storage_drive) ? 'local' : $collection->storage_drive;
        
        // Only process PDF files
        if ($document->type !== 'application/pdf') {
            return false;
        }

        try {
            // If stored remotely, we need to download it temporarily
            if ($storageDrive !== 'local') {
                $tempPath = storage_path('app/temp_' . $document->id . '.pdf');
                $content = Storage::disk($storageDrive)->get($document->path);
                file_put_contents($tempPath, $content);
                $pdfPath = $tempPath;
            } else {
                $pdfPath = storage_path('app/' . $document->path);
            }

            $result = $this->generateThumbnail($pdfPath, $document->collection_id, $document->id);

            // Clean up temp file if created
            if ($storageDrive !== 'local' && isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Failed to regenerate thumbnail for document {$document->id}: " . $e->getMessage());
            return false;
        }
    }
}
