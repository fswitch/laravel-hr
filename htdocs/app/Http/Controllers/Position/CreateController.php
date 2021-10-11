<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use App\Models\Position;

class CreateController extends Controller
{
    public function __invoke()
    {
        $this->authorize( 'create', [ Position::class ] );

        return view('position.position_create');
    }

}
