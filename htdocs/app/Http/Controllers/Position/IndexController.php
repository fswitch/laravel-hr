<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Gate::allows( 'viewAny', [ Position::class ] )) {
            return view('error',[ 'error_message'=>'You have no access' ]);
        }

        if ($request->ajax()) {
            $data = Position::all();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                            $btn  = '<a href="'.route('position.moveemployees',$row->id).'"><i class="fas fa-running"></i></a>';
                            $btn .= '<a href="'.route('position.edit',$row->id).'"><i class="edit fas fa-pencil-alt ml-3"></i></a> ';
                            $btn .= '<a href="#" class="deletePosition" data-position="'.$row->id.'"><i class="far fa-trash-alt ml-3" id="deletePositionFA-'.$row->id.'"></i></a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('position.positions');
    }

}
