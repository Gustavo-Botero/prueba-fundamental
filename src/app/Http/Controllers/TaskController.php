<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Task;
use App\UseCases\Contracts\Task\CreateTaskInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * List all tasks
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = Task::where('user_id', Auth::id())->paginate(10);

        return response()->json([
            'status' => 'success',
            'tasks' => $tasks,
        ]);
    }

    /**
     * Create a new task
     *
     * @param StoreRequest $request
     * @param CreateTaskInterface $createTask
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateTaskInterface $createTask): JsonResponse
    {
        return response()->json($createTask->handle($request), 201);
    }

    /**
     * Show a task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        return response()->json([
            'status' => 'success',
            'task' => $task,
        ]);
    }

    /**
     * Update a task
     *
     * @param UpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $task->update($request->validated());

        return response()->json([
            'status' => 'success',
            'task' => $task,
        ]);
    }

    /**
     * Delete a task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Authorize task
     *
     * @param Task $task
     */
    private function authorizeTask(Task $task): void
    {
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized');
    }
}
