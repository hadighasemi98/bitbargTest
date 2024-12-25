<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'due_date', 'user_id'];

    public const STATUS = [
        'pending' => 0,
        'completed' => 1,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
