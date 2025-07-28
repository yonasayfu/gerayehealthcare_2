<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MarketingCampaign;
use App\Models\Staff;
use App\Models\CampaignContent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class MarketingTask extends Model
{
    use HasFactory;
    protected static function booted()
    {
        static::creating(function ($task) {
            if (empty($task->task_code)) {
                DB::transaction(function () use ($task) {
                    $latestTask = static::lockForUpdate()
                                            ->whereNotNull('task_code')
                                            ->orderBy('id', 'desc')
                                            ->first();

                    $nextNumber = 1;
                    if ($latestTask && preg_match('/TASK-(\d+)/', $latestTask->task_code, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    }

                    $task->task_code = 'TASK-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                });
            }
        });
    }

    protected $fillable = [
        'task_code',
        'campaign_id',
        'assigned_to_staff_id',
        'related_content_id',
        'doctor_id',
        'task_type',
        'title',
        'description',
        'scheduled_at',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class);
    }

    public function assignedToStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_to_staff_id');
    }

    public function relatedContent()
    {
        return $this->belongsTo(CampaignContent::class, 'related_content_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }
}
