<?php

namespace App\Controllers;

use App\Libraries\ImageUpload;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;
use RuntimeException;

abstract class ResourceController extends BaseController
{
    protected string $resource;
    protected string $singular;
    protected string $modelClass;
    protected array $inputFields;
    protected string $searchField = 'full_name';
    protected ?string $imageField = null;

    protected function model(): Model
    {
        return new $this->modelClass();
    }

    abstract protected function rules(?int $id): array;

    public function index()
    {
        $model = $this->model();
        $raw = $this->request->getGet('q');
        $q = is_string($raw) ? mb_substr(trim($raw), 0, 100) : '';
        if ($q !== '') {
            $model->like($this->searchField, $q);
        }
        return $this->page('resources/index', [
            'title' => ucfirst($this->resource), 'resource' => $this->resource, 'singular' => $this->singular,
            'rows' => $model->orderBy('id', 'DESC')->paginate(12), 'pager' => $model->pager, 'q' => $q,
            'total' => $this->model()->countAllResults(),
        ]);
    }

    public function create()
    {
        return $this->form([]);
    }

    public function edit(int $id)
    {
        return $this->form($this->find($id));
    }

    protected function form(array $row)
    {
        return $this->page('resources/form', [
            'title' => (isset($row['id']) ? 'Edit ' : 'Add ') . $this->singular,
            'resource' => $this->resource, 'singular' => $this->singular, 'row' => $row,
        ]);
    }

    protected function find(int $id): array
    {
        return $this->model()->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }

    public function store()
    {
        return $this->save(null);
    }

    public function update(int $id)
    {
        return $this->save($id);
    }

    protected function save(?int $id)
    {
        $old = $id ? $this->find($id) : [];
        $data = $this->fields($this->inputFields);
        if (isset($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }
        $safe = $data;
        if ($this->resource === 'staff') {
            $password = $this->request->getPost('password');
            $data['password'] = is_string($password) ? $password : '';
        }
        if (!$this->validateData($data, $this->rules($id))) {
            return $this->fail($this->validator->getErrors(), $safe);
        }
        $image = null;
        try {
            if ($this->resource === 'staff') {
                if (strlen($data['password']) > 72) {
                    throw new RuntimeException('The password must be at most 72 bytes.');
                }
                if ($data['password'] !== '') {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                } else {
                    unset($data['password']);
                }
            }
            if ($this->imageField) {
                $image = (new ImageUpload())->save($this->request->getFile($this->imageField), $this->resource === 'staff');
                if ($image) {
                    $data[$this->imageField] = $image;
                }
            }
            if ($this->resource === 'products') {
                $data['price'] = \App\Libraries\Money::decimal(\App\Libraries\Money::cents($data['price']));
            }
            $this->persist($id, $data, $old);
        } catch (\Throwable $e) {
            (new ImageUpload())->remove($image);
            if ($e instanceof RuntimeException && !($e instanceof \CodeIgniter\Database\Exceptions\DatabaseException)) {
                return $this->fail([$e->getMessage()], $safe);
            }
            log_message('error', 'Management save failed: {message}', ['message' => $e->getMessage()]);
            return $this->fail(['Could not save this record. Check for a duplicate username, then try again.'], $safe);
        }
        if ($image) {
            (new ImageUpload())->remove($old[$this->imageField] ?? null);
        }
        if ($this->resource === 'staff' && $id === (int) session()->get('user_id') && isset($data['password'])) {
            session()->regenerate(true);
            session()->set('auth_version', hash('sha256', $data['password']));
        }
        return redirect()->to(site_url($this->resource))->with('success', ucfirst($this->singular) . ($id ? ' updated.' : ' added.'));
    }

    protected function persist(?int $id, array $data, array $old): void
    {
        $result = $id ? $this->model()->update($id, $data) : $this->model()->insert($data);
        if ($result === false) {
            throw new RuntimeException('The record could not be saved. Please try again.');
        }
    }

    public function delete(int $id)
    {
        $this->find($id);
        if ($this->resource === 'staff' && $id === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete the account you are signed in with.');
        }
        if ($this->resource === 'staff') {
            $db = db_connect();
            $db->transException(true)->transBegin();
            try {
                $lock = $db->DBDriver === 'MySQLi' ? ' FOR UPDATE' : '';
                $active = $db->query('SELECT id FROM users WHERE deleted_at IS NULL ORDER BY id' . $lock)->getResultArray();
                $activeIds = array_map('intval', array_column($active, 'id'));
                if (!in_array((int) session()->get('user_id'), $activeIds, true) || count($activeIds) <= 1) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'At least one active staff account must remain. Please sign in again if your account was removed.');
                }
                $this->model()->delete($id);
                $db->transCommit();
            } catch (\Throwable $e) {
                $db->transRollback();
                log_message('error', 'Staff removal failed: {message}', ['message' => $e->getMessage()]);
                return redirect()->back()->with('error', 'The staff member could not be removed. Please try again.');
            }
        } else {
            $this->model()->delete($id);
        }
        return redirect()->to(site_url($this->resource))->with('success', ucfirst($this->singular) . ($this->resource === 'products' ? ' archived.' : ' deleted.') . ' Past sales are preserved.');
    }
}
