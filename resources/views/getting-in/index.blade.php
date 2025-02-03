<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Getting In') }}
        </h2>
    </x-slot>
    <style>
        .status-new {
            background-color: #d4edda;
            color: #155724;
        }

        /* Green */
        .status-existing {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Grey */
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>
    <div class="py-12">
        <div class="container mx-auto">
            <form method="POST" action="{{ route('enroll.store') }}" class="col-12">
                @csrf
                <input type="hidden" name="workshop_id" id="workshop_id">
                <input type="hidden" name="student_id" id="student_id">
                <div class="row">
                    <!-- Workshop Section -->
                    <div class="col-md-6">
                        <h3>Workshop Details</h3>
                        <!-- Workshop Selection -->
                        <div class="form-group">
                            <label for="workshop">Select Workshop</label>
                            <select class="form-control" id="workshop" name="workshop">
                                <option value="">Select a workshop</option>
                                @foreach($workshops as $workshop)
                                    <option value="{{ $workshop->id }}" data-fees="{{ $workshop->fees }}"
                                        data-insurance="{{ $workshop->insurance }}">
                                        {{ $workshop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Fees and Insurance -->
                        <div class="form-group">
                            <label for="workshopFees">Fees</label>
                            <input type="text" class="form-control" id="workshopFees" readonly>
                        </div>
                        <div class="form-group">
                            <label for="workshopInsurance">Insurance</label>
                            <input type="text" class="form-control" id="workshopInsurance" readonly>
                        </div>
                        <!-- Pay Amount -->
                        <div class="form-group">
                            <label for="payAmount">Pay Amount</label>
                            <input type="number" class="form-control" id="payAmount" name="payAmount" min="0"
                                value="0.00">
                        </div>
                        <!-- Remaining Amount -->
                        <div class="form-group">
                            <label for="remainingAmount">Remaining Amount</label>
                            <input type="text" class="form-control" id="remainingAmount" readonly>
                        </div>
                        <!-- Group Number Input Field -->
                        <div class="form-group">
                            <label for="groupNumber">Group Number</label>
                            <input type="number" class="form-control" id="groupNumber" name="group_number" min="1"
                                required>
                        </div>
                        <!-- End of Group Number Input Field -->
                    </div>

                    <!-- Student Section -->
                    <div class="col-md-6">
                        <h3>Student Details</h3>
                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="studentSelect">Select Student</label>
                            <select class="form-control" id="studentSelect">
                                <option value="">Select a student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-name="{{ $student->name }}"
                                        data-phone="{{ $student->phone }}" data-volunteer="{{ $student->volunteer }}">
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
                        <!-- Volunteer Checkbox -->
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="volunteer" name="volunteer" disabled>
                            <label class="form-check-label" for="volunteer">Volunteer</label>
                        </div>
                        <!-- Insurance Checkbox -->
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="insurance" name="insurance">
                            <label class="form-check-label" for="insurance">Insurance</label>
                        </div>
                        <!-- Enrollment Status -->
                        <div class="form-group">
                            <label>Enrollment Status</label>
                            <div id="enrollmentStatus" class="p-2 rounded">
                                <span class="badge"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <div class="form-group">
                            <button class="btn btn-primary" id="enrollButton">Enroll Student</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- JavaScript Section -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const students = @json($students);

            // Workshop selection logic
            document.getElementById('workshop').addEventListener('change', function () {
                const workshopId = this.value;
                document.getElementById('workshop_id').value = workshopId;
                const selectedOption = this.options[this.selectedIndex];
                const fees = selectedOption.getAttribute('data-fees');
                const insurance = selectedOption.getAttribute('data-insurance');
                const numberOfGroups = parseInt(selectedOption.getAttribute('data-groups')) || 1;

                document.getElementById('workshopFees').value = fees;
                document.getElementById('workshopInsurance').value = insurance;

                // Set default group number
                document.getElementById('groupNumber').value = getDefaultGroupNumber();

                calculateRemaining();
            });

            document.getElementById('payAmount').addEventListener('input', calculateRemaining);
            document.getElementById('insurance').addEventListener('change', calculateRemaining);

            function calculateRemaining() {
                const fees = parseFloat(document.getElementById('workshopFees').value) || 0;
                const insurance = parseFloat(document.getElementById('workshopInsurance').value) || 0;
                const payAmount = parseFloat(document.getElementById('payAmount').value) || 0;
                const pocket = parseFloat(document.getElementById('pocket').value) || 0;
                const insuranceCheckbox = document.getElementById('insurance');

                // Calculate insurance portion only if checkbox is checked and enabled
                const insuranceAmount = (insuranceCheckbox.checked && !insuranceCheckbox.disabled) ? insurance : 0;

                // Total amount owed (fees + insurance)
                const totalOwed = fees + insuranceAmount;

                // Calculate remaining (what's left to pay)
                const remaining = pocket + payAmount - totalOwed;

                // Ensure remaining can't be negative
                document.getElementById('remainingAmount').value = remaining.toFixed(2);
            }

            // Function to calculate default group number based on current time and number of groups
            function getDefaultGroupNumber() {
                const currentHour = new Date().getHours();
                let groupNumber = 1; // Default group number

                if (currentHour >= 9 && currentHour < 12) {
                    groupNumber = 1;
                } else if (currentHour >= 12 && currentHour < 15) {
                    groupNumber = 2;
                } else if (currentHour >= 15 && currentHour < 18) {
                    groupNumber = 3;
                } else {
                    groupNumber = 4; // Default to group 4 if outside designated times
                }

                return groupNumber;
            }

            // Local student search logic
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
                            listItem.dataset.volunteer = student.volunteer;
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
                    const studentVolunteer = event.target.dataset.volunteer === '1';

                    document.getElementById('name').value = studentName;
                    document.getElementById('phone').value = studentPhone;
                    document.getElementById('id').value = studentId;
                    document.getElementById('pocket').value = studentPocket;
                    document.getElementById('volunteer').checked = studentVolunteer;
                    document.querySelectorAll('.list-group').forEach(list => list.innerHTML = '');
                    document.getElementById('studentSelect').selectedIndex = 0;

                    updateInsurance(studentId);
                }
            });

            document.getElementById('studentSelect').addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const name = selectedOption.getAttribute('data-name');
                const phone = selectedOption.getAttribute('data-phone');
                const volunteer = selectedOption.getAttribute('data-volunteer') === '1';
                const studentPocket = students.find(student =>
                    student.id.toString() === this.value.trim()
                )?.pocket;

                document.getElementById('name').value = name;
                document.getElementById('phone').value = phone;
                document.getElementById('id').value = this.value;
                document.getElementById('pocket').value = studentPocket;
                document.getElementById('volunteer').checked = volunteer;
                document.querySelectorAll('.list-group').forEach(list => list.innerHTML = '');

                updateInsurance(this.value);
            });

            async function updateInsurance(studentId) {
                document.getElementById('student_id').value = studentId;

                const workshopId = document.getElementById('workshop').value;
                const insuranceCheckbox = document.getElementById('insurance');
                const statusElement = document.getElementById('enrollmentStatus');
                const statusBadge = statusElement.querySelector('.badge');

                if (!workshopId) {
                    statusElement.classList.remove('status-new', 'status-existing');
                    statusBadge.textContent = 'Select workshop first';
                    return;
                }

                try {
                    const url = `{{ route('workshop-data.first') }}?student_id=${studentId}&workshop_id=${workshopId}`;
                    const response = await fetch(url);

                    if (response.status === 404) {
                        // New student (not enrolled)
                        statusElement.classList.add('status-new');
                        statusElement.classList.remove('status-existing');
                        statusBadge.textContent = 'NEW STUDENT';
                        insuranceCheckbox.disabled = false;
                        insuranceCheckbox.checked = false;
                    } else if (response.ok) {
                        const data = await response.json();
                        // Existing student
                        statusElement.classList.add('status-existing');
                        statusElement.classList.remove('status-new');
                        statusBadge.textContent = 'EXISTING STUDENT';

                        // Set insurance status
                        insuranceCheckbox.checked = data.data?.insurance || false;
                        insuranceCheckbox.value =insuranceCheckbox.checked;
                        insuranceCheckbox.disabled = data.data?.insurance; // Disable if already has insurance
                    }
                    calculateRemaining();
                } catch (error) {
                    console.error('Error:', error);
                    statusElement.classList.add('status-new');
                    statusElement.classList.remove('status-existing');
                    statusBadge.textContent = 'NEW STUDENT';
                    insuranceCheckbox.disabled = false;
                    insuranceCheckbox.checked = false;
                }
            }
        });
    </script>
</x-app-layout>