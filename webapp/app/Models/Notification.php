<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    protected $table = 'notificacao';

    protected $fillable = [
        'id',
        'timestamp',
        'texto',
        'id_utilizador',
        'id_administrador',
        'link',
        'lida'
    ];
}
