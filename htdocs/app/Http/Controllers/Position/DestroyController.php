<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Position;

class DestroyController extends Controller
{
    public function __invoke(Position $position)
    {
        if (!Gate::allows( 'delete', [ Position::class, $position ] )) {
            return response()->json([
                'success' => 0,
                'message' => 'You have no access',
                'btn1' => 1,
                'btn2' => 0,
                'btn1_title' => 'Close',
                'btn2_title' => ''
            ], 200);
        }

        $positions = Position::where('id',$position->id)->with('employee')->get();
        if (sizeof($positions[0]->employee) > 0)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Some employees own this position. <a href="'.route('position.moveemployees',$position[0]->id).'">Move them.</a>',
                'btn1' => 1,
                'btn2' => 0,
                'btn1_title' => 'Close',
                'btn2_title' => ''
            ], 200);
        }

        $position->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Position successfully deleted',
            'btn1' => 1,
            'btn2' => 1,
            'btn1_title' => 'Close',
            'btn2_title' => 'Delete'
        ], 200);
    }

}
