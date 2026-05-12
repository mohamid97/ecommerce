<?php

namespace App\Models\Api\Admin;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = ['type', 'amount', 'data'];

    public $translatedAttributes = ['title'];
    public $translationForeignKey = 'expense_id';
    public $translationModel = ExpenseTranslation::class;

    protected $casts = [
        'amount' => 'decimal:2',
        'data' => 'array',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
