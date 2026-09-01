// script.js

// Toggle add-task form
const toggleBtn = document.getElementById('toggleAddForm');
const addForm = document.getElementById('addTaskForm');

if (toggleBtn && addForm) {
    toggleBtn.addEventListener('click', () => {
        addForm.classList.toggle('hidden');
    });
}

// Edit modal logic
const editButtons = document.querySelectorAll('.edit-btn');
const editModal = document.getElementById('editModal');
const cancelEditBtn = document.getElementById('cancelEdit');

const editTaskId = document.getElementById('edit_task_id');
const editTitle = document.getElementById('edit_title');
const editCategory = document.getElementById('edit_category');
const editDeadline = document.getElementById('edit_deadline');
const editDescription = document.getElementById('edit_description');

editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const data = JSON.parse(btn.getAttribute('data-task'));

        editTaskId.value = data.id;
        editTitle.value = data.title || '';
        editCategory.value = data.category || '';
        editDeadline.value = data.deadline || '';
        editDescription.value = data.description || '';

        editModal.classList.remove('hidden');
    });
});

if (cancelEditBtn && editModal) {
    cancelEditBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });
}

// Close modal on background click
if (editModal) {
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.classList.add('hidden');
        }
    });
}

// Deadline highlighting (simple reminder logic)
const today = new Date();
const rows = document.querySelectorAll('.task-row');

rows.forEach(row => {
    const deadlineStr = row.getAttribute('data-deadline');
    if (!deadlineStr) return;

    const cell = row.querySelector('.deadline-cell');
    if (!cell) return;

    const deadlineDate = new Date(deadlineStr + 'T00:00:00'); // treat as local date
    const diffMs = deadlineDate - today;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        cell.classList.add('overdue');
        cell.title = 'Overdue';
    } else if (diffDays <= 2) {
        cell.classList.add('due-soon');
        cell.title = 'Due soon';
    }
});
