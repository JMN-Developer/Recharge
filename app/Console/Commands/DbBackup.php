<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DbBackupNotification;
use Illuminate\Support\Facades\Log;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return 0;
        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".gz";
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
        $returnVar = NULL;
        $output  = NULL;
        $file = storage_path() . "/app/backup/" . $filename;
        //$file = fopen(storage_path() . "/app/backup/" . $filename, "r");
        //file_put_contents('test.txt',$file);
        $data = [
            'from'=>'pointrecharge@gmail.com',
            'file'=>$file
        ];
        try {

            Notification::route('mail','dev@jmnation.com')
                ->notify(new DbBackupNotification($data));
       } catch (\Throwable $th) {
           //throw $th;
            Log::error($th);
       }
        exec($command, $output, $returnVar);
    }
}
