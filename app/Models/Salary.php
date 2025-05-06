<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'client_salaries';

    protected $fillable = [
        'client_id',
        'company_name',
        'job_title',
        'salary',
        'date',
        'has_benefit',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'salary_benefits');
    }
}
