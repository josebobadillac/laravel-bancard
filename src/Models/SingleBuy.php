<?php 

namespace josebobadillac\Bancard\Models;

use Illuminate\Support\Str;

class SingleBuy extends BaseModel
{
    protected $table = 'bancard_single_buys';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'shop_process_id', 
        'amount', 
        'currency', 
        'additional_data', 
        'description', 
        'status', 
        'process_id', 
        'zimple',
        'pre_authorization',
        'return_url',
        'cancel_url',
    ];

    protected $casts = [
        'id' => 'string',
        'zimple' => 'boolean',
        'pre_authorization' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($singleBuy) {
            $singleBuy->id = Str::uuid();
        });
    }
}
