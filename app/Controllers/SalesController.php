<?php

namespace App\Controllers;

use App\Libraries\SaleService;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class SalesController extends BaseController
{
    public function create()
    {
        $keys = session()->get('sale_keys') ?? [];
        $key = bin2hex(random_bytes(32));
        $keys[] = $key;
        session()->set('sale_keys', array_slice($keys, -20));
        return $this->page('sales/create', [
            'title' => 'Record sale',
            'products' => (new ProductModel())->orderBy('name')->findAll(),
            'customers' => (new CustomerModel())->orderBy('full_name')->findAll(),
            'requestKey' => $key,
        ]);
    }

    public function store()
    {
        $data = $this->fields(['product_id', 'customer_id', 'quantity', 'request_key']);
        if (!$this->validateData($data, [
            'product_id' => 'required|is_natural_no_zero',
            'customer_id' => 'permit_empty|is_natural_no_zero',
            'quantity' => 'required|is_natural_no_zero|less_than_equal_to[1000000]',
            'request_key' => 'required|exact_length[64]|alpha_numeric',
        ])) {
            return $this->fail($this->validator->getErrors(), $data);
        }
        if (!in_array($data['request_key'], session()->get('sale_keys') ?? [], true)) {
            return redirect()->to(site_url('sales/new'))->with('error', 'This sale form has expired. Please select the product and try again.');
        }
        try {
            $id = (new SaleService(db_connect()))->record(
                (int) $data['product_id'], $data['customer_id'] !== '' ? (int) $data['customer_id'] : null,
                (int) session()->get('user_id'), (int) $data['quantity'], $data['request_key']
            );
            return redirect()->to(site_url('sales'))->with('success', 'Sale #' . str_pad((string) $id, 5, '0', STR_PAD_LEFT) . ' recorded. Inventory is up to date.');
        } catch (\DomainException $e) {
            return $this->fail([$e->getMessage()], $data);
        } catch (\Throwable $e) {
            log_message('error', 'Sale failed: {message}', ['message' => $e->getMessage()]);
            return $this->fail(['The sale could not be recorded. No stock was deducted. Please try again.'], $data);
        }
    }

    public function index()
    {
        $model = (new SaleModel())->history();
        $raw = $this->request->getGet('q');
        $q = is_string($raw) ? mb_substr(trim($raw), 0, 100) : '';
        if ($q !== '') {
            $model->groupStart()->like('products.name', $q)->orLike('customers.full_name', $q)->orLike('users.full_name', $q)->groupEnd();
        }
        return $this->page('sales/index', [
            'title' => 'Sales history', 'rows' => $model->paginate(15), 'pager' => $model->pager, 'q' => $q,
            'summary' => db_connect()->table('sales')->select('COUNT(*) AS transactions, COALESCE(SUM(quantity), 0) AS units, COALESCE(SUM(total_price), 0) AS revenue', false)->get()->getRowArray(),
        ]);
    }
}

