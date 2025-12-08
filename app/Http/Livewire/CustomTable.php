<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CustomTable extends Component
{
    public $fields;
    public $displayFields;
    public $headers;
    public $table;

    public $searchTerm;

    protected $query;

    public $model;
    public $where;

    public $data;

    public function render()
    {
        return view('livewire.custom-table');
    }

    public function mount()
    {
        $this->buildQuery();
        $this->load();
    }

    private function buildQuery()
    {
        $query = $this->model::query()->limit(100)->latest() or DB::table($this->table)->limit(50);

        $joins = [];

        foreach ($this->fields as $v => $field) {
            $this->headers[] = $v;
            $this->displayFields[] = $field;

            $relation = '';

            if (is_array($field)) {
                $field = $field[0];
            }

            if (str_contains($field, '.')) {
                $with = explode(".", $field);

                $relation = join('.', array_slice($with, 0, count($with) - 1));
            }

            if (!empty($relation) && !in_array($relation,  $joins, true)) {
                $joins[] = $relation;
            }
        }

        if (!empty($this->where)) {
            foreach ($this->where as $w) {
                if (is_callable($w)) {
                    $query->where($w);
                }
            }
        }

        unset($this->where);
        $this->query = $query->with($joins);
    }

    public function load($query = null)
    {
        if ($query) {
            $this->data = $query->get();
            return;
        }
        $this->data = $this->query->get();
    }

    public function search()
    {
        if (empty($this->searchTerm)) {
            return;
        }

        $query = $this->query?->clone();

        foreach ($this->fields as $k => $field) {
            if (is_array($field)) {
                $field = $field[0];
            }

            $this->applyQuery($query, $field);
        }

        $this->load($query);
    }

    private function applyQuery(&$query, $field)
    {
        if (str_contains($field, '.')) {
            [$rel, $c] = explode('.', $field);

            $query->where($rel, function ($query) use ($c) {
                $query->where($c, 'ilike', "%{$this->searchTerm}%");
            });
        } else {
            $query->where($field, "like", "%{$this->searchTerm}%");
        }
    }
}
