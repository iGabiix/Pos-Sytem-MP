<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

final class ImageUpload
{
    public function save(?UploadedFile $file, bool $avatar = false): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (!$file->isValid() || $file->getSize() > 2 * 1024 * 1024) {
            throw new RuntimeException('Choose a valid image no larger than 2 MB.');
        }
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $info = @getimagesize($file->getTempName());
        if (!in_array($file->getMimeType(), $allowed, true) || !$info || !in_array($info['mime'], $allowed, true)) {
            throw new RuntimeException('Only JPEG, PNG, and WebP images are accepted.');
        }
        if ($info[0] > 4000 || $info[1] > 4000) {
            throw new RuntimeException('The image must be no larger than 4000 × 4000 pixels.');
        }
        if (!extension_loaded('gd')) {
            throw new RuntimeException('Image processing is unavailable. Ask the administrator to enable PHP GD.');
        }
        $directory = WRITEPATH . 'uploads/';
        if (!is_dir($directory)) {
            mkdir($directory, 0750, true);
        }
        $name = bin2hex(random_bytes(20)) . '.jpg';
        try {
            // Decode, resize/crop, and re-encode; never publish the original upload.
            $image = service('image', 'gd')->withFile($file->getTempName());
            if ($avatar) {
                $image->fit(256, 256, 'center');
            } else {
                $image->resize(900, 900, true, 'auto');
            }
            if (!$image->convert(IMAGETYPE_JPEG)->save($directory . $name, 85)) {
                throw new RuntimeException('The processed image could not be written.');
            }
        } catch (\Throwable $e) {
            $this->remove($name);
            log_message('error', 'Image processing failed: {message}', ['message' => $e->getMessage()]);
            throw new RuntimeException('This image could not be processed. Please try a different image.');
        }
        return $name;
    }

    public function remove(?string $name): void
    {
        if ($name && preg_match('/^[a-f0-9]{40}\.jpg$/D', $name) && is_file(WRITEPATH . 'uploads/' . $name)) {
            unlink(WRITEPATH . 'uploads/' . $name);
        }
    }
}
