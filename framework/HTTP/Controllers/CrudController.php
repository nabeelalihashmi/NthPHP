<?php
/*
    Warning: It is just Proof of Concept!
*/

namespace Framework\HTTP\Controllers;

use RedBeanPHP\R;
use Framework\HTTP\Responses\JSONResponse;

class CrudController {
    protected string $model;
    protected array $allowedFields = [];

    public function __construct(string $model, array $allowedFields = []) {
        $this->model = $model;
        $this->allowedFields = $allowedFields;
    }

    
    protected function filterAllowedFields(array $data): array {
        return array_filter(
            $data,
            fn($key) => in_array($key, $this->allowedFields),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function index(int $page = 1, int $limit = 10, string $search = ''): JSONResponse {
        $offset = ($page - 1) * $limit;

        $searchQuery = '';
        $params = [];

        if ($search) {
            $searchQuery = 'WHERE name LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $data = R::findAll($this->model, $searchQuery . ' LIMIT ? OFFSET ?', array_merge($params, [$limit, $offset]));
        $total = R::count($this->model, $searchQuery, $params);

        return new JSONResponse([
            'success' => true,
            'message' => 'Fetched records successfully.',
            'extra' => [
                'data' => $data,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit),
                ],
            ],
        ]);
    }

    public function show(int $id): JSONResponse {
        $bean = R::load($this->model, $id);
        if (!$bean->id) {
            return new JSONResponse([
                'success' => false,
                'message' => 'Record not found.',
                'extra' => null,
            ], 404);
        }

        return new JSONResponse([
            'success' => true,
            'message' => 'Record fetched successfully.',
            'extra' => $bean,
        ]);
    }

    public function create(array $data): JSONResponse {
        // Filter the data to only include allowed fields
        $filteredData = $this->filterAllowedFields($data);

        $bean = R::dispense($this->model);
        foreach ($filteredData as $key => $value) {
            $bean->$key = $value;
        }
        $id = R::store($bean);

        return new JSONResponse([
            'success' => true,
            'message' => 'Record created successfully.',
            'extra' => ['id' => $id],
        ], 201);
    }

    public function update(int $id, array $data): JSONResponse {
        // Filter the data to only include allowed fields
        $filteredData = $this->filterAllowedFields($data);

        $bean = R::load($this->model, $id);
        if (!$bean->id) {
            return new JSONResponse([
                'success' => false,
                'message' => 'Record not found.',
                'extra' => null,
            ], 404);
        }

        foreach ($filteredData as $key => $value) {
            $bean->$key = $value;
        }
        R::store($bean);

        return new JSONResponse([
            'success' => true,
            'message' => 'Record updated successfully.',
            'extra' => $bean,
        ]);
    }

    public function delete(int $id): JSONResponse {
        $bean = R::load($this->model, $id);
        if (!$bean->id) {
            return new JSONResponse([
                'success' => false,
                'message' => 'Record not found.',
                'extra' => null,
            ], 404);
        }

        R::trash($bean);

        return new JSONResponse([
            'success' => true,
            'message' => 'Record deleted successfully.',
            'extra' => null,
        ]);
    }
}
