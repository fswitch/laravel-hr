<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Employee;

class MoveemployeesController extends Controller
{
    public function __invoke(Position $position, Request $request)
    {
        if (!Gate::allows( 'viewAny', [ Position::class ] )) {
            return view('error',[ 'error_message'=>'You have no access' ]);
        }

        $message = '';
        if ($request->isMethod('POST')){
            $count = Employee::SetPosition([
                'employee' => '*',
                'hasPosition' => $position->id,
                'toPosition' => $request->input('position_id')
            ]);
            $message = 'Employees moved: '.$count;
        }

        return view('position.moveemployees', ['position'=>$position, 'message'=>$message]);
    }
}
