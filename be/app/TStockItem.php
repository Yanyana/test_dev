<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TStockItem extends Model
{
    protected $table = 't_stock_item';

    protected $fillable = [
        'item_id',
        'qty'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps =true;

    public function item () {
        return $this->hasOne(MItem::class, 'id', 'item_id');
    }
}
