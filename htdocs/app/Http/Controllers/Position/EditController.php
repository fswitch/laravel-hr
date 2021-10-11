<?php

namespace App\Http\Controllers\Position;

use App\Http\Controllers\Controller;
use App\Models\Position;

class EditController extends Controller
{
    public function __invoke(Position $position)
    {
        $this->authorize('update', [$position]);

        return view( 'position.position_edit', compact('position') );
    }

}
