<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Support\Facades\Gate;

class FindController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Gate::allows( 'viewAny', [ Position::class ] ) || !$request->ajax()) {
            return view('error',[ 'error_message'=>'You have no access' ]);
        }

        $positions = [];
        if (!empty($request->id)){
            $positions = [Position::find($request->id)];
        } elseif (!empty($request->find)) {
            $positions = Position::where('title', 'like', '%'.$request->find.'%')->limit(10)->get();
        }

        $arr = [];
        foreach ($positions as $position) {
            $arr[] = array(
                'value' => $position->id,    // Required.
                'text' => $position->title,     // If not set, it will use the value as the text.
                'class' => '',    // The CSS class(es) to apply to the option element.
                'disabled' => false,     // {Boolean} true|false
            );
        }

        return $arr;
    }

}
