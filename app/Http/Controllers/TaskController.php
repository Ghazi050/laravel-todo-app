<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('id', 'desc')->get();

        $totalTasks = Task::count();

        $completedTasks = Task::where('is_completed', true)->count();

        $activeTasks = Task::where('is_completed', false)->count();

        return view('tasks.index', compact(
            'tasks',
            'totalTasks',
            'completedTasks',
            'activeTasks'
        ));
    }

    public function store(Request $request)
    {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'is_completed' => false,
        ]);

        return redirect('/tasks');
    }

    public function update(Request $request, Task $task)
    {
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        return redirect('/tasks');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect('/tasks');
    }

    public function toggleComplete(Task $task)
    {
        $wasCompleted = $task->is_completed;

        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        if ($wasCompleted) {
            return redirect('/tasks')->with('message', 'Task moved back to active.');
        }

        return redirect('/tasks')->with('message', 'Great job! You completed a task.');
    }
}