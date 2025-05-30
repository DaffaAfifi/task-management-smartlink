<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'deadline',
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function isFinished(string $type = 'on_time'): bool
    {
        if ($this->status !== 'Done') {
            return false;
        }

        $lastEndedAt = $this->taskSessions()
            ->whereNotNull('ended_at')
            ->latest('ended_at')
            ->value('ended_at');

        if (!$lastEndedAt) {
            return false;
        }

        return match ($type) {
            'on_time' => $lastEndedAt <= $this->deadline,
            'late'    => $lastEndedAt > $this->deadline,
            default   => false,
        };
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'Done' && now()->greaterThanOrEqualTo($this->deadline);
    }

    public function beginSession(): void
    {
        $this->update(['status' => 'In Progress']);

        $this->taskSessions()->create([
            'user_id' => Auth::user()->id,
            'started_at' => now(),
        ]);
    }

    public function pauseSession(): void
    {
        $last = $this->taskSessions()
            ->whereNull('ended_at')
            ->where('user_id', Auth::user()->id)
            ->latest()
            ->first();

        if (! $last || ! $last->started_at || $last->ended_at) {
            return;
        }

        $last->update([
            'ended_at' => now(),
            'duration_seconds' => Carbon::parse($last->started_at)->diffInSeconds(now()),
        ]);
    }

    public function finishTask(): void
    {
        $this->pauseSession();

        $this->update(['status' => 'Done']);
    }

    public function isSessionRunningForCurrentUser(): bool
    {
        return $this->taskSessions()
            ->where('user_id', Auth::user()->id)
            ->whereNull('ended_at')
            ->exists();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function taskSessions(): HasMany
    {
        return $this->hasMany(TaskSession::class);
    }
}
