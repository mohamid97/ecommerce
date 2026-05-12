<?php

namespace App\Services\Admin\Expense;

use App\Models\Api\Admin\Expense;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class ExpenseService extends BaseModelService
{
    use StoreMultiLang;

    protected string $modelClass = Expense::class;

    public function store()
    {
        $this->prepareData();

        $expense = parent::store($this->getBasicColumn(['type', 'amount', 'data']));
        $this->processTranslations($expense, $this->data, ['title']);

        return $expense;
    }

    public function update(int $id)
    {
        $this->prepareData();

        $expense = $this->modelClass::findOrFail($id);
        $expense->update($this->getBasicColumn(['type', 'amount', 'data']));
        $this->processTranslations($expense, $this->data, ['title']);

        return $expense;
    }

    public function applySearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('translations', function ($translation) use ($search) {
            $translation->where('title', 'like', '%' . $search . '%');
        });
    }

    public function type(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'desc'): Builder
    {
        return $query->orderBy($orderBy, $direction);
    }

    private function prepareData(): void
    {
        $this->data['type'] = $this->data['type'] ?? 'fixed';

        if (!in_array($this->data['type'], ['fixed', 'variable'], true)) {
            throw new \Exception(__('validation.in', ['attribute' => 'type']));
        }

        if (empty($this->data['title']) || !is_array($this->data['title'])) {
            throw new \Exception(__('validation.required', ['attribute' => 'title']));
        }

        if ($this->data['type'] === 'fixed' && !isset($this->data['amount'])) {
            throw new \Exception(__('validation.required', ['attribute' => 'amount']));
        }

        if ($this->data['type'] === 'variable') {
            $this->data['data'] = $this->normalizeVariableData($this->data['data'] ?? []);

            if (!isset($this->data['amount'])) {
                $this->data['amount'] = collect($this->data['data'])->sum('amount');
            }
        }

        $this->data['amount'] = (float) ($this->data['amount'] ?? 0);
    }

    private function normalizeVariableData(array $rows): array
    {
        return collect($rows)
            ->map(function ($row) {
                if (!is_array($row) || !isset($row['amount'])) {
                    throw new \Exception(__('validation.required', ['attribute' => 'data.amount']));
                }

                return [
                    'date' => $row['date'] ?? null,
                    'amount' => (float) $row['amount'],
                    'note' => $row['note'] ?? null,
                ];
            })
            ->values()
            ->all();
    }
}
