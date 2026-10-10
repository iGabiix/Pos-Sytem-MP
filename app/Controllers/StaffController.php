<?php

namespace App\Controllers;

class StaffController extends ResourceController
{
    protected string $resource = 'staff';
    protected string $singular = 'staff member';
    protected string $modelClass = \App\Models\UserModel::class;
    protected array $inputFields = ['full_name', 'username'];
    protected ?string $imageField = 'avatar';

    protected function rules(?int $id): array
    {
        return [
            'full_name' => 'required|max_length[100]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_dash|is_unique[users.username,id,' . ($id ?? 0) . ']',
            'password' => ($id ? 'permit_empty' : 'required') . '|min_length[12]|max_length[72]',
        ];
    }
}

