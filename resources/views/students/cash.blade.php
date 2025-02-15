<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Cash') }}
        </h2>
    </x-slot>
    <style>
        /* Grey */
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
        .status-new {
            background-color: #d4edda;
            color: #155724;
        }
        .status-existing {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
    <div class="py-12">
        <div class="container mx-auto">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('students.updateCash') }}" method="POST">
                @csrf
                <!-- Student Selection Section (copied from getting-in/index.blade.php) -->
                <div class="row">
                    <div class="col-md-6">
                        <h3>Student Details</h3>
                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="studentSelect">Select Student</label>
                            <select class="form-control" id="studentSelect">
                                <option value="">Select a student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-name="{{ $student->name }}"
                                        data-phone="{{ $student->phone }}">
                                        {{ $student->id }}: {{ $student->name }} ({{ $student->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" autocomplete="off">
                            <ul id="nameList" class="list-group"></ul>
                        </div>
                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" autocomplete="off">
                            <ul id="phoneList" class="list-group"></ul>
                        </div>
                        <!-- ID -->
                        <div class="form-group">
                            <label for="id">ID</label>
                            <input type="text" class="form-control" id="id" name="id" autocomplete="off">
                            <ul id="idList" class="list-group"></ul>
                        </div>
                        <!-- Pocket -->
                        <div class="form-group">
                            <label for="pocket">Pocket</label>
                            <input type="text" class="form-control" id="pocket" readonly>
                        </div>
                    </div>
                    <!-- Cash Transaction Section -->
                    <div class="col-md-6">
                        <h3>Cash Transaction</h3>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="deposit">Deposit</option>
                                <option value="withdraw">Withdraw</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const students = @json($students);

            // Student selection and search functionality copied from getting-in/index.blade.php
            const studentSelect = document.getElementById('studentSelect');
            const pocketInput = document.getElementById('pocket');

            studentSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const name = selectedOption.getAttribute('data-name');
                const phone = selectedOption.getAttribute('data-phone');
                const studentPocket = students.find(student =>
                    student.id.toString() === this.value.trim()
                )?.pocket;

                document.getElementById('name').value = name;
                document.getElementById('phone').value = phone;
                document.getElementById('id').value = this.value;
                pocketInput.value = studentPocket;
                document.querySelectorAll('.list-group').forEach(list => list.innerHTML = '');
                });

            function searchStudents(query, field) {
                const filteredStudents = students.filter(student => {
                    return student[field].toString().toLowerCase().includes(query.toLowerCase());
                }).slice(0, 5);

                const listId = `${field}List`;
                const listElement = document.getElementById(listId);
                if (listElement) {
                    listElement.innerHTML = '';
                    if (filteredStudents.length > 0) {
                        filteredStudents.forEach(student => {
                            const listItem = document.createElement('li');
                            listItem.className = 'list-group-item';
                            listItem.dataset.id = student.id;
                            listItem.dataset.name = student.name;
                            listItem.dataset.phone = student.phone;
                            listItem.textContent = `id: ${student.id} ${student.name} ${student.phone}`;
                            listElement.appendChild(listItem);
                        });
                    } else {
                        const listItem = document.createElement('li');
                        listItem.className = 'list-group-item';
                        listItem.textContent = 'No results found';
                        listElement.appendChild(listItem);
                    }
                }
            }

            ['name', 'phone', 'id'].forEach(field => {
                const inputElement = document.getElementById(field);
                inputElement.addEventListener('input', function () {
                    const query = this.value;
                    if (query.length > 0) {
                        searchStudents(query, field);
                    } else {
                        document.getElementById(`${field}List`).innerHTML = '';
                    }
                });
            });

            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('list-group-item')) {
                    const studentId = event.target.dataset.id;
                    const studentName = event.target.dataset.name;
                    const studentPhone = event.target.dataset.phone;
                    const studentPocket = students.find(student =>
                        student.id.toString() === studentId.trim()
                    )?.pocket;

                    document.getElementById('name').value = studentName;
                    document.getElementById('phone').value = studentPhone;
                    document.getElementById('id').value = studentId;
                    pocketInput.value = studentPocket;
                    document.getElementById('nameList').innerHTML = '';
                    document.getElementById('phoneList').innerHTML = '';
                    document.getElementById('idList').innerHTML = '';
                    // Reset the select to avoid duplicate selection
                    studentSelect.selectedIndex = 0;

                }
            });
        });
    </script>
</x-app-layout>