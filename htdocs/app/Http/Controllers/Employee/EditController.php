<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use JamesMills\LaravelTimezone\Facades\Timezone;

class EditController extends Controller
{
    public function __invoke(Employee $employee)
    {
        $this->authorize('update', [$employee]);

        if (!empty($employee->filename_thumb)) {
            $employee->filename_thumb_uri = asset('storage/'.$employee->filename_thumb);
            $employee->has_photo = 1;
        } else {
            $employee->filename_thumb_uri = asset('img/user_default.jpg');
            $employee->has_photo = 0;
        }
        $employee->date_start_show = Timezone::convertToLocal(Carbon::CreateFromTimestamp($employee->timestamp_start),'d.m.Y');

        return view( 'employee.employee_create', compact('employee') );
    }

}
