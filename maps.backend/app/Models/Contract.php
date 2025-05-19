<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'busstation_id',
        'file_path',
        'original_name',
        'mime_type',
        'size'
    ];

    public function busStop()
    {
        return $this->belongsTo(BusStop::class, 'busstation_id', 'ogc_fid');
    }
}
