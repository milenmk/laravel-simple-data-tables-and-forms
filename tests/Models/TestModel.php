<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'is_active',
        'created_at',
        'updated_at',
        'category',
        'price',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
