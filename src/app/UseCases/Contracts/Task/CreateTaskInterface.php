<?php

namespace App\UseCases\Contracts\Task;

use App\Http\Requests\Task\StoreRequest;

interface CreateTaskInterface
{
    /**
     * Handle the task creation
     *
     * @param StoreRequest $taskData
     * @return array
     */
    public function handle(StoreRequest $taskData): array;
}
