<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['username', 'full_name', 'password', 'avatar', 'created_at'];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $updatedField = '';
}

