<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VnbFrameworkItem extends Model
{
    protected $table = 'vnb_framework_items';

    protected $fillable = ['career_stage', 'behaviour', 'phase', 'integration_1', 'integration_2', 'integrations'];

    protected $casts = [
        'integrations' => 'array',
    ];

    public static array $careerStages = [
        'manage_self_non_staff' => 'Manage Self (Non-Staff)',
        'manage_self_staff'     => 'Manage Self (Staff dan Supervisor)',
        'manage_others'         => 'Manage Other (Manager)',
        'manage_managers'       => 'Manage Manager (Direktur)',
    ];

    public static array $behaviours = [
        'Empathy',
        'Speak with Data',
        'Collaborative',
        'Decisive',
        'Be Ambassador',
        'Integrity',
        'Innovation',
    ];
}
