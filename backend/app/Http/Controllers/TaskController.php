<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexTasksRequest $request): JsonResponse
    {
        $query = Task::query();
        $user = auth('api')->user();

        // Проверяем, является ли пользователь админом
        if (Auth::user()->role !== 'admin') {
            // Если не админ - показываем только задачи текущего пользователя
            $query->where('user_id', $user->id);
        }

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Поиск по заголовку
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Фильтрация по дедлайну
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // Сортировка
        $query->orderBy(
            $request->sort_by,
            $request->sort_direction
        );

        // Пагинация
        $tasks = $query->paginate(min($request->per_page ?? 15, 100));

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = new Task($request->validated());
        $task->user_id = Auth::id();
        $task->save();

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        // Проверяем, что задача принадлежит пользователю или пользователь - админ
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        // Проверяем, что задача принадлежит пользователю или пользователь - админ
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $task->update($request->validated());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        // Проверяем, что задача принадлежит пользователю или пользователь - админ
        if ($task->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
