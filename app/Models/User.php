<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    protected $primaryKey = 'user_id';

    protected $table = 'Users';

    protected $fillable = [
        'name', 'email', 'phone', 'username', 'userType', 'profilePicture', 'status', 'password','is_newUser','gender','identity_path'
    ];
    

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }
    public function notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id', 'user_id');
    }
    public function issue()
    {
        return $this->hasMany(Issue::class, 'user_id', 'user_id');
    }

    public function address()
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function customerAddress()
{
    return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
}

 

}
