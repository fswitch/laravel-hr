<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Position;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $fillable = [
        'position_id',
        'parent_id',
        'admin_created_id',
        'admin_updated_id',
        'full_name',
        'timestamp_start',
        'phone',
        'email',
        'salary',
        'filename',
        'filename_thumb'
    ];

    public function position()
    {
        return $this->hasOne(Position::class,'id','position_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class,'parent_id','id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class,'parent_id','id');
    }


    public static function SetPosition($setter = [])
    {
        $query = Employee::query();
        $query->when(isset($setter['employee']) && is_array($setter['employee']), function($q) use ($setter){
            return $q->whereIn('id',$setter['employee']);
        })
        ->when(isset($setter['employee']) && is_numeric($setter['employee']), function($q) use ($setter){
            return $q->where('id',$setter['employee']);
        })
        ->when(isset($setter['hasPosition']), function($q) use ($setter){
            return $q->where('position_id',$setter['hasPosition']);
        });
        $count = $query->update(['position_id'=>$setter['toPosition']]);

        return $count;
    }


}
