<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to(site_url());
        }
        return $this->page('auth/login', ['title' => 'Staff sign in']);
    }

    public function authenticate()
    {
        $data = $this->fields(['username']);
        $password = $this->request->getPost('password');
        if (!$this->validateData($data, ['username' => 'required|max_length[50]']) || !is_string($password) || strlen($password) > 72) {
            return $this->fail(['Enter a valid username and password.'], $data);
        }
        // One rate limit per IP, shared across sessions.
        if (!service('throttler')->check('login-' . hash('sha256', $this->request->getIPAddress()), 10, MINUTE)) {
            return $this->fail(['Too many sign-in attempts. Please wait a minute and try again.'], $data);
        }
        $user = (new UserModel())->where('username', strtolower($data['username']))->first();
        $hash = $user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        if (!password_verify($password, $hash) || !$user) {
            return $this->fail(['The username or password is incorrect.'], $data);
        }
        session()->regenerate(true);
        session()->set([
            'user_id' => (int) $user['id'], 'full_name' => $user['full_name'],
            'avatar' => $user['avatar'], 'auth_version' => hash('sha256', $user['password']),
        ]);
        return redirect()->to(site_url())->with('success', 'Welcome back, ' . $user['full_name'] . '.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}

