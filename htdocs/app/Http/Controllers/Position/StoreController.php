<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\StoreRequest;
use App\Models\Position;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $this->authorize( 'create', [ Position::class ] );

        $data = $request->validated();
        $data['admin_created_id'] = auth()->user()->id;

        Position::create($data);

        return redirect(route('positions.index'));
    }

}
