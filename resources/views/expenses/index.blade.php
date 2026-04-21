<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>EXPENSE TRACKER</title>
</head>
<body class="bg-gray-100 p-5 md:p-10">

    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-center">💰 EXPENSE TRACKER</h1>
        
        <!-- Input Form -->
        <div class="space-y-3 mb-6 bg-gray-50 p-4 rounded-md border border-gray-100">
            <input type="text" id="title" placeholder="What did you buy?" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="number" id="amount" placeholder="How much?" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="date" id="date" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-400 outline-none">
            <button onclick="addExpense(event)" class="w-full bg-blue-500 text-white p-2 rounded font-bold hover:bg-blue-600 transition shadow-md">
                Add Expense
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="search-input" onkeyup="filterExpenses()" placeholder="🔍 Search expenses..." 
                   class="w-full border-2 border-blue-100 p-2 rounded-lg focus:border-blue-500 outline-none transition bg-blue-50">
        </div>

        <h2 class="font-bold text-lg border-b pb-2 mb-3 text-gray-700">Recent Expenses</h2>
        
        <ul id="list" class="divide-y max-h-80 overflow-y-auto">
            <!-- Expenses appear here -->
        </ul>
        
        <div class="mt-4 p-3 bg-blue-600 rounded-lg flex justify-between items-center text-white shadow-lg">
            <strong class="uppercase text-xs tracking-wider">Total Spent:</strong>
            <span class="text-xl font-bold">$<span id="total-amount">0.00</span></span>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-sm shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Expense</h2>
            <input type="hidden" id="edit-id">
            <div class="space-y-4">
                <input type="text" id="edit-title" class="w-full border p-2 rounded">
                <input type="number" id="edit-amount" class="w-full border p-2 rounded">
                <input type="date" id="edit-date" class="w-full border p-2 rounded">
                <div class="flex gap-2">
                    <button onclick="saveEdit()" class="flex-1 bg-green-500 text-white p-2 rounded font-bold">Save Changes</button>
                    <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 p-2 rounded font-bold">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-sm shadow-2xl text-gray-700">
            <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Expense Details</h2>
            <div class="space-y-4">
                <div><p class="text-xs font-bold text-gray-400">ITEM</p><p id="view-title" class="text-lg font-semibold"></p></div>
                <div class="flex justify-between">
                    <div><p class="text-xs font-bold text-gray-400">AMOUNT</p><p id="view-amount" class="text-lg font-bold text-green-600"></p></div>
                    <div><p class="text-xs font-bold text-gray-400">DATE</p><p id="view-date" class="text-lg"></p></div>
                </div>
                <div><p class="text-xs font-bold text-gray-400">SYSTEM ID</p><p id="view-id" class="text-sm font-mono text-gray-500"></p></div>
            </div>
            <button onclick="closeDetailsModal()" class="w-full mt-6 bg-blue-500 text-white p-2 rounded font-bold">Close</button>
        </div>
    </div>

    <script>
        async function fetchExpenses() {
            try {
                let res = await fetch('/expenses', { headers: { 'Accept': 'application/json' } });
                let data = await res.json();
                let list = document.getElementById('list');
                list.innerHTML = ''; 
                
                data.forEach(e => {
                    list.innerHTML += `
                        <li class="py-3 flex justify-between items-center px-1 border-gray-50 hover:bg-gray-50 transition"> 
                            <div>
                                <p class="font-semibold text-gray-800">${e.title}</p>
                                <p class="text-xs text-gray-500">${e.date}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-green-600 mr-1">$${parseFloat(e.amount).toFixed(2)}</span>
                                <button onclick='showDetails(${JSON.stringify(e)})' class="text-gray-400 text-xs font-bold uppercase hover:text-gray-700">View</button>
                                <button onclick='openEditModal(${JSON.stringify(e)})' class="text-blue-500 text-xs font-bold uppercase hover:text-blue-700">Edit</button>
                                <button onclick="deleteExpense(${e.id})" class="text-red-400 text-xs font-bold uppercase hover:text-red-600">Del</button>
                            </div>
                        </li>
                    `;
                });
                
                // Refresh search filter and total after fetching
                filterExpenses(); 
            } catch (error) { console.error("Error:", error); }
        }

        async function addExpense(event) {
            if (event) event.preventDefault();
            const title = document.getElementById('title').value;
            const amount = document.getElementById('amount').value;
            const date = document.getElementById('date').value;
            if(!title || !amount || !date) return alert("Fill all fields");

            await fetch('/expenses', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ title, amount, date })
            });

            document.getElementById('title').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('date').value = '';
            fetchExpenses();
        }

        function filterExpenses() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const items = document.querySelectorAll('#list li');

            items.forEach(item => {
                const title = item.querySelector('p').innerText.toLowerCase();
                item.style.display = title.includes(searchTerm) ? "flex" : "none";
            });
            updateVisibleTotal();
        }

        function updateVisibleTotal() {
            const items = document.querySelectorAll('#list li');
            let total = 0;
            items.forEach(item => {
                if (item.style.display !== "none") {
                    const amountText = item.querySelector('.text-green-600').innerText.replace('$', '');
                    total += parseFloat(amountText);
                }
            });
            document.getElementById('total-amount').innerText = total.toFixed(2);
        }

        // --- Modals & Actions ---
        function showDetails(e) {
            document.getElementById('view-title').innerText = e.title;
            document.getElementById('view-amount').innerText = `$${parseFloat(e.amount).toFixed(2)}`;
            document.getElementById('view-date').innerText = e.date;
            document.getElementById('view-id').innerText = `#${e.id}`;
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeDetailsModal() { document.getElementById('detailsModal').classList.add('hidden'); }

        function openEditModal(e) {
            document.getElementById('edit-id').value = e.id;
            document.getElementById('edit-title').value = e.title;
            document.getElementById('edit-amount').value = e.amount;
            document.getElementById('edit-date').value = e.date;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() { document.getElementById('editModal').classList.add('hidden'); }

        async function saveEdit() {
            const id = document.getElementById('edit-id').value;
            const title = document.getElementById('edit-title').value;
            const amount = document.getElementById('edit-amount').value;
            const date = document.getElementById('edit-date').value;

            await fetch(`/expenses/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ title, amount, date })
            });
            closeModal();
            fetchExpenses();
        }

        async function deleteExpense(id) {
            if(!confirm('Delete this item?')) return;
            await fetch(`/expenses/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            fetchExpenses();
        }

        fetchExpenses();
    </script>
</body>
</html>
