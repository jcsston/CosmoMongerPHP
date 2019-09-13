<?php

use Illuminate\Database\Seeder;

class CSVDataSeeder extends Seeder
{
    function arrayFromCSV($file, $hasHeader = false) {
        $result = [];
        $file = fopen($file, 'r');
        if ($hasHeader) {
            $keys = fgetcsv($file);
        }
        while ($row = fgetcsv($file)) {
            $n = count($row);
            $res=[];
            for($i = 0; $i < $n; $i++) {
                $idx = ($hasHeader) ? $keys[$i] : $i;
                $res[$idx] = $row[$i];
            }
            $result[] = $res;
        }
        fclose($file);
        return $result;
    }

    /**
     * Seed the database based on csv files
     *
     * @return void
     */
    public function run()
    {
        $csv_files = [
            'base_ship.csv',
            'good.csv',
            'jump_drive.csv',
            'npc.csv',
            'npc_name.csv',
            'race.csv',
            'shield.csv',
            'system.csv',
            'system_good.csv',
            'system_jump_drive_upgrade.csv',
            'system_shield_upgrade.csv',
            'system_ship.csv',
            'system_weapon_upgrade.csv',
            'weapon.csv',
        ];
        $seed_directory = dirname(__FILE__);

        foreach ($csv_files as $csv_file) {
            $table_name = substr($csv_file, 0, -4); // strip off .csv
            $data = $this->arrayFromCSV($seed_directory . '/' . $csv_file, true);
            DB::table($table_name)->delete();
            foreach ($data as $row) {
                DB::table($table_name)->insert($row);
            }
        }

        // $this->call(UsersTableSeeder::class);
    }
}
