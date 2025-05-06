<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'payment_account',
        'uploaded_passport',
        'uploaded_work_book',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    public function pensions(): HasMany
    {
        return $this->hasMany(Pension::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'client_benefits');
    }
}
