<?php

namespace App\Services\Admin\Industry;

use App\Models\Api\Admin\Industry;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class IndustryService extends BaseModelService
{
    use StoreMultiLang;

    protected string $modelClass = Industry::class;
    protected array $relations = ['products'];

    public function store()
    {
        $this->uploadSingleImage(['industry_image'], 'uploads/industries');
        $industry = parent::store($this->getBasicColumn(['industry_image', 'order']));
        $this->data['slug'] = $this->createSlug($this->data);
        $this->processTranslations($industry, $this->data, ['title', 'slug', 'small_des', 'des', 'alt_image', 'title_image', 'meta_title', 'meta_des']);

        return $industry;
    }

    public function update($id)
    {
        $this->uploadSingleImage(['industry_image'], 'uploads/industries');
        $industry = parent::update($id, $this->getBasicColumn(['industry_image', 'order']));
        $this->processTranslations($industry, $this->data, ['title', 'slug', 'small_des', 'des', 'alt_image', 'title_image', 'meta_title', 'meta_des']);

        return $industry;
    }

    public function applySearch(Builder $query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%")
              ->orWhereTranslationLike('slug', "%$search%");
        });
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }
}
