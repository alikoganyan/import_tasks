<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttributes extends Model
{
    protected $fillable = [
        'contact_id',
        'key',
        'value'
    ];

    /**
     * get contact
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contacts::class, 'contact_id', 'id');
    }
}
