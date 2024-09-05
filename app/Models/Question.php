<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_question';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'kategori',
        'deskripsi',
        'gambar',
    ];

    // Relasi dengan tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    // Relasi dengan tabel questions
    public function answer(): HasMany
    {
        return $this->HasMany(Answer::class, 'id_question', 'id_question');
    }

    // Relasi dengan tabel vote
    public function vote(): HasMany
    {
        return $this->HasMany(Vote::class, 'id_question', 'id_question');
    }

    // Relasi dengan tabel report
    public function report(): HasMany
    {
        return $this->HasMany(Report::class, 'id_question', 'id_question');
    }
}
