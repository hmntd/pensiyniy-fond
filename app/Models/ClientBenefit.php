<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientBenefit extends Model
{
    protected $table = 'client_benefits';

    protected $fillable = [
        'client_id',
        'benefit_id',
        'active',
    ];
}
