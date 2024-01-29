<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Notification;
use App\Models\Purchase;

use App\Http\Controllers\FileController;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps = false;
    public $isAdmin = true;

    public $validTokens = [];

    protected $table = 'utilizador';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'telefone',
        'morada',
        'codigo_postal',
        'localidade',
        'profile_image',
        'banned',
        'became_admin',
        'deleted'
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
    ];

    public function getNumberOfUreadNotifications()
    {
        return Notification::where('id_utilizador', $this->id)->where('lida', false)->count();
    }

    public function getProfileImage()
    {
        return FileController::get('profile', $this->id);
    }

    public function hasPhoto(): bool
    {
        return ($this->profile_image !== null && $this->profile_image !== '');
    }

    public function totalOrders(): int
    {
        return $this->hasMany('App\Models\Order', 'id_utilizador')->count();
    }

    public function totalReviews(): int
    {
        return $this->hasMany('App\Models\Review', 'id_utilizador')->count();
    }

    public function orders(): HasMany
    {
        return $this->hasMany('App\Models\Order', 'id_utilizador');
    }

    public function ban(): void
    {
        $this->banned = true;
        $this->save();
    }

    public function unban(): void
    {
        $this->banned = false;
        $this->save();
    }

    public function hasPendingOrders(): bool
    {
        $pending = Purchase::where('id_utilizador', $this->id)
            ->where('estado', '!=', 'Entregue')
            ->where('estado', '!=', 'Cancelada')
            ->count();

        return $pending > 0;
    }

    public function getNormalizeOrderId(int $id)
    {

        $highestId = Order::max('id');
        $highestIdLength = strlen((string) $highestId);
        $id = (string) $id;
        $id = str_pad($id, ($highestIdLength + 1), '0', STR_PAD_LEFT);
        return $id;

    }

    public function getNormalizeUserId(int $id)
    {

        $highestId = User::max('id');
        $highestIdLength = strlen((string) $highestId);
        $id = (string) $id;
        $id = str_pad($id, ($highestIdLength + 1), '0', STR_PAD_LEFT);
        return $id;

    } 
}
