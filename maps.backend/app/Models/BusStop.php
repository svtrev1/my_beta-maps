<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusStop extends Model
{
    protected $table = 'busstation';  

    protected $primaryKey = 'ogc_fid';
    protected $keyType = 'int'; 

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'id', 'name', 'street', 'type', 'affilation', 'comments', 'replace', 
        'financing', 'year', 'area', 'topkod', 'numbus', 'numtaxi', 'numsezon', 
        'inform_typ', 'comfort_ty', 'wkb_geometry'
    ];

    public function setGeometry($longitude, $latitude)
    {
        $this->attributes['wkb_geometry'] = DB::raw("ST_SetSRID(ST_Point($longitude, $latitude), 4326)");
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'busstation_id', 'ogc_fid');
    }

}

