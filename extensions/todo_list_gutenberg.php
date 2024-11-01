<?php
defined('WPINC') || die; 
/**
 * To-Do Task List Gutenberg Block

 */
add_action('init', function() {
    register_block_type('termin-kalender/todo-list', [
        'render_callback' => 'render_todo_list_block',
        'attributes' => [
            'tasks' => [
                'type' => 'array',
                'default' => [],
            ],
        ],
    ]);
});

function render_todo_list_block($attributes) {
    $tk_benutzerrechte = ter_kal_benutzerrechte_pruefen();
    $tasks = get_option('ter_kal_todo_list_tasks', []);
    ob_start();
    ?>
    <div class="wrap">
        <h1>To-Do List</h1>
        <?php if ($tk_benutzerrechte == 'bearbeiten' || $tk_benutzerrechte == 'loeschen') { ?>
            <button id="todo-list-add-task" class="btn btn-primary">Add Task</button><br>
        <?php } else { ?>
            <p>Please <a href="<?php echo esc_url(wp_login_url()); ?>">log in</a> to edit tasks.</p>
        <?php } ?>
        <table class="widefat fixed" id="todo-list-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Priority</th>
                    <th style="width: 30%;">Task</th>
                    <th style="width: 50%;">Task Description</th>
                    <th style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var tasks = <?php echo wp_json_encode($tasks); ?>;
            renderTasks(tasks);

            function sortTasks(tasks) {
                const priorityOrder = { 'new': 1, 'high': 2, 'normal': 3, 'low': 4, 'done': 5 };
                return tasks.sort((a, b) => {
                    const priorityDiff = priorityOrder[a.priority] - priorityOrder[b.priority];
                    if (priorityDiff !== 0) {
                        return priorityDiff;
                    }
                    return b.id - a.id; // Sort by id, higher id on top
                });
            }

            $('#todo-list-add-task').on('click', function() {
                var uniqueId = generateUniqueId();
                tasks.push({ id: uniqueId, task: 'New', priority: 'new', description: uniqueId });
                updateTasks();
            });

            function generateUniqueId() {
                return tasks.length > 0 ? Math.max(...tasks.map(task => task.id)) + 1 : 1;
            }

            function renderTasks(tasks) {
                tasks = sortTasks(tasks);
                var $tbody = $('#todo-list-table tbody').empty();
                tasks.forEach(function(task) {
                    var priorityClass = task.priority; // Use priority class directly
                    <?php  if ($tk_benutzerrechte == 'bearbeiten' || $tk_benutzerrechte == 'loeschen') { ?>
                        $tbody.append('<tr class="' + priorityClass + '"><td><select class="todo-priority-select form-select form-select-sm" data-id="' + task.id + '"><option value="low" ' + (task.priority === 'low' ? 'selected' : '') + '>Low</option><option value="normal" ' + (task.priority === 'normal' ? 'selected' : '') + '>Normal</option><option value="high" ' + (task.priority === 'high' ? 'selected' : '') + '>High</option><option value="done" ' + (task.priority === 'done' ? 'selected' : '') + '>Done</option><option value="new" ' + (task.priority === 'new' ? 'selected' : '') + '>New</option></select></td><td><input type="text" class="todo-task-input form-control form-control-sm" value="' + task.task + '" data-id="' + task.id + '" /></td><td><textarea class="todo-description-input form-control" data-id="' + task.id + '">' + task.description + '</textarea></td><td><button class="todo-delete-task btn btn-outline-danger btn-sm" data-id="' + task.id + '">Delete ID: ' + task.id + '</button></td></tr>');
                    <?php } else { ?>
$tbody.append('<tr class="' + priorityClass + '"><td><select class="todo-priority-select form-select form-select-sm" data-id="' + task.id + '" disabled><option value="low" ' + (task.priority === 'low' ? 'selected' : '') + '>Low</option><option value="normal" ' + (task.priority === 'normal' ? 'selected' : '') + '>Normal</option><option value="high" ' + (task.priority === 'high' ? 'selected' : '') + '>High</option><option value="done" ' + (task.priority === 'done' ? 'selected' : '') + '>Done</option><option value="new" ' + (task.priority === 'new' ? 'selected' : '') + '>New</option></select></td><td><input type="text" class="todo-task-input form-control form-control-sm" value="' + task.task + '" data-id="' + task.id + '" readonly /></td><td><textarea class="todo-description-input form-control" data-id="' + task.id + '" readonly>' + task.description + '</textarea></td><td></td></tr>');
                    <?php } ?>
             });
            }

            $(document).on('change', '.todo-task-input, .todo-description-input, .todo-priority-select', function() {
                var id = $(this).data('id');
                var taskField = $(this).hasClass('todo-task-input') ? 'task' : ($(this).hasClass('todo-description-input') ? 'description' : 'priority');
                var task = tasks.find(t => Number(t.id) === Number(id));
                if (task) {
                    task[taskField] = $(this).val();
                    updateTasks();
                }
            });

            $(document).on('click', '.todo-delete-task', function() {
                var id = $(this).data('id');
                tasks = tasks.filter(task => Number(task.id) !== Number(id)); // Remove the task by id
                updateTasks();
            });

            var nonce = '<?php echo wp_create_nonce("todo_list_nonce"); ?>'; // Generate nonce
            function updateTasks() {
                $.post(js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL, { action: 'todo_list_update_tasks', tasks, nonce: nonce }, function(response) {
                    if (response.success) {
                        renderTasks(response.data);
                    } else {
                        console.error('Failed to update tasks:', response);
                    }
                });
            }
        });
    </script>
    <style>
        .low { background-color: #EDEDED; }
        .normal { background-color:  #F5F5FF; }
        .high { background-color:  #FFD3CC; }
        .done { background-color: #D6E3BF; font-size: 0.3em; opacity: 0.3; }
        .new { background-color: #FFFEE1; }
        textarea, .todo-task-input { width: 100%; }
    </style>
    <?php
    return ob_get_clean();

} // gutenberg todo
