<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => TaskStatusEnum::class
    ];

    // Связь: задача принадлежит пользователю
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
