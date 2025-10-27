<?php
namespace App\Services\Observer;
use Illuminate\Support\Facades\File;


class ObserverService{
    protected array $observers = [];
    public function __construct()
    {
        $this->loadObserversFromConfig();
    }
    protected function loadObserversFromConfig(): void
    {
        $configPath = config_path('observers.php');
        
        if (File::exists($configPath)) {
            $this->observers = require $configPath;
        }
    }



    public function registerObservers($type): void
    {

        $observers = $this->observers[$type] ?? [];
        foreach ($observers as $model => $observer) {
            if (class_exists($model) && class_exists($observer)) {
                $model::observe($observer);
            }
        }
        
    }
    

}