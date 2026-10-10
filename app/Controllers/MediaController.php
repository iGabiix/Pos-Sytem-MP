<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class MediaController extends BaseController
{
    public function show(string $name)
    {
        if (!preg_match('/^[a-f0-9]{40}\.jpg$/D', $name) || !is_file(WRITEPATH . 'uploads/' . $name)) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->response->setContentType('image/jpeg')
            ->setHeader('Content-Disposition', 'inline')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents(WRITEPATH . 'uploads/' . $name));
    }
}

