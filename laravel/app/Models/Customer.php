<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'contract_number',
        'notify_owner',
        'telegram_chat_id',
        'notify_owner_tg',
    ];

    //protected $with = ['users', 'orders'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
