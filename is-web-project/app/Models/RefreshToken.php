<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{

    protected $primaryKey = 'token';


    public $incrementing = false;


    protected $keyType = 'string';


    protected $table = 'refresh_tokens';


    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
        'revoked',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
