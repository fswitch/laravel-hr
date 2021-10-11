<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use App\Http\Requests\Employee\UpdateRequest;
use App\Models\Employee;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class UpdateController extends Controller
{
    public function __invoke(Employee $employee, UpdateRequest $request)
    {

        $this->authorize( 'update', [ Employee::class, $employee ] );

        $data = $request->validated();
        $data['admin_updated_id'] = auth()->user()->id;
        $data['timestamp_start'] = Timezone::convertFromLocal($data['date_start'])->timestamp;

        if (!empty($data['photo_delete']) && $data['photo_delete'] == 1 && !empty($employee->filename)){
            Storage::disk('public')->delete([
                $employee->filename,
                $employee->filename_thumb,
            ]);
            $employee->filename = '';
            $employee->filename_thumb = '';
        }

        unset($data['date_start'], $data['photo'], $data['photo_delete']);

        foreach ($data as $key => $val){
            $employee->$key = $val;
        }
        $employee->save();

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

            EmployeeController::EmployeeRecountSubs($employee->parent_id);
            EmployeeController::EmployeeRecountLevel($employee->id);

            session()->forget('photo_tmp');
            session()->forget('photo_orig');
        }

        return redirect(route('employees.index'));
    }

}
