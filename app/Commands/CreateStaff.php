<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateStaff extends BaseCommand
{
    protected $group = 'POS';
    protected $name = 'pos:create-staff';
    protected $description = 'Create an initial staff account from POS_ADMIN_USERNAME, POS_ADMIN_NAME, and POS_ADMIN_PASSWORD environment variables.';

    public function run(array $params)
    {
        $data = [
            'username' => strtolower((string) env('POS_ADMIN_USERNAME', 'admin')),
            'full_name' => (string) env('POS_ADMIN_NAME', 'Store Administrator'),
            'password' => (string) env('POS_ADMIN_PASSWORD', ''),
        ];
        $validation = service('validation');
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|alpha_dash|is_unique[users.username]',
            'full_name' => 'required|max_length[100]',
            'password' => 'required|min_length[12]|max_length[72]',
        ]);
        if (!$validation->run($data) || strlen($data['password']) > 72) {
            CLI::error('Set a unique POS_ADMIN_USERNAME and a POS_ADMIN_PASSWORD of 12-72 characters (at most 72 bytes).');
            return EXIT_ERROR;
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        (new UserModel())->insert($data);
        CLI::write('Staff account created. Sign in with the credentials you supplied.', 'green');
        return EXIT_SUCCESS;
    }
}

