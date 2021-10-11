<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\MoveRequest;
use App\Http\Controllers\EmployeeController;
use App\Models\Employee;

use DB;

class MoveController extends Controller
{
    public function __invoke(Employee $employee, MoveRequest $request)
    {
        $this->authorize( 'update', [ Employee::class, $employee ] );
        $data = $request->validated();

        $employee = Employee::where('id',$employee->id)->with('employees')->get()->first();
        $employee_new = Employee::where('id',$data['parent_id'])->with('employees')->first();
        foreach ($employee->employees as $imp){
            $imp->manager()->associate($employee_new);
            $imp->admin_updated_id = auth()->user()->id;
            $imp->save();
            EmployeeController::EmployeeRecountLevel($imp->id);
        }

        $employee->admin_updated_id = auth()->user()->id;
        $employee->save();

        EmployeeController::EmployeeRecountSubs($employee->id);
        EmployeeController::EmployeeRecountSubs($employee_new->id);

        return redirect(route('employees.index',[ 'parent_id' => $employee->id ]));
    }

}
