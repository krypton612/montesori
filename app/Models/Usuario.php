<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use SoftDeletes, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function persona()
    {
        return $this->hasOne(Persona::class, 'usuario_id');
    }
    
    public function canAccessPanel(Panel $panel): bool
    {

        // en vez de validar por dominio del email, validar por permisos o roles del usuario
        // esto permitira a un usuario tener acceso a multiples paneles si tiene los permisos o roles adecuados

        if ($panel->getId() === 'informatica') {
            return $this->hasPermissionTo('AccessAdminPanel');
        }

        if ($panel->getId() === 'profesor') {
            return $this->hasPermissionTo('AccessProfesorPanel');
        }

        if ($panel->getId() === 'finanzas') {
            return $this->hasPermissionTo('AccessFinanzasPanel');
        }

        if ($panel->getId() === 'inscripcion') {
            return $this->hasPermissionTo('AccessInscripcionPanel');
        }

        return true;
    }
}
