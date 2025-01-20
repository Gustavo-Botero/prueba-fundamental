<?php

namespace App\UseCases\Modules\Task;

use App\Http\Requests\Task\StoreRequest;
use App\Models\Task;
use App\UseCases\Contracts\Task\CreateTaskInterface;
use Illuminate\Support\Facades\Auth;

class CreateTaskUsecases implements CreateTaskInterface
{
    /**
     * Handle the task creation
     *
     * @param StoreRequest $request
     * @return array
     */
	public function handle(StoreRequest $request): array
	{
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $task = Task::create($data);

        return [
            'status' => 'success',
            'task' => $task,
        ];
	}
}
