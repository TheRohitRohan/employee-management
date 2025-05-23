// Employee Management AJAX Functions
const EmployeeManager = {
    // Add new employee
    addEmployee: function(formData) {
        return $.ajax({
            url: 'ajax/employee_actions.php',
            type: 'POST',
            data: formData,
            dataType: 'json'
        });
    },

    // Edit employee
    editEmployee: function(formData) {
        return $.ajax({
            url: 'ajax/employee_actions.php',
            type: 'POST',
            data: formData,
            dataType: 'json'
        });
    },

    // Delete employee
    deleteEmployee: function(id, csrfToken) {
        return $.ajax({
            url: 'ajax/employee_actions.php',
            type: 'POST',
            data: {
                action: 'delete',
                id: id,
                csrf_token: csrfToken
            },
            dataType: 'json'
        });
    },

    // Search employees
    searchEmployees: function(params) {
        const csrfToken = $('#csrf_token').val();
        return $.ajax({
            url: 'ajax/employee_actions.php',
            type: 'POST',
            data: {
                action: 'search',
                csrf_token: csrfToken,
                ...params
            },
            dataType: 'json'
        });
    },

    // Update employee table
    updateEmployeeTable: function(employees) {
        const tbody = $('#employeeTable tbody');
        tbody.empty();

        if (employees.length === 0) {
            tbody.append('<tr><td colspan="9" class="text-center">No employees found</td></tr>');
            return;
        }

        employees.forEach(employee => {
            const row = `
                <tr>
                    <td>${employee.first_name} ${employee.last_name}</td>
                    <td>${employee.email}</td>
                    <td>${employee.phone}</td>
                    <td>${employee.department}</td>
                    <td>${employee.job_title}</td>
                    <td>${employee.hire_date}</td>
                    <td>${employee.salary}</td>
                    <td>
                        <span class="badge bg-${employee.status === 'active' ? 'success' : 'danger'}">
                            ${employee.status}
                        </span>
                    </td>
                    <td>
                        <a href="edit_employee.php?id=${employee.id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-employee" data-id="${employee.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    },

    // Update pagination
    updatePagination: function(pagination) {
        const paginationContainer = $('#pagination');
        paginationContainer.empty();

        if (pagination.total_pages <= 1) return;

        let paginationHtml = '<ul class="pagination">';
        
        // Previous button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            paginationHtml += `
                <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `;

        paginationHtml += '</ul>';
        paginationContainer.html(paginationHtml);
    }
};

// Document Ready Handler
$(document).ready(function() {
    // Load initial employees
    EmployeeManager.searchEmployees({})
        .done(function(response) {
            if (response.success) {
                EmployeeManager.updateEmployeeTable(response.employees);
                EmployeeManager.updatePagination(response.pagination);
            }
        })
        .fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error occurred while loading employees'
            });
        });

    // Handle search form submission
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        const params = {};
        
        formData.forEach(item => {
            if (item.value) {
                params[item.name] = item.value;
            }
        });

        EmployeeManager.searchEmployees(params)
            .done(function(response) {
                if (response.success) {
                    EmployeeManager.updateEmployeeTable(response.employees);
                    EmployeeManager.updatePagination(response.pagination);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error occurred while searching employees'
                });
            });
    });

    // Handle pagination clicks
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const formData = $('#searchForm').serializeArray();
        const params = {};
        
        formData.forEach(item => {
            if (item.value) {
                params[item.name] = item.value;
            }
        });
        params.page = page;

        EmployeeManager.searchEmployees(params)
            .done(function(response) {
                if (response.success) {
                    EmployeeManager.updateEmployeeTable(response.employees);
                    EmployeeManager.updatePagination(response.pagination);
                }
            });
    });

    // Handle delete employee
    $(document).on('click', '.delete-employee', function() {
        const id = $(this).data('id');
        const csrfToken = $('#csrf_token').val();
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                EmployeeManager.deleteEmployee(id, csrfToken)
                    .done(function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message
                            }).then(() => {
                                // Refresh the employee list
                                $('#searchForm').submit();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error occurred while deleting employee'
                        });
                    });
            }
        });
    });

    // Handle add employee form submission
    $('#addEmployeeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');

        EmployeeManager.addEmployee(formData)
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    }).then(() => {
                        window.location.href = 'employees.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error occurred while adding employee'
                });
            });
    });

    // Handle edit employee form submission
    $('#editEmployeeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'edit');

        $.ajax({
            url: 'ajax/employee_actions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    }).then(() => {
                        window.location.href = 'employees.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the employee.'
                });
            }
        });
    });
}); 