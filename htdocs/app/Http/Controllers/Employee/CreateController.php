<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;

class CreateController extends Controller
{
    public function __invoke()
    {
        $this->authorize( 'create', [ Employee::class ] );

        $dirpath = storage_path('app').'/../tmp';
        if (!file_exists($dirpath)){
            mkdir($dirpath,0755);
            file_put_contents($dirpath.'/.gitignore','*');
        }

        $dir = opendir($dirpath);
        while ($file = readdir($dir)){
            if (strpos($file,'.') === 0){}
            elseif ( (filectime($dirpath.'/'.$file) + 10800) < time() ) { // delete files older than 3h
                unlink($dirpath.'/'.$file);
            }
        }

        $employee = new Employee;

        return view('employee.employee_create',array('employee'=>$employee));
    }

}
