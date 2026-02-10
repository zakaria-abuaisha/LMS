<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter {
    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) 
        {
            if (method_exists($this, $key))
            {
                $this->$key($value);
            }
        }

        return $builder;
    }

    public function include($value)
    {
        return $this->builder->with(explode(",", $value));
    }

    protected function filter($arr)
    {
        foreach ($arr as $key => $value)
        {
            if(method_exists($this, $key))
            {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function sort($value)
    {
        $sortAttributes = explode(",", $value);
        $model = $this->builder->getModel();
        $baseTable = $model->getTable();
        $this->joined = $this->joined ?? [];

        foreach ($sortAttributes as $attribute)
        {
            $direction = 'asc';

            if (str_starts_with($attribute, "-"))
            {
                $direction = "desc";
                $attribute = ltrim($attribute,"-");
            }

            if (!in_array($attribute, $this->sortable))
            {
                continue;
            }

            $mapping = $this->sortable[$attribute];

            // Self means in the base table
            if ($mapping === "self")
            {
                // Sort using the base table
                $this->builder->orderBy($attribute, $direction);
            }
            else 
            {
                // Format: ' :foreignKey,ownerKey'
                [$relatedTable, $keys] = explode(":", $mapping);
                [$foreignKey, $ownerKey] = explode(",", $keys);

                // Perform Join if not already done
                if (!in_array($relatedTable, $this->joined))
                {
                    $this->builder->join($relatedTable, "$baseTable.$foreignKey", "=", "$relatedTable.$ownerKey");
                    $this->joined[] = $relatedTable;
                }

                $this->builder->orderBy("$relatedTable.$attribute", $direction);
            }
        }
    }
}