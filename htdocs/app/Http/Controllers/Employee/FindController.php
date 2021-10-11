<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;

class FindController extends Controller
{
    public function __invoke(Request $request)
    {
        if ( !Gate::allows( 'viewAny', [ Employee::class ] ) || !$request->ajax() ) {
            return view('error',[ 'error_message'=>'You have no access' ]);
        }

        $employees = [];
        if (!empty($request->id)){
            $employees = [
                Employee::find($request->id)
            ];
        } elseif (!empty($request->find)) {
            $employees = Employee::where('full_name', 'like', $request->find.'%')->limit(10)->get();
        }

        $arr = [ 'results' => [] ];
        foreach ( $employees as $employee ){
            $arr['results'][] = array(
                'id' => $employee->id,
                'text' => $employee->full_name
            );
        }

        return $arr;
    }

}
