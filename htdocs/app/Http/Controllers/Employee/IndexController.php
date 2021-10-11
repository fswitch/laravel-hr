<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;

use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {


        if (!Gate::allows( 'viewAny', [ Employee::class ] )) {
            if ($request->ajax()){
                return [
                    'success'=>0,
                    'payload'=> [
                        'message'=>'You have no access'
                    ]
                ];
            } else {
                return view('error',[ 'error_message'=>'You have no access' ]);
            }
        }

        if ($request->ajax())
        {
            $length = (int)($request->input('length')) ?? 10;
            $start = (int)($request->input('start')) ?? 0;
            $search = $request->input('search');
            $order = $request->input('order');
            $request->merge(['search'=>'','order'=>'']);
            $columns = $request->input('columns');

            $query = Employee::query();
            $query->when($request->input('parent_id')>0,function($q) use ($request){
                return $q->where('parent_id',$request->input('parent_id'));
            })
                ->when(!empty($search['value']), function($q) use ($search,$columns) {
                    $q->where(function($q1) use ($search, $columns){
                        foreach ($columns as $column)
                        {
                            if (isset($column['searchable']) && $column['searchable'] == 'true'){
                                $q1->orWhere($column['data'],'like','%'.$search['value'].'%');
                            }
                        }
                        return $q1;
                    });
                    return $q;
                })
                ->when(isset($order[0]) && !empty($order[0]['column']), function($q) use ($order, $columns){

                    if ($columns[$order[0]['column']]['name'] == 'position.title'){
                        $q->join('positions','positions.id','=','position_id');
                        $columns[$order[0]['column']]['name'] = 'positions.title';
                    }

                    return $q->orderBy($columns[$order[0]['column']]['name'],$order[0]['dir']);
                })
                ->with('position');

            $total = $query->count();
            $query->offset($start)
                ->limit($length);
            $data = $query->get();

            foreach ($data as $key => $val)
            {
                if (empty($val->position)){
                    $data[$key]->setRelation('position',Position::make()->setRawAttributes(['id'=>0,'title'=>'']));
                }
                $data[$key]['date_start_show'] = Timezone::convertToLocal(Carbon::createFromTimestamp($val['timestamp_start']),'d.m.Y');
                $data[$key]['filename_uri'] = (!empty($val['filename'])) ? asset('storage/'.$val['filename']) : asset('img/user_default.jpg');
                $data[$key]['filename_thumb_uri'] = (!empty($val['filename_thumb'])) ? asset('storage/'.$val['filename_thumb']) : asset('img/user_default.jpg');
            }

            $test = Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn  = '';
                    if ($row->subs > 0) {
                        $btn .= '<a href="'.route('employees.index', ['parent_id'=>$row->id]).'"><i class="edit fas fa-users ml-3"></i></a> ';
                    }
                    $btn .= '<a href="'.route('employee.edit',$row->id).'"><i class="edit fas fa-pencil-alt ml-3"></i></a> ';
                    $btn .= '<a href="#" class="deleteEmployee" data-employee="'.$row->id.'"><i class="delete far fa-trash-alt ml-3" id="deleteEmployeeFA-'.$row->id.'"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->skipPaging()
                ->setFilteredRecords($total)
                ->setTotalRecords($total)
                ->make(true);

            return $test;
        }

        $arr = [
            'parent_id' => 0
        ];
        if ($request->input('parent_id')>0){
            $arr['parent_id'] = $request->input('parent_id');
            $arr['parent'] = Employee::find($arr['parent_id']);
        }

        return view('employee.employees', $arr);
    }

}
