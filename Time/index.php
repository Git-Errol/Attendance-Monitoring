<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeIn/TimeOut System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css">
    <style>
        .container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: center;
        }
        .message {
            margin-top: 10px;
            display: none;
        }
        .timesheet-container {
            display: none;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: center;
        }
        .timesheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .timesheet-table th, .timesheet-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .icon-title {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-title h2 {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="30" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16">
                <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5z"/>
                <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64l.012-.013.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5M8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3"/>
            </svg>
            <h2><strong>TimeIn/TimeOut System</strong></h2>
        </div>
        <h1 id="current-date"></h1>
        <h1 id="current-time"></h1>
        <button id="time-in-out-btn" class="btn btn-primary btn-lg mt-3">TimeIn</button>
        <button id="timesheet-btn" class="btn btn-secondary btn-lg mt-3">Timesheet</button>
        <div class="alert alert-success message" id="message"></div>
    </div>

    <div class="timesheet-container" id="timesheet-container">
        <table class="table table-bordered timesheet-table" id="timesheet-table">
            <thead class="thead-dark">
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button class="btn btn-info" id="download-btn">Download</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentDateElement = document.getElementById('current-date');
            const currentTimeElement = document.getElementById('current-time');
            const timeInOutBtn = document.getElementById('time-in-out-btn');
            const timesheetBtn = document.getElementById('timesheet-btn');
            const timesheetContainer = document.getElementById('timesheet-container');
            const timesheetTableBody = document.getElementById('timesheet-table').getElementsByTagName('tbody')[0];
            const downloadBtn = document.getElementById('download-btn');
            const messageElement = document.getElementById('message');

            function updateTime() {
                const now = new Date();
                const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
                const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };

                currentDateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
                currentTimeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
            }

            function formatTime(date) {
                const options = { hour: '2-digit', minute: '2-digit', hour12: true };
                return date.toLocaleTimeString('en-US', options);
            }

            function fetchTimesheet() {
                fetch('timesheet.php')
                    .then(response => response.json())
                    .then(data => {
                        timesheetTableBody.innerHTML = '';
                        data.forEach(record => {
                            const row = timesheetTableBody.insertRow();
                            row.insertCell(0).textContent = record.date;
                            row.insertCell(1).textContent = record.time_in;
                            row.insertCell(2).textContent = record.time_out ? record.time_out : '';
                        });
                    });
            }

            function checkCurrentStatus() {
                fetch('check_status.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'time_in') {
                            timeInOutBtn.textContent = 'TimeOut';
                        } else {
                            timeInOutBtn.textContent = 'TimeIn';
                        }
                    });
            }

            timeInOutBtn.addEventListener('click', () => {
                const action = timeInOutBtn.textContent;
                const now = new Date();
                const date = now.toISOString().split('T')[0];
                const time = formatTime(now);

                fetch('timein_timeout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action, date, time })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageElement.textContent = data.message;
                        messageElement.classList.add('alert-success');
                        messageElement.classList.remove('alert-danger');
                        messageElement.style.display = 'block';
                        setTimeout(() => {
                            messageElement.style.display = 'none';
                        }, 3000);
                        timeInOutBtn.textContent = action === 'TimeIn' ? 'TimeOut' : 'TimeIn';
                        fetchTimesheet();
                    } else {
                        messageElement.textContent = data.error;
                        messageElement.classList.add('alert-danger');
                        messageElement.classList.remove('alert-success');
                        messageElement.style.display = 'block';
                        setTimeout(() => {
                            messageElement.style.display = 'none';
                        }, 3000);
                    }
                });
            });

            timesheetBtn.addEventListener('click', () => {
                timesheetContainer.style.display = timesheetContainer.style.display === 'none' ? 'block' : 'none';
                fetchTimesheet();
            });

            downloadBtn.addEventListener('click', () => {
                fetch('download_timesheet.php')
                    .then(response => response.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = 'timesheet.txt';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    });
            });

            updateTime();
            setInterval(updateTime, 1000);
            checkCurrentStatus();
        });
    </script>
</body>
</html>
