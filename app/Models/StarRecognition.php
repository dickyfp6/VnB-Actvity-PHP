<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $manager_id
 * @property int $employee_id
 * @property string|null $draft_group
 * @property string $activity_name
 * @property \Illuminate\Support\Carbon $activity_date
 * @property string $organizer
 * @property string|null $certificate_path
 * @property string|null $certificate_original_name
 * @property string|null $activity_documentation_path
 * @property string|null $activity_documentation_original_name
 * @property string|null $activity_documentation
 * @property string $status
 * @property string|null $rejection_reason
 * @property string|null $approval_notes
 * @property float|null $total_points
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $manager
 * @property-read \App\Models\Employee $employee
 * @property-read StarRecognitionResponse[] $responses
 */
class StarRecognition extends Model
{
    protected $fillable = [
        'manager_id',
        'employee_id',
        'draft_group',
        'activity_name',
        'activity_date',
        'organizer',
        'certificate_path',
        'certificate_original_name',
        'activity_documentation',
        'activity_documentation_path',
        'activity_documentation_original_name',
        'status',
        'rejection_reason',
        'approval_notes',
        'total_points',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(StarRecognitionResponse::class, 'star_recognition_id');
    }

    /**
     * Check if recognition is complete (tahap 1 + tahap 2 filled)
     */
    public function isComplete(): bool
    {
        // Check tahap 1: has activity details
        if (!$this->activity_name || !$this->activity_date || !$this->organizer) {
            return false;
        }

        // Check tahap 2: has responses for all schema indicators
        $schema = StarSchema::where('is_active', true)->latest('id')->first();
        if (!$schema) {
            return false;
        }

        $indicatorCount = $schema->indicators()->count();
        $responseCount = $this->responses()->count();

        return $responseCount === $indicatorCount;
    }

    /**
     * Get draft status indicator
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
