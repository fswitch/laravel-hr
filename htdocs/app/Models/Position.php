<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $table = 'positions';
    public $fillable = [ 'title', 'admin_created_id', 'admin_updated_id' ];

    public function employee()
    {
        return $this->hasMany(Employee::class,'position_id','id');
    }
}
