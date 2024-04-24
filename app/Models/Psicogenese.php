<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psicogenese extends Model
{
    use HasFactory;
    protected $fillable = [
        'psicogenese',
        'aluno_id'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

}
