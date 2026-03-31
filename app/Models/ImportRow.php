<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportRow extends Model
{
    protected $fillable = ['import_id', 'row_number', 'data', 'error_message', 'status'];
    protected $casts = ['data' => 'array'];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
