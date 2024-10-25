<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }


    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            return $query->where('status', $status);
        });
    }

    public function scopeFilterByDueDate($query, $dueDate)
    {
        return $query->when($dueDate, function ($query, $dueDate) {
            return $query->whereDate('due_date', $dueDate);
        });
    }

}
