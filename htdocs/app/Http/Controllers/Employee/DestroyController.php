<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Gate;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class DestroyController extends Controller
{
    public function __invoke(Employee $employee)
    {
        if (!Gate::allows( 'delete', [ Employee::class, $employee ] )) {
            return response()->json([
                'success' => 0,
                'message' => 'You have no access',
                'btn1' => 1,
                'btn2' => 0,
                'btn1_title' => 'Close',
                'btn2_title' => ''
            ], 200);
        }

        $ifparent = Employee::where('parent_id', $employee->id)->count();

        if ($ifparent>0){
            return response()->json([
                'success' => 0,
                'message' => $employee->full_name.' has employees.'
                                .' You should <a href="'.route('employees.index',['parent_id'=>$employee->id]).'">move</a> them to another person.',
                'btn1' => 1,
                'btn2' => 0,
                'btn1_title' => 'Close',
                'btn2_title' => ''
            ], 200);
        }

        if (!empty($employee->filename)){
            Storage::disk('public')->delete([
                $employee->filename,
                $employee->filename_thumb,
            ]);
        }

        $employee_parent_id = $employee->parent_id ?? 0;
        $employee->delete();

        if ($employee_parent_id > 0){
            EmployeeController::EmployeeRecountSubs($employee_parent_id);
        }

        return response()->json([
            'success' => 1,
            'message' => 'Employee successfully deleted',
            'btn1' => 1,
            'btn2' => 1,
            'btn1_title' => 'Close',
            'btn2_title' => 'Delete'
        ], 200);
    }

}
