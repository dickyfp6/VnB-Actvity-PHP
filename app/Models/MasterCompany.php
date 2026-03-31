<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCompany extends Model
{
    use SoftDeletes;
    protected $table = 'master_companies';
    protected $fillable = ['name'];
}
