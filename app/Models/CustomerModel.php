<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $allowedFields = ['full_name', 'email', 'phone', 'created_at'];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $updatedField = '';
}

