<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $dates = ['start_date','end_date'];
    protected $fillable = ['product_name','discount_value','start_date','end_date'];

    public function advertisements(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Advertisement::class);
    }
}
