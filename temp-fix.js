// Submit form via fetch API using the new AJAX route
fetch(`/admin/menus/${menuId}/update`, {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    }
}) 