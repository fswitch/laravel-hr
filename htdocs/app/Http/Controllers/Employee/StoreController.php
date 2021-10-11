<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use App\Http\Requests\Employee\StoreRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use JamesMills\LaravelTimezone\Facades\Timezone;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $this->authorize( 'create', [ Employee::class ] );

        $data = $request->validated();

        $data['admin_created_id'] = auth()->user()->id;

        $data['timestamp_start'] = Timezone::convertFromLocal($request->input('date_start'))->timestamp;

        $employee = Employee::create($data);

        if (!empty(session('photo_tmp')) && file_exists(storage_path('app').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.session('photo_tmp'))){
            $file_path_tmp = storage_path('app').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.session('photo_tmp');
            $file_ext_new = File::guessExtension($file_path_tmp);
            $file_name_new = $employee->id.'.'.$file_ext_new;
            $file_name_thumb_new = $employee->id.'_thumb.'.$file_ext_new;
            $storage_file_dir_path = 'employee'.DIRECTORY_SEPARATOR.((ceil(($employee->id+1)/1000)*1000)-1000);
            $file_path_new = storage_path('app/public').DIRECTORY_SEPARATOR.$storage_file_dir_path.DIRECTORY_SEPARATOR.$file_name_new;
            $file_path_thumb_new = storage_path('app/public').DIRECTORY_SEPARATOR.$storage_file_dir_path.DIRECTORY_SEPARATOR.$file_name_thumb_new;

            if (!Storage::exists($storage_file_dir_path)){
                Storage::makeDirectory($storage_file_dir_path);
            }
            File::move($file_path_tmp, $file_path_new);
            $thumb = Image::make($file_path_new)->orientate();
            $thumb->resize(300,300);
            $thumb->save($file_path_thumb_new);

            $employee->filename = $storage_file_dir_path.DIRECTORY_SEPARATOR.$file_name_new;
            $employee->filename_thumb = $storage_file_dir_path.DIRECTORY_SEPARATOR.$file_name_thumb_new;
            $employee->save();

            session()->forget('photo_tmp');
            session()->forget('photo_orig');
        }

        EmployeeController::EmployeeRecountLevel($employee->id);
        EmployeeController::EmployeeRecountSubs($employee->parent_id);

        return redirect(route('employees.index'));
    }

}
