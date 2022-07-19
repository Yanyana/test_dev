<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TRequestItem extends Model
{
    protected $table = 't_request_item';
    ## table ini bisa di normalisasi lagi menjadi header(data peminta) dan child(detail)
    ## supaya saya cepat mengerjakannya saya buat denormalisasi
    protected $fillable = [
        'item_id',
        'user_id',
        'name',
        'location',
        'unit',
        'qty_req',
        'note',
        'request_date',
        'req_number'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps =true;

    public function stock_item () {
        return $this->hasOne(TStockItem::class, 'item_id', 'item_id');
    }

    public function user_by () {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
