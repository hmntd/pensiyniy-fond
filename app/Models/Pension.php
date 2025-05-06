<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pension extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'pension',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'client_id', 'client_id');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class, 'client_id', 'client_id');
    }
}
