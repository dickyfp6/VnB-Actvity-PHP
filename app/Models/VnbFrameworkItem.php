<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VnbFrameworkItem extends Model
{
    protected $table = 'vnb_framework_items';

    protected $fillable = ['career_stage', 'behaviour', 'phase', 'integration_1', 'integration_2'];

    public static array $careerStages = [
        'manage_self_non_staff' => 'Manage Self (Non-Staff)',
        'manage_self_staff'     => 'Manage Self (Staff)',
        'manage_others'         => 'Manage Others',
        'manage_managers'       => 'Manage Managers',
        'manage_function'       => 'Manage Function',
    ];

    public static array $behaviours = [
        'Empathy',
        'Be A Wismilak Ambassador',
        'Effective & Efficient',
        'Speak with Data',
        'Collaborative',
        'Decisive',
        'Open Mind',
    ];

    public static array $phases = [
        '1-3' => '1-3 Bulan',
        '4-6' => '4-6 Bulan',
        '6+'  => '>6 Bulan',
    ];
}
