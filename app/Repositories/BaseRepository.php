<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;

abstract class BaseRepository
{
    protected Model $model;

    abstract public function model(): string;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    public function all(array $relations = []): Collection
    {
        return $this->query()
            ->with($relations)
            ->get();
    }

    public function paginate(int $perPage = 15, array $relations = []): Paginator
    {
        return $this->query()
            ->with($relations)
            ->paginate($perPage);
    }

    public function find(int $id, array $relations = []): ?Model
    {
        return $this->query()
            ->with($relations)
            ->find($id);
    }

    public function findOrFail(int $id, array $relations = []): Model
    {
        return $this->query()
            ->with($relations)
            ->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
