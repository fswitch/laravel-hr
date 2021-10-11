<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public static function EmployeeRecountSubs($employee_id=0): bool
    {
        if (!is_numeric($employee_id) || $employee_id<1){
            return false;
        }

        $employee = Employee::find($employee_id);

        if (!$employee || $employee->id < 1){
            return false;
        }

        $count = Employee::where('parent_id',$employee->id)->count();

        $employee->subs = $count;
        $employee->save();

        return true;
    }

    public static function EmployeeRecountLevel($employee_id,$employee_id_last=0,$level=0)
    {
        $employee = Employee::find($employee_id);
        if ($employee_id_last < 1) {
            $employee_id_last = $employee_id;
        }
        $l = $level;
        if (!empty($employee->parent_id)){
            $level++;
            return self::EmployeeRecountLevel($employee->parent_id,$employee_id_last,$level);
        }

        if ($level < 1){
            Employee::where('id',$employee_id_last)->update([
                'level' => NULL,
                'topmanager' => 1
            ]);
        } else {
            Employee::where('id',$employee_id_last)->update([
                'level' => $level,
                'topmanager' => NULL
            ]);
        }

        return true;
    }


}
