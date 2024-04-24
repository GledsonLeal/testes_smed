<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aritmeticacoletiva extends Model
{
    use HasFactory;
    protected $fillable = [
        'aritmetica',
        'percentil',
        'interpretação',
        'aluno_id'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

}
