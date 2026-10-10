<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('user_id') ? (new UserModel())->find($session->get('user_id')) : null;
        if (!$user || $session->get('auth_version') !== hash('sha256', $user['password'])) {
            $session->remove(['user_id', 'full_name', 'avatar', 'auth_version']);
            return redirect()->to(site_url('login'))->with('error', 'Please sign in to access the campus store.');
        }
        $session->set(['full_name' => $user['full_name'], 'avatar' => $user['avatar']]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, private');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
    }
}

