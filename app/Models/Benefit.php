<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'coefficient',
        'value',
    ];

    public function salaries(): BelongsToMany
    {
        return $this->belongsToMany(Salary::class, 'salary_benefits');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_benefits');
    }
}
