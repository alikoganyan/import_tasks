<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'team_id',
        'unsubscribed_status',
        'first_name',
        'last_name',
        'phone',
        'email',
        'sticky_phone_number_id',
        'twitter_id',
        'fb_messenger_id',
        'time_zone'
    ];

    /**
     * get custom attributes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customAttributes()
    {
        return $this->hasMany(CustomAttributes::class, 'contact_id', 'id');
    }
}
