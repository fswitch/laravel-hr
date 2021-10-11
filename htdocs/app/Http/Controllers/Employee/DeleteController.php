<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Employee;

class DeleteController extends Controller
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

        return response()->json([
            'success' => 1,
            'message' => 'Do you really want to delete employee "'.$employee->full_name.'"?',
            'btn1' => 1,
            'btn2' => 1,
            'btn1_title' => 'Close',
            'btn2_title' => 'Delete'
        ], 200);
    }

}
