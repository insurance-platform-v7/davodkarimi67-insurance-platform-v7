<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteDocument extends Model
{
    protected $fillable = [

        'type',
        'path',
      ' quote_request_id'
    ];




    public function documents()
    {
        return $this->hasMany(QuoteDocument::class);
    }

}
