<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // User role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EDITOR = 'editor';
    const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the profile image URL.
     *
     * @return string|null
     */
    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }
        
        return asset('storage/' . $this->profile_image);
    }

    /**
     * Check if user has admin role.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user has manager role.
     *
     * @return bool
     */
    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Check if user has editor role.
     *
     * @return bool
     */
    public function isEditor()
    {
        return $this->role === self::ROLE_EDITOR;
    }

    /**
     * Check if user has customer role.
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if user can access admin panel.
     *
     * @return bool
     */
    public function canAccessAdminPanel()
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_EDITOR,
        ]);
    }

    /**
     * Get the user's role label.
     *
     * @return string
     */
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_CUSTOMER => 'Customer',
            default => 'Unknown Role',
        };
    }

    /**
     * Get the user's role badge HTML.
     *
     * @return string
     */
    public function getRoleBadgeAttribute()
    {
        $class = match($this->role) {
            self::ROLE_ADMIN => 'bg-danger',
            self::ROLE_MANAGER => 'bg-warning',
            self::ROLE_EDITOR => 'bg-info',
            self::ROLE_CUSTOMER => 'bg-success',
            default => 'bg-secondary',
        };

        return '<span class="badge ' . $class . '">' . $this->role_label . '</span>';
    }

    /**
     * Get user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get user's comparison lists.
     */
    public function comparisonLists()
    {
        return $this->hasMany(ComparisonList::class);
    }

    /**
     * Get the contact messages for the user.
     */
    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }

    /**
     * Check if user has staff permissions
     * 
     * @return bool
     */
    public function isStaff()
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_EDITOR
        ]) || $this->is_admin;
    }

    /**
     * Check if user has a specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        return in_array($this->role, $roles);
    }
}
