<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MDepartement extends Model
{
    protected $table = 'm_departement';

    protected $fillable = [
        'name'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps =true;
}
