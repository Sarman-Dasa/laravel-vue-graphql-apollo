<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Cerbero\CommandValidator\ValidatesInput;
use Closure;
use Illuminate\Console\Command;

class argCommad extends Command
{

    use ValidatesInput;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:arg-commad {--start-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
     

        // Sample data to process
        $items = range(1, 100); // Example of 100 items

        // Define the progress bar with the total number of items
        $this->output->title('Processing Items');

        // Start the progress bar with the total number of items
        $this->output->progressStart(count($items));

        foreach ($items as $item) {
            // Simulate processing each item
            //sleep(1); // Replace with actual processing logic

            // Advance the progress bar by one step
            $this->output->progressAdvance();
        }

        // Finish the progress bar
        $this->output->progressFinish();

           $headers = ['ID', 'Name', 'Email'];

        $user = User::select('id','name','email')->get();
        // Define the rows of data to display

        // Display the table
        $this->table($headers, $user);

        $this->info('Processing complete!');
        
    }

    public function rules(): array
    {
        // return ['start-date' => 'date_format:Y-m-d'];
        return [
            'start-date' => [
                'date_format:Y-m-d',
                function (string $attribute, mixed $value, Closure $fail) {
                    $date = Carbon::parse($value);
                    $startOfYear = Carbon::now()->startOfYear();
     
                    if ($date->lessThan($startOfYear)) {
                        $fail("The {$attribute} must be a date from {$startOfYear->format('Y-m-d')} or later.");
                    }
                }
            ],
        ];
    }
}
