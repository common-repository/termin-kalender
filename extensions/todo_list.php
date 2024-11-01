<?php
defined('WPINC') || die; 
  /**
To-Do Task List
   */
  function todo_list_admin_page() {
      ?>
      <div class="wrap">
          <h1>To-Do List</h1>
          <br>
            <span name="ter_kal_admin_help" class="ter_kal_admin_title"><?php esc_html_e('Simple To-Do List.', 'termin-kalender');?></span>
            <div style="display: none;"><br>
                <?php esc_html_e('This is a simple To-Do List, not connected to the calendar.', 'termin-kalender') ?>
                <br>
                <br> <?php esc_html_e('Simply add and organize your tasks and notes here.', 'termin-kalender') ?><br>
                <b><?php esc_html_e('Gutenberg Block', 'termin-kalender') ?>:</b>
                <?php esc_html_e('You like to display and use it on a page in the frontend too?', 'termin-kalender') ?>
                <br>
                <?php esc_html_e('Yes, you can do so, there is a free Gutenberg Block', 'termin-kalender') ?>
                <br>
            </div><br><br><br>
          <button class="button button-primary" id="todo-list-add-task">Add Task</button>
          <br><br>
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
              var tasks = <?php echo wp_json_encode(get_option('ter_kal_todo_list_tasks', [])); ?>;
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

                // Function to generate a unique ID
                function generateUniqueId() {
                    return tasks.length > 0 ? Math.max(...tasks.map(task => task.id)) + 1 : 1;
                }

              // Update the rendering function to use the unique ID
              function renderTasks(tasks) {
                  tasks = sortTasks(tasks);
                  var $tbody = $('#todo-list-table tbody').empty();
                  tasks.forEach(function(task) {
                      var priorityClass = task.priority; // Use priority class directly
                      $tbody.append('<tr class="' + priorityClass + '"><td><select class="todo-priority-select" data-id="' + task.id + '"><option value="low" ' + (task.priority === 'low' ? 'selected' : '') + '>Low</option><option value="normal" ' + (task.priority === 'normal' ? 'selected' : '') + '>Normal</option><option value="high" ' + (task.priority === 'high' ? 'selected' : '') + '>High</option><option value="done" ' + (task.priority === 'done' ? 'selected' : '') + '>Done</option><option value="new" ' + (task.priority === 'new' ? 'selected' : '') + '>New</option></select></td><td><input type="text" class="todo-task-input" value="' + task.task + '" data-id="' + task.id + '" /></td><td><textarea class="todo-description-input" data-id="' + task.id + '">' + task.description + '</textarea></td><td><button class="todo-delete-task button button-link-delete" data-id="' + task.id + '">Delete ID: ' + task.id + '</button></td></tr>');
                  });
              }

              // Update the change event to find the task by ID
              $(document).on('change', '.todo-task-input, .todo-description-input, .todo-priority-select', function() {
                  var id = $(this).data('id');
                  var taskField = $(this).hasClass('todo-task-input') ? 'task' : ($(this).hasClass('todo-description-input') ? 'description' : 'priority');
                  var task = tasks.find(t => Number(t.id) === Number(id));
                  if (task) {
                      task[taskField] = $(this).val();
                      updateTasks();
                      //alert(JSON.stringify(task));
                  }
              });

              // Update the delete function to find the task by ID
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
  }

add_action('wp_ajax_todo_list_update_tasks', function() {
    // Check user capabilities
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
        exit;
    }
    check_ajax_referer('todo_list_nonce', 'nonce');      // Verify nonce
    $tasks = array_map(function($task) {
        return [
            'id' => intval($task['id']),
            'task' => sanitize_text_field($task['task']),
            'priority' => sanitize_text_field($task['priority']),
            'description' => sanitize_textarea_field($task['description']),
        ];
    }, $_POST['tasks'] ?? []);
    update_option('ter_kal_todo_list_tasks', $tasks);
    wp_send_json_success($tasks);
});