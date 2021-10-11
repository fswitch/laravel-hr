<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\UpdateRequest;
use App\Models\Position;

class UpdateController extends Controller
{
    public function __invoke(Position $position, UpdateRequest $request)
    {
        $this->authorize( 'update', [ $position ] );
        $data = $request->validated();

        foreach ($data as $key => $val){
            $position->$key = $val;
        }
        $position->admin_updated_id = auth()->user()->id;
        $position->save();

        return redirect(route('positions.index'));
    }

}
