<div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
    <div class="px-4 py-6 sm:p-8">
        <!-- Formulario -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Crear Nuevo Contacto</h3>
            <form id="createContactForm" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <input type="hidden" id="contactId" name="contactId" value="">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nombre</label>
                            <input type="text" id="name" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                        </div>
                        <div>
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Celular</label>
                            <input type="tel" id="phone" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                        </div>
                
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input type="email" id="email" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Dirección</label>
                        <input type="text" id="address" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                <button type="submit" 
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full sm:w-auto">
                    Crear Contacto
                </button>
            </form>
        </div>

        <!-- Tabla -->
        <div class="relative overflow-hidden rounded-xl shadow-lg">
        <!-- Barra de búsqueda -->
        <div class="bg-gray-200 p-4 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-800">Lista de Contactos</h4>
            <input id="searchInput" type="text" placeholder="Buscar contactos..." 
                       class="w-full max-w-sm bg-white border border-gray-300 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-3 shadow-sm">

        </div>
            <div class="overflow-x-auto">
                <table id="contactsTable" class="min-w-full text-sm text-left bg-white text-gray-700 border-collapse border border-gray-200">
                    <thead class="bg-gray-100  text-xs ">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Teléfono</th>
                            <th scope="col" class="px-6 py-3">Dirección</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Filas dinámicas -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let dataTable;

document.addEventListener('DOMContentLoaded', function() {
    loadContacts();


    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#contactsTable tbody tr');
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('createContactForm').addEventListener('submit', function(e) {
        e.preventDefault();


        
        const formData = {
            name: document.getElementById('name').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            email: document.getElementById('email').value
        };
        const editId = document.getElementById('contactId').value;
        const isEditing = editId !== '';
        $.ajax({
            url: isEditing 
            ? `/agendaContactos/src/contacts/update/${editId}` 
            : '/agendaContactos/src/contacts/create',
        method: 'POST',
        data: {
            ...formData,
            _method: isEditing ? 'PUT' : 'POST'
        },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Contacto creado correctamente'
                    });
                    
                    document.getElementById('createContactForm').reset();
                    loadContacts();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al crear el contacto'
                });
            }
        });
    });
});

function loadContacts() {
    $.ajax({
        url: '/agendaContactos/src/contacts/list', 
        method: 'GET',
        success: function(response) {
            const tbody = document.querySelector('#contactsTable tbody');
            tbody.innerHTML = '';
            
            response.data.forEach(contact => {
                tbody.innerHTML += `
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">${contact.name}</td>
                        <td class="px-6 py-4">${contact.phone}</td>
                        <td class="px-6 py-4">${contact.address || ''}</td>
                        <td class="px-6 py-4">
                            <button onclick="editContact(${contact.id})" 
                                    class="font-medium text-blue-600 hover:underline mr-2">
                                Editar
                            </button>
                            <button onclick="deleteContact(${contact.id})" 
                                    class="font-medium text-red-600 hover:underline">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            });
 
         
             /*

                if (dataTable) {
                dataTable.destroy();
            }
            dataTable = new simpleDatatables.DataTable("#contactsTable", {
                searchable: false,
                labels: {
                    placeholder: "Buscar...",
                    perPage: "Registros por página",
                    noRows: "No hay registros para mostrar",
                }
            });
            */
        },
        error: function(xhr) {
            console.error('Error cargando contactos:', xhr);
        }
    });
}



function editContact (id) {
    $.ajax({
        url: `/agendaContactos/src/contacts/get_contact/${id}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Llenar el formulario con los datos
                document.getElementById('name').value = response.data.name;
                document.getElementById('phone').value = response.data.phone;
                document.getElementById('email').value = response.data.email || '';
                document.getElementById('address').value = response.data.address || '';
                
                const submitButton = document.querySelector('#createContactForm button[type="submit"]');
                submitButton.textContent = 'Actualizar Contacto';
                document.getElementById('contactId').value = id; 
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Error al cargar el contacto'
            });
        }
    });
}

function deleteContact(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/agendaContactos/src/contacts/delete/${id}`,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El contacto ha sido eliminado.',
                            'success'
                        );
                        loadContacts();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Ha ocurrido un error'
                    });
                }
            });
        }
    });
}
</script>