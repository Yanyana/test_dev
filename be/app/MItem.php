<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MItem extends Model
{
    protected $table = 'm_item';

    protected $fillable = [
        'name',
        'location',
        'unit'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps =true;

    public function stock_item () {
        return $this->hasMany(TStockItem::class, 'item_id', 'id');
    }
}
