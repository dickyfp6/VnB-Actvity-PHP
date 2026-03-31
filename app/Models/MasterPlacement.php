<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPlacement extends Model
{
    use SoftDeletes;
    protected $table = 'master_placements';
    protected $fillable = ['name'];
}
