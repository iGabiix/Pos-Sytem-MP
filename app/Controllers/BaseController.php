<?php

namespace App\Controllers;

use CodeIgniter\Controller;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'pos'];

    protected function page(string $view, array $data = []): string
    {
        return view($view, $data);
    }

    protected function fail(array $errors, array $safeInput = [])
    {
        // Never flash raw POST data: it can contain passwords or binary uploads.
        return redirect()->back()->with('errors', $errors)->with('form_data', $safeInput);
    }

    protected function fields(array $names): array
    {
        $values = [];
        foreach ($names as $name) {
            $value = $this->request->getPost($name);
            $values[$name] = is_string($value) ? trim($value) : '';
        }
        return $values;
    }
}

