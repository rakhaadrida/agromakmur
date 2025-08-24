<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getTotalUnreadNotificationAttribute(): string
    {
        return $this->unreadNotifications()->count();
    }

    public function hasRole($roles): bool
    {
        if(is_array($roles)) {
            foreach($roles as $need_role){
                if($this->checkUserRole($need_role)) {
                    return true;
                }
            }
        } else {
            return $this->checkUserRole($roles);
        }
        return false;
    }

    private function checkUserRole($role): bool
    {
        return $role === $this->role;
    }
}
