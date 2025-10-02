<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class precache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage:
     * php artisan precache:tables
     */
    protected $signature = 'precache:tables';

    /**
     * The console command description.
     */
    protected $description = 'Pre-cache frequently used database tables';

    /**
     * The list of tables to pre-cache.
     */
    protected array $tables = [
        "organization",
        "users",
        "user_role_association",
        "application",
        "application_device_association",
        "application_location_association",
        "application_sensor_type_association",
        "application_device_type_association",
        "device",
        "device_deployment",
        "device_sensor_association",
        "device_type",
        "location",
        "sub_location",
        "permission",
        "resource",
        "role",
        "role_permission_association",
        "sensor",
        "sensor_data_types",
        "sensor_data_types_association",
        "sensor_type",
        "software_component"
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info(now());
        $this->info("Starting table pre-cache...");

        try {
            Cache::flush();
            foreach ($this->tables as $table) {
                if ($table == "location"){
                    Cache::rememberForever($table, fn() => DB::table($table)
                    ->select(DB::raw(DB::raw("location_id, organization_id, location_name, location_civic_address, ST_X(location_geo::geometry) as latitude, ST_Y(location_geo::geometry) as longitude, ST_Z(location_geo::geometry) as altitude, location_desc"))
                    )->get());
                }
                else if ($table == "sub_location"){
                    Cache::rememberForever($table, fn() => DB::table($table)
                    ->select(DB::raw(DB::raw("sub_location_id, location_id, sub_location_name, sub_location_civic_address, sub_location_floor, ST_X(sub_location_geo::geometry) as latitude, ST_Y(sub_location_geo::geometry) as longitude, ST_Z(sub_location_geo::geometry) as altitude, sub_location_desc"))
                    )->get());
                }
                else if ($table == "device_deployment"){
                    Cache::rememberForever($table, fn() => DB::table("device_deployment")
                    ->select(DB::raw("deploy_id, device.device_eui, deploy_time, location_id, sub_location_id, deploy_ip_address,  ST_X(deploy_geo::geometry) as latitude, ST_Y(deploy_geo::geometry) as longitude, ST_Z(deploy_geo::geometry) as altitude, deploy_deployed_by, deploy_is_latest, deploy_device_data, deploy_data_port, deploy_desc"))
                    ->join("device","device.device_eui","=","device_deployment.device_eui")
                    ->get());
                }
                else{
                    Cache::rememberForever($table, fn() => DB::table($table)->get());
                }
                $this->line("✔ Cached table: {$table}");
            }

            $this->info("✅ All tables cached successfully.");
            return Command::SUCCESS;

        } catch (Exception $e) {
            Log::channel("customlog")->error("Precache error: " . $e->getMessage());
            $this->error("❌ Error while caching tables. Check logs for details.");
            return Command::FAILURE;
        }
    }
}
