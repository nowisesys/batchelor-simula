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
                $behavior = $this->getBehavior();
                $interact->getLogger()->info("Simulating $behavior behavior");

                // 
                // Handle bad behavior first (exit and throw):
                // 
                switch ($behavior) {
                        case "exit":
                                self::runBehaviorExit();
                        case "throw":
                                self::runBehaviorThrow();
                }

                // 
                // Simulate normal behavior (i.e. run application):
                // 
                self::runBehaviorCommand(
                    $interact, $this->getCommand($workdir, $result, $behavior)
                );

                // 
                // Set job status:
                // 
                $interact->setStatus(self::getState($behavior));
        }

        private static function runBehaviorExit()
        {
                exit(rand(0, 2));
        }

        private static function runBehaviorThrow()
        {
                throw new Exception("Test exception in simula task");
        }

        private static function runBehaviorCommand(Interaction $interact, string $command)
        {
                $interact->getLogger()->info("Running command $command");

                $status = $interact->runCommand($command);

                if ($status->signaled) {
                        $interact->getLogger()->error("Command was killed with signal %d", [$status->termsig]);
                }
        }

        private function getCommand(Directory $workdir, Directory $result, string $status): string
        {
                return sprintf("%s/simula -d %d -b -i %s -r %s -s %s", dirname(__DIR__), $this->getDuration(), $workdir->getPathname(), $result->getPathname(), $status);
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

        private static function getState(string $behavior): JobState
        {
                switch ($behavior) {
                        case 'success':
                                return JobState::SUCCESS();
                        case 'warning':
                                return JobState::WARNING();
                        case 'error':
                                return JobState::ERROR();
                        case 'crash':
                                return JobState::CRASHED();
                }
        }

}
