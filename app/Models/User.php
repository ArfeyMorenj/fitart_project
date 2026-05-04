<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable;

  public const ROLE_SUPER_ADMIN = 'super_admin';
  public const ROLE_FINANCE_ADMIN = 'finance_admin';
  public const ROLE_SALES_OPERATOR = 'sales_operator';
  public const ROLE_AR_COLLECTOR = 'ar_collector';
  public const ROLE_AUDITOR_VIEWER = 'auditor_viewer';
  public const ROLE_MANAGER = 'manager';

  public function isSuperAdmin(): bool { return $this->role === self::ROLE_SUPER_ADMIN; }

  public function hasAnyRole(array $roles): bool
  {
    return $this->isSuperAdmin() || in_array($this->role, $roles, true);
  }

  protected $fillable = ['username', 'name', 'email', 'password', 'role', 'is_active'];
  protected $hidden = ['password','remember_token'];

  protected function casts(): array
  {
    return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
  }
}
