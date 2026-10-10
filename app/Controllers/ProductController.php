<?php

namespace App\Controllers;

use App\Models\ProductModel;
use RuntimeException;

class ProductController extends ResourceController
{
    protected string $resource = 'products';
    protected string $singular = 'product';
    protected string $modelClass = ProductModel::class;
    protected array $inputFields = ['name', 'price', 'stock_quantity'];
    protected string $searchField = 'name';
    protected ?string $imageField = 'image';

    protected function rules(?int $id): array
    {
        return [
            'name' => 'required|max_length[100]',
            'price' => ['required', 'regex_match[/^\d{1,8}(?:\.\d{1,2})?$/D]', 'greater_than[0]'],
            'stock_quantity' => 'required|is_natural|less_than_equal_to[2147483647]',
        ];
    }

    protected function persist(?int $id, array $data, array $old): void
    {
        if (!$id) {
            parent::persist(null, $data, $old);
            return;
        }
        $baseline = $this->request->getPost('stock_before');
        if (!is_string($baseline) || !ctype_digit($baseline)) {
            throw new RuntimeException('Reload the product before saving.');
        }
        $db = db_connect();
        $db->table('products')->where('id', $id)->where('deleted_at', null)
            ->where('stock_quantity', (int) $baseline)->update($data);
        if ($db->affectedRows() === 0) {
            $current = $this->model()->find($id);
            $same = $current && (int) $current['stock_quantity'] === (int) $baseline;
            foreach ($data as $field => $value) {
                $same = $same && (string) $current[$field] === (string) $value;
            }
            if (!$same) {
                throw new RuntimeException('Stock changed while you were editing. Reload this product to review the latest stock before saving.');
            }
        }
    }
}

