<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .completed { text-decoration: line-through; }
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .task-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #task-list {
            list-style: none;
            padding: 0;
        }
        #task-list li {
            padding: 10px;
            margin: 5px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .delete-btn {
            margin-left: auto;
        }
        .loader {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <!-- Form to Add Tasks -->
    <form id="submit" action="{{ url('addTask') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="container">
            <div class="task-container">
                <h2 class="mb-4">Task Management</h2>
                <div class="input-group mb-3">
                    <input type="text" id="task" name="task" class="form-control" placeholder="Enter task" required>
                    <button id="submit-btn" type="submit" class="btn btn-primary">Add Task</button>
                </div>
                <button id="show-all-btn" class="btn btn-secondary mb-3">Show All Tasks</button>
                <ul id="task-list" class="mt-3"></ul>
            </div>
        </div>
    </form>

    <!-- Loader -->
    <div class="loader">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
       $(document).ready(function() {
            // Toastr options
            toastr.options = {
                "closeButton": true,        // Show close button
                "progressBar": true,        // Show progress bar
                "positionClass": "toast-top-center", // Position at top center
                "timeOut": "3000",          // Duration of the toast
                "extendedTimeOut": "1000"   // Duration of the extended time
            };

            $(document).on("submit", "#submit", function(event) {
                event.preventDefault();
                $(".loader").fadeIn("slow");

                var taskInput = $("#task").val();
                // Check if task already exists in the list
                if ($("#task-list li").text().includes(taskInput)) {
                    toastr.remove();
                    toastr.error("Duplicate task. Please enter a different task.");
                    $(".loader").fadeOut("slow");
                    return false;
                }

                var formdata1 = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    type: "post",
                    data: formdata1,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $(".loader").fadeOut("slow");

                        if (response.result === 1) {
                            toastr.remove();
                            toastr.success(response.msg);  // Success message with green color
                            addTaskToList(response.task);
                        }
                        if (response.result === 0) {
                            toastr.remove();
                            toastr.error(response.msg);  // Error toast when task is duplicate
                            return false;
                        }

                        if (response.result === -1) {
                            toastr.remove();
                            toastr.error(response.msg);
                            return false;
                        }
                    }
                });
            });

            function addTaskToList(task) {
                var taskItem = `<li id="task-${task.id}">
                    <input type="checkbox" class="task-checkbox" data-task-id="${task.id}"> ${task.task}
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-task-id="${task.id}">Delete</button>
                </li>`;

                $("#task-list").append(taskItem);
                $("#task").val(""); // Clear input field after adding task
            }

            // Task Checkbox (Mark as completed and disappear)
            $(document).on("change", ".task-checkbox", function() {
                var taskId = $(this).data("task-id");
                var taskItem = $(this).closest("li");

                if (this.checked) {
                    taskItem.css("text-decoration", "line-through");
                    taskItem.fadeOut(500); // Hide the task
                    markTaskAsCompleted(taskId);
                }
            });

            function markTaskAsCompleted(taskId) {
                $.ajax({
                    url: "{{ url('markAsComplete') }}",
                    type: "POST",
                    data: {
                        id: taskId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.result === 1) {
                            toastr.success("Task marked as completed!");
                        }
                    }
                });
            }

            // Show All Tasks
            $("#show-all-btn").click(function(event) {
                event.preventDefault();
                $(".loader").fadeIn("slow");

                $.ajax({
                    url: "{{ url('getAllTasks') }}",
                    type: "GET",
                    success: function(response) {
                        $(".loader").fadeOut("slow");

                        if (response.result === 1) {
                            $("#task-list").empty();  // Clear existing tasks
                            response.tasks.forEach(function(task) {
                                addTaskToList(task);  // Add each task to the list
                            });
                        }
                    },
                    error: function() {
                        $(".loader").fadeOut("slow");
                        toastr.remove();
                        toastr.error("Error while fetching tasks.");
                    }
                });
            });

            // Delete Task
            $(document).on("click", ".delete-btn", function() {
                var taskId = $(this).data("task-id");

                if (confirm("Are you sure you want to delete this task?")) {
                    deleteTask(taskId);
                }
            });

            function deleteTask(taskId) {
                $.ajax({
                    url: "{{ url('deleteTask') }}",
                    type: "POST",
                    data: {
                        id: taskId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.result === 1) {
                            toastr.success(response.msg);
                            $("#task-" + taskId).fadeOut(500);
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function() {
                        toastr.error("An error occurred while deleting the task.");
                    }
                });
            }
        });
    </script>
</body>
</html>
