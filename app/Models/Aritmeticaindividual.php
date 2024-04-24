<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aritmeticaindividual extends Model
{
    use HasFactory;
    protected $fillable = [
        'resposta',
        'percentil',
        'interpretação',
        'aluno_id'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

}
