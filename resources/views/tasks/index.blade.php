@extends('layouts.app')

@section('content')

    <div class="app-header">
        <div class="header-inner">
            <div class="app-icon">
                ✓
            </div>

            <div>
                <h1>My Tasks</h1>
                <p>Simple task management</p>
            </div>
        </div>
    </div>

    <div class="app-page">

        <aside class="sidebar">

            <div class="panel">
                <h2>Add New Task</h2>

                <form method="POST" action="/tasks">
                    @csrf

                    <div class="form-group">
                        <label>Task Title *</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            placeholder="What needs to be done?"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input
                            type="text"
                            name="description"
                            class="form-control"
                            placeholder="Add some details..."
                        >
                    </div>

                    <div class="form-group">
                        <label>Priority</label>

                        <select name="priority" class="form-select">
                            <option value="high">High</option>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Due Date</label>

                        <input
                            type="date"
                            name="due_date"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        + Add Task
                    </button>
                </form>
            </div>

        </aside>

        <section class="tasks-area">

            <div class="tabs">
                <button class="tab active" type="button" data-filter="all">
                    All ({{ $totalTasks }})
                </button>

                <button class="tab" type="button" data-filter="active">
                    Active ({{ $activeTasks }})
                </button>

                <button class="tab" type="button" data-filter="done">
                    Done ({{ $completedTasks }})
                </button>
            </div>

            @if(session('message'))
                <div class="success-message">
                    {{ session('message') }}
                </div>
            @endif

            <div class="progress-card">
                <div class="progress-info">
                    <span>Progress</span>
                    <strong>{{ $completedTasks }}/{{ $totalTasks }}</strong>
                </div>

                <div class="progress">
                    <div
                        class="progress-bar"
                        style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%"
                    ></div>
                </div>

                <p>
                    {{ $totalTasks > 0 ? $completedTasks . ' task completed' : 'No tasks yet' }}
                </p>
            </div>

            @if($tasks->count() == 0)

                <div class="empty-box">
                    <h3>No tasks yet</h3>
                    <p>Add your first task from the form.</p>
                </div>

            @endif

            @foreach($tasks as $task)

                <div
                    class="task-card {{ $task->is_completed ? 'task-done' : '' }}"
                    data-status="{{ $task->is_completed ? 'done' : 'active' }}"
                >

                    <div class="task-row">

                        <form method="POST" action="/tasks/{{ $task->id }}/toggle-complete">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="status-btn {{ $task->is_completed ? 'undo' : 'done' }}"
                            >
                                {{ $task->is_completed ? 'Undo' : 'Done' }}
                            </button>
                        </form>

                        <div class="task-info">

                            <div class="task-top">
                                <h3>{{ $task->title }}</h3>

                                @if($task->priority == 'high')
                                    <span class="priority high">High</span>
                                @elseif($task->priority == 'medium')
                                    <span class="priority medium">Medium</span>
                                @else
                                    <span class="priority low">Low</span>
                                @endif
                            </div>

                            @if($task->description)
                                <p class="description">{{ $task->description }}</p>
                            @else
                                <p class="description muted">No description provided.</p>
                            @endif

                            <small>Due {{ $task->due_date }}</small>

                        </div>

                        <div class="task-actions">

                            <button
                                type="button"
                                class="action-btn edit"
                                data-bs-toggle="collapse"
                                data-bs-target="#editTask{{ $task->id }}"
                            >
                                Edit
                            </button>

                            <form method="POST" action="/tasks/{{ $task->id }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="action-btn delete">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </div>

                    <div class="collapse" id="editTask{{ $task->id }}">
                        <div class="edit-box">

                            <form method="POST" action="/tasks/{{ $task->id }}">
                                @csrf
                                @method('PUT')

                                <input
                                    type="text"
                                    name="title"
                                    class="form-control mb-2"
                                    value="{{ $task->title }}"
                                    required
                                >

                                <input
                                    type="text"
                                    name="description"
                                    class="form-control mb-2"
                                    value="{{ $task->description }}"
                                >

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <select name="priority" class="form-select">
                                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <input
                                            type="date"
                                            name="due_date"
                                            class="form-control"
                                            value="{{ $task->due_date }}"
                                            required
                                        >
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-save">
                                    Save Changes
                                </button>

                            </form>

                        </div>
                    </div>

                </div>

            @endforeach

        </section>

    </div>

    <script>
        const tabs = document.querySelectorAll('.tab');
        const taskCards = document.querySelectorAll('.task-card');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const selectedFilter = tab.dataset.filter;

                tabs.forEach(function (item) {
                    item.classList.remove('active');
                });

                tab.classList.add('active');

                taskCards.forEach(function (card) {
                    const taskStatus = card.dataset.status;

                    if (selectedFilter === 'all') {
                        card.style.display = 'block';
                    } else if (selectedFilter === taskStatus) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>

@endsection