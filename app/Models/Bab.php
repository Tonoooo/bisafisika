<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bab extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    //  relasi jika diperlukan di masa depan, misalnya:
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
