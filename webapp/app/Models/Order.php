<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'compra';

    protected $fillable = [
        'id',
        'timestamp',
        'total',
        'sub_total',
        'descricao',
        'id_utilizador',
        'estado',
        'id_administrador',
        'promo_code',
        'first_name',
        'last_name',
        'email',
        'morada',
        'porta',
        'andar',
        'codigo_postal',
        'localidade',
        'telefone',
        'nif',
        'pais',
        'metodo_pagamento'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_utilizador');
    }

    public function normalizeOrderId(int $id)
    {
        $highestId = Order::max('id');
        $highestIdLength = strlen((string) $highestId);
        $id = (string) $id;
        $idLength = strlen((string) $id);
        $id = str_pad($id, $highestIdLength + 1 - $idLength, '0', STR_PAD_LEFT);
        return $id;
    }

}