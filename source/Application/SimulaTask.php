<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Batchelor\Queue\Task;
use Batchelor\Queue\Task\Adapter as TaskAdapter;
use Batchelor\Queue\Task\Interaction;
use Batchelor\Storage\Directory;
use Batchelor\System\Service\Config;
use Batchelor\WebService\Types\JobData;
use Batchelor\WebService\Types\JobState;
use Exception;

/**
 * The simula task.
 * 
 * Demonstrate using application config to get common settings (job duration). 
 * Only the execute() lifetime method is implemented because this is a trivial 
 * task that don't perform i.e. transformation of input data.
 * 
 * This task is not making something useful. Instead the goal is to generate 
 * some random behavior to test the robustness in the job queue, task manager 
 * and scheduler.
 * 
 * @author Anders Lövgren (Nowise Systems)
 */
class SimulaTask extends TaskAdapter implements Task
{

        public function validate(JobData $data)
        {
                // Ignore
        }

        public function execute(Directory $workdir, Directory $result, Interaction $interact)
        {
                // 
                // Get behavior to simulate:
                // 
                $status = $this->getBehavior();
                $interact->getLogger()->info("Got $status behavior");

                // 
                // Throw exception or exit on request:
                // 
                if ($status == "exit") {
                        exit(rand(0, 2));
                } elseif ($status == "throw") {
                        throw new Exception("Test exception in simula task");
                }

                // 
                // Run load simulation program:
                // 
                $command = $this->getCommand($workdir, $result, $status);

                $interact->getLogger()->info("Running command $command");
                $interact->runCommand($command);

                $interact->setStatus(new JobState($status));
        }

        private function getCommand(Directory $workdir, Directory $result, string $status): string
        {
                return sprintf("%s/../simula -d %d -b -i %s -r %s -s $status", __DIR__, $this->getDuration(), $workdir->getPathname(), $result->getPathname());
        }

        private function getBehavior(): string
        {
                $behavior = Config::toArray($this->app->task->simula->behavior);
                $possible = array_keys($behavior);

                while (true) {
                        $random1 = rand(0, 100);
                        $random2 = rand(0, count($possible) - 1);

                        if ($behavior[$possible[$random2]] <= $random1) {
                                return $possible[$random2];
                        }
                }
        }

        private function getDuration(): int
        {
                return $this->app->task->simula->duration;
        }

}