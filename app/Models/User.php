<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi dengan tabel murid
    public function murid()
    {
        return $this->hasOne(Murid::class, 'username', 'username');
    }

    // Relasi dengan tabel ustaz
    public function ustaz()
    {
        return $this->hasOne(Ustaz::class, 'username', 'username');
    }

    // Relasi dengan tabel umum
    public function umum()
    {
        return $this->hasOne(Umum::class, 'username', 'username');
    }

    // Relasi dengan tabel questions
    public function question(): HasMany
    {
        return $this->HasMany(Question::class, 'username', 'username');
    }

    // Relasi dengan tabel answer
    public function answer(): HasMany
    {
        return $this->HasMany(Answer::class, 'username', 'username');
    }

    // Relasi dengan tabel answer
    public function reply(): HasMany
    {
        return $this->HasMany(Answer::class, 'username', 'replyTo');
    }

    // Relasi dengan tabel vote
    public function vote(): HasMany
    {
        return $this->HasMany(Vote::class, 'username', 'username');
    }

    public function followers()
    {
        return $this->HasMany(Follow::class, 'follower', 'username');
    }

    public function followings()
    {
        return $this->HasMany(Follow::class, 'following', 'username');
    }

    public function livechat()
    {
        return $this->hasOne(Livechat::class, 'username', 'username');
    }

    public function message()
    {
        return $this->HasMany(Message::class, 'username', 'username');
    }

    // Relasi dengan tabel report
    public function report(): HasMany
    {
        return $this->HasMany(Report::class, 'id_question', 'id_question');
    }
}
