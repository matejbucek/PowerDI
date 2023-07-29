<?php

namespace SimpleFW\Scheduling;

use SimpleFW\Annotations\Schedule;
use SimpleFW\Containers\ContainerAccessor;
use SimpleFW\Loaders\ComponentLoader;
use SimpleFW\Storage\Storage;

class Scheduler {

    private Storage $storage;
    private ContainerAccessor $containerAccessor;
    private int $lastSchedule;
    private int $currentSchedule;

    public function __construct(Storage $storage, ContainerAccessor $containerAccessor) {
        $this->storage = $storage;
        $this->containerAccessor = $containerAccessor;

        if(!$this->storage->has("scheduler")) {
            $this->lastSchedule = time();
            $this->currentSchedule = $this->lastSchedule;
            $this->storage->set("scheduler", ["last_schedule" => $this->lastSchedule]);
        } else {
            $data = $this->storage->get("scheduler");
            $this->lastSchedule = $data->last_schedule;
            $this->currentSchedule = time();
            $data->last_schedule = $this->currentSchedule;
            $this->storage->set("scheduler", $data);
        }
    }

    public function schedule() {
        foreach ($this->containerAccessor->getServiceClasses() as $class) {
            $methods = ComponentLoader::filterMethods($class, Schedule::class);
            if(count($methods) == 0) continue;
            $service = $this->containerAccessor->getService(ComponentLoader::resolveServiceName($class));
            foreach($methods as $method) {
                $reflectionMethod = new \ReflectionMethod($class, $method->name);
                $attribute = $reflectionMethod->getAttributes(Schedule::class)[0]->newInstance();
                if($this->lastSchedule + $attribute->getFrequency() <= $this->currentSchedule)
                    $reflectionMethod->invoke($service);
            }
        }
    }
}