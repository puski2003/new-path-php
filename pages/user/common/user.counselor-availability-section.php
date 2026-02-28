<?php
$counselorIdField = (int)($counselor['counselor_id'] ?? 0);
?>

<div class="availability-section">
    <h4>Availability</h4>
    <div class="availability-content">
        <div class="availability-content-body">
            <div class="calendar-container">
                <div class="calendar-header">
                    <button class="calendar-nav-btn" id="prevMonth">
                        <i data-lucide="arrow-left" stroke-width="1.8" class="calendar-nav-icon" aria-label="Previous month"></i>
                    </button>
                    <span class="calendar-month" id="calendarMonth">January 2026</span>
                    <button class="calendar-nav-btn" id="nextMonth">
                        <i data-lucide="arrow-right" stroke-width="1.8" class="calendar-nav-icon" aria-label="Next month"></i>
                    </button>
                </div>
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>
            </div>
            <div class="time-slots-container">
                <h5>Available Time Slots</h5>
                <div class="time-slots" id="timeSlots">
                    <p class="no-slots-message" id="noSlotsMessage">Select a date to see available times</p>
                </div>
                <a href="#" id="bookNowBtn" class="btn btn-primary book-now-btn" onclick="proceedToBooking(event)">Book Now</a>
                <input type="hidden" id="selectedDate" value="" />
                <input type="hidden" id="selectedTime" value="" />
                <input type="hidden" id="selectedDateFormatted" value="" />
                <input type="hidden" id="counselorIdField" value="<?= $counselorIdField ?>" />
            </div>
        </div>
    </div>
</div>

<script>
    const counselorAvailability = <?= $availabilityJson ?>;

    const dayIndexToName = {
        0: null,
        1: 'monday',
        2: 'tuesday',
        3: 'wednesday',
        4: 'thursday',
        5: 'friday',
        6: null
    };

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    function initCalendar() {
        renderCalendar();
        setupEventListeners();
    }

    function isDayAvailable(date) {
        const dayIndex = date.getDay();
        const dayName = dayIndexToName[dayIndex];

        if (!dayName) return false;
        return Object.prototype.hasOwnProperty.call(counselorAvailability, dayName);
    }

    function getTimeSlotsForDay(date) {
        const dayIndex = date.getDay();
        const dayName = dayIndexToName[dayIndex];

        if (!dayName || !counselorAvailability[dayName]) {
            return [];
        }

        const schedule = counselorAvailability[dayName];
        const startHour = parseInt(String(schedule.start || '0').split(':')[0], 10);
        const endHour = parseInt(String(schedule.end || '0').split(':')[0], 10);

        if (Number.isNaN(startHour) || Number.isNaN(endHour) || endHour <= startHour) {
            return [];
        }

        const slots = [];
        for (let hour = startHour; hour < endHour; hour++) {
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            slots.push({
                value: hour.toString().padStart(2, '0') + ':00',
                display: displayHour + ':00 ' + ampm
            });
        }

        return slots;
    }

    function renderCalendar() {
        const calendarDays = document.getElementById('calendarDays');
        const calendarMonth = document.getElementById('calendarMonth');

        calendarMonth.textContent = months[currentMonth] + ' ' + currentYear;
        calendarDays.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();

        for (let i = 0; i < firstDay; i++) {
            const emptySpan = document.createElement('span');
            emptySpan.classList.add('empty');
            calendarDays.appendChild(emptySpan);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const daySpan = document.createElement('span');
            daySpan.textContent = String(day);

            const checkDate = new Date(currentYear, currentMonth, day);
            const isPast = checkDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const available = isDayAvailable(checkDate);

            if (isPast) {
                daySpan.classList.add('past');
            } else if (!available) {
                daySpan.classList.add('unavailable');
            } else {
                daySpan.classList.add('available');
                daySpan.addEventListener('click', function() {
                    document.querySelectorAll('.calendar-days span').forEach(function(d) {
                        d.classList.remove('selected');
                    });
                    this.classList.add('selected');

                    const selectedDateStr = months[currentMonth] + ' ' + this.textContent + ', ' + currentYear;
                    document.getElementById('selectedDate').value = selectedDateStr;

                    const formattedDate = currentYear + '-' +
                        String(currentMonth + 1).padStart(2, '0') + '-' +
                        String(parseInt(this.textContent, 10)).padStart(2, '0');
                    document.getElementById('selectedDateFormatted').value = formattedDate;

                    updateTimeSlots(checkDate);
                });
            }

            calendarDays.appendChild(daySpan);
        }
    }

    function updateTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        const noSlotsMessage = document.getElementById('noSlotsMessage');
        const slots = getTimeSlotsForDay(date);

        timeSlotsContainer.querySelectorAll('.time-slot').forEach(function(el) {
            el.remove();
        });

        if (slots.length === 0) {
            if (noSlotsMessage) {
                noSlotsMessage.style.display = 'block';
                noSlotsMessage.textContent = 'No available times for this day';
            }
            return;
        }

        if (noSlotsMessage) {
            noSlotsMessage.style.display = 'none';
        }

        slots.forEach(function(slot) {
            const btn = document.createElement('button');
            btn.className = 'time-slot';
            btn.dataset.time = slot.value;
            btn.textContent = slot.display;
            btn.addEventListener('click', function() {
                document.querySelectorAll('.time-slot').forEach(function(s) {
                    s.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('selectedTime').value = String(this.dataset.time || '');
            });
            timeSlotsContainer.appendChild(btn);
        });
    }

    function setupEventListeners() {
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
    }

    function proceedToBooking(event) {
        event.preventDefault();
        const counselorId = document.getElementById('counselorIdField').value;
        const selectedDate = document.getElementById('selectedDate').value;
        const selectedTime = document.getElementById('selectedTime').value;

        if (!selectedDate || !selectedTime) {
            alert('Please select a date and time slot');
            return;
        }

        const redirectUrl = '/user/counselors?id=' + encodeURIComponent(counselorId) +
            '&date=' + encodeURIComponent(selectedDate) +
            '&time=' + encodeURIComponent(selectedTime);

        window.location.href = redirectUrl;
    }

    document.addEventListener('DOMContentLoaded', initCalendar);
</script>

<style>
    .calendar-nav-icon {
        width: 18px;
        height: 18px;
    }

    .calendar-days span.unavailable {
        color: #d1d5db;
        cursor: not-allowed;
    }

    .no-slots-message {
        color: #9ca3af;
        font-size: 13px;
        text-align: center;
        padding: 16px;
    }
</style>
