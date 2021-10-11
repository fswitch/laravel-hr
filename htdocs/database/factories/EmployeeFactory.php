<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker_ua = \Faker\Factory::create('uk_UA');
        sleep(mt_rand(0,2));
        return [
            'position_id' => Position::inRandomOrder()->first()->id, // random position id from positions
            'full_name' => $this->faker->firstName().' '.$this->faker->lastName, // first name + last name
            'timestamp_start' => $this->faker->numberBetween((time()-31536000),time()), // random timestamp for last year
            'phone' => $faker_ua->unique()->e164PhoneNumber, // random valid phone number, ukrainian format
            'email' => $this->faker->unique()->email,
            'salary' => ceil($this->faker->numberBetween(10,500) / 10) * 10,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Employee $employee) {
            if (mt_rand(1,10) == 6)
            {
                $filepath = 'employee/'.((ceil(($employee->id+1)/1000)*1000)-1000).'/'.$employee->id.'.jpg';
                $filethumbpath = 'employee/'.((ceil(($employee->id+1)/1000)*1000)-1000).'/'.$employee->id.'_thumb.jpg';
                try {
                    $file_get_contents = file_get_contents('https://thispersondoesnotexist.com/image');
                    if ($file_get_contents)
                    {
                        Storage::disk('public')->put($filepath, $file_get_contents);
                        if (Storage::disk('public')->exists($filepath))
                        {
                            $thumb = Image::make(Storage::disk('public')->path($filepath));
                            $thumb->resize(300,300);
                            $thumb->save(Storage::disk('public')->path($filethumbpath));
                            $employee->filename = $filepath;
                            $employee->filename_thumb = $filethumbpath;
                            $employee->save();
                        }
                    }
                } catch (\Exception $e) {
                    dump($e);
                }
            }
        });
    }


    public function _Hierarchy()
    {
        //DB::update('update employees set topmanager=null, parent_id=null, level=null, subs=null');

        $topmanager = Employee::where('topmanager',NULL)->where('parent_id',NULL)->inRandomOrder()->first();
        if ($topmanager && $topmanager->id > 0){
            $topmanager->topmanager = 1;
            $topmanager->save();
            $this->_HierarchyGenerate(Employee::find($topmanager->id));
        }

        return;
    }

    private function _HierarchyGenerate(Employee $topmanager)
    {
        $this_iter_ids = [];
        $topmanager_subs = 0;

        $managers_1 = Employee::where('topmanager',NULL)
                        ->where('level',NULL)
                        ->where('subs',NULL)
                        ->inRandomOrder()
                        ->limit(mt_rand(3,10))
                        ->get();
        foreach ($managers_1 as $manager_1){
            $this_iter_ids[] = $manager_1->id;
        }
        foreach ($managers_1 as $manager_1)
        {
            $manager_1->parent_id = $topmanager->id;
            $manager_1->level = 1;
            $manager_1_subs = 0;

            $managers_2 = Employee::where('topmanager',NULL)
                            ->where('level',NULL)
                            ->where('subs',NULL)
                            ->whereNotIn('id',$this_iter_ids)
                            ->inRandomOrder()
                            ->limit(mt_rand(3,10))->get();
            foreach ($managers_2 as $manager_2){
                $this_iter_ids[] = $manager_2->id;
            }
            foreach ($managers_2 as $manager_2)
            {
                $manager_2->parent_id = $manager_1->id;
                $manager_2->level = 2;
                $manager_2_subs = 0;

                $managers_3 = Employee::where('topmanager',NULL)
                                ->where('level',NULL)
                                ->where('subs',NULL)
                                ->whereNotIn('id',$this_iter_ids)
                                ->inRandomOrder()
                                ->limit(mt_rand(3,10))
                                ->get();
                foreach ($managers_3 as $manager_3){
                    $this_iter_ids[] = $manager_3->id;
                }
                foreach ($managers_3 as $manager_3)
                {
                    $manager_3->parent_id = $manager_2->id;
                    $manager_3->level = 3;
                    $manager_3_subs = 0;

                    $managers_4 = Employee::where('topmanager',NULL)
                                    ->where('level',NULL)
                                    ->where('subs',NULL)
                                    ->whereNotIn('id',$this_iter_ids)
                                    ->inRandomOrder()
                                    ->limit(mt_rand(3,10))
                                    ->get();
                    foreach ($managers_4 as $manager_4)
                    {
                        $manager_4->parent_id = $manager_3->id;
                        $manager_4->level = 4;
                        $manager_4->save();
                        $manager_3_subs++;
                    }
                    $manager_3->subs = $manager_3_subs;
                    $manager_3->save();
                    $manager_2_subs++;
                }
                $manager_2->subs = $manager_2_subs;
                $manager_2->save();
                $manager_1_subs++;
            }

            $manager_1->subs = $manager_1_subs;
            $manager_1->save();

            $topmanager_subs++;
        }

        $topmanager->subs = $topmanager_subs;
        $topmanager->save();
        $this->_Hierarchy();

        return;
    }
}
