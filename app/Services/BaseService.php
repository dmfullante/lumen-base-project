<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BaseService
{
    protected $model;
    
    public function updateRecord($repository, $data, $id)
    {
        return $repository->update($data, $id);
    }

    public function filterQueryString($request, $query)
    {
        // Get the query string keys
        $queryKeys = array_keys($request->query());
        // Get the fillable attributes from the model
        $fillableAttributes = $this->getModel()->getFillable();
        // Check for any intersection between query keys and fillable attributes
        $commonKeys = array_intersect($queryKeys, $fillableAttributes);
        // If there are any common keys, do something
        if (!empty($commonKeys)) {
            // Common keys found in both query string and fillable attributes
            foreach ($commonKeys as $key) {
                if (is_array($request->$key)) {
                    $query = $query->whereIn($key, $request->$key);
                } else {
                    $query = $query->where($key, $request->$key);
                }
            }
        }
        return $query;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWhere($where)
    {
        return $this->model->where($where)->get();
    }
    public function findFirst($where)
    {
        return $this->model->where($where)->first();
    }

    public function list($request = null)
    {
        return $this->listQuery($request);
    }
    public function listQuery($request = null)
    {
        if (is_array($request->search)) {
            $searchTerm = $request->search['value'];
        } else {
            $searchTerm = $request->search;
        }
        if ($request) {
            if ($request->has('search')) {
                return  $this->model->search($searchTerm);
            }
        }
        return $this->model->query();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function createData($data)
    {
        $model =  new $this->model;
        $model->fill($data);
        if ($model->save()) {
            return $model;
        };
        return false;
    }

    public function updateData($data, $where)
    {
        $model =  $this->model->where($where)->first();
        $model->fill($data);
        if ($model->isDirty()) {
            $model->save(); // saves only if something changed
        }
        return $model;
    }

    public function update($data, $where)
    {
        return $this->model->where($where)->update($data);
    }

    public function delete($where)
    {
        return $this->model->where($where)->delete();
    }

    public function getCachedData()
    {
        $dayInSec = 86400;
        $cachedTableName = class_basename(get_class($this->getModel()))."_cached";
        Log::info("cachedTableName:$cachedTableName");
        return Cache::remember($cachedTableName, $dayInSec, function () {
            return $this->getModel()->all();
        });
    }
    public function resetCachedData()
    {
        $cachedTableName = class_basename(get_class($this->getModel()))."_cached";
        Log::info("cachedTableName:$cachedTableName");
        Cache::forget($cachedTableName);
    }
}
