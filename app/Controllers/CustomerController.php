<?php

namespace App\Controllers;

class CustomerController extends ResourceController
{
    protected string $resource = 'customers';
    protected string $singular = 'customer';
    protected string $modelClass = \App\Models\CustomerModel::class;
    protected array $inputFields = ['full_name', 'email', 'phone'];

    protected function rules(?int $id): array
    {
        return [
            'full_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => ['permit_empty', 'max_length[20]', 'regex_match[/^[+0-9(). -]+$/D]'],
        ];
    }
}

