<?php
include 'header.php';
$pageTitle = "Booking Management Dashboard";

// Function to generate a random number of people range
function generateNumPeopleRange() {
    $ranges = ["1-10", "11-20", "21-30", "31-40"];
    return $ranges[array_rand($ranges)];
}

// Simulate Data Fetching for Dashboard Bookings ---
$bookings = [
    [
        'room_id' => 'RM101',
        'date' => '2024-07-26',
        'time' => '2:00 PM - 3:00 PM',
        'student_name' => 'Sophia Clark',
        'student_id' => 'STU12345',
        'purpose' => 'Group Study',
        'num_people' => generateNumPeopleRange(),
        'status' => 'Approved'
    ],
    [
        'room_id' => 'RM102',
        'date' => '2024-07-26',
        'time' => '3:00 PM - 4:00 PM',
        'student_name' => 'Ethan Miller',
        'student_id' => 'STU67890',
        'purpose' => 'Presentation Practice',
        'num_people' => generateNumPeopleRange(),
        'status' => 'Cancelled'
    ],
    [
        'room_id' => 'RM103',
        'date' => '2024-07-26',
        'time' => '4:00 PM - 5:00 PM',
        'student_name' => 'Olivia Davis',
        'student_id' => 'STU11223',
        'purpose' => 'Project Meeting',
        'num_people' => generateNumPeopleRange(),
        'status' => 'In Progress'
    ],
    [
        'room_id' => 'RM104',
        'date' => '2024-07-26',
        'time' => '5:00 PM - 6:00 PM',
        'student_name' => 'Liam Wilson',
        'student_id' => 'STU33445',
        'purpose' => 'Tutoring Session',
        'num_people' => generateNumPeopleRange(),
        'status' => 'Cancelled'
    ],
    [
        'room_id' => 'RM101',
        'date' => '2024-07-26',
        'time' => '6:00 PM - 7:00 PM',
        'student_name' => 'Ava Brown',
        'student_id' => 'STU55667',
        'purpose' => 'Study Group',
        'num_people' => generateNumPeopleRange(),
        'status' => 'Approved'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-inter">
    <div class="w-full bg-white flex flex-col items-start">
        <div class="w-full min-h-screen bg-[#FAFAFA] flex flex-col items-start overflow-hidden">

            <div class="w-full flex flex-col items-start">
                <div class="w-full py-5 px-40 bg-white flex justify-center items-start">
                    <div class="flex-1 max-w-5xl overflow-hidden flex flex-col items-start">

                        <div class="w-full p-4 flex justify-between items-start flex-wrap">
                            <div class="min-w-72 flex flex-col items-start">
                                <div class="text-[#1A0F0F] text-3xl font-bold leading-tight">Booking Management Dashboard</div>
                            </div>
                            <a href="pending.php" class="h-8 px-4 bg-[#C3272B] rounded-2xl flex justify-center items-center overflow-hidden">
                                <div class="flex flex-col justify-start items-center overflow-hidden">
                                    <div class="text-center text-white text-sm font-medium leading-tight">View Pending Requests</div>
                                </div>
                            </a>
                        </div>

                        <div class="w-full p-4 flex justify-start items-start gap-4 flex-wrap">
                            <div class="flex-1 min-w-40 p-6 rounded-lg border border-[#E5D1D1] flex flex-col items-start gap-2">
                                <div class="w-full text-[#1A0F0F] text-base font-medium leading-normal">Total Bookings Today</div>
                                <div class="w-full text-[#1A0F0F] text-2xl font-bold leading-tight">25</div>
                            </div>
                            <div class="flex-1 min-w-40 p-6 rounded-lg border border-[#E5D1D1] flex flex-col items-start gap-2">
                                <div class="w-full text-[#1A0F0F] text-base font-medium leading-normal">Approved Bookings</div>
                                <div class="w-full text-[#1A0F0F] text-2xl font-bold leading-tight">20</div>
                            </div>
                            <div class="flex-1 min-w-40 p-6 rounded-lg border border-[#E5D1D1] flex flex-col items-start gap-2">
                                <div class="w-full text-[#1A0F0F] text-base font-medium leading-normal">Cancelled Bookings</div>
                                <div class="w-full text-[#1A0F0F] text-2xl font-bold leading-tight">3</div>
                            </div>
                            <div class="flex-1 min-w-40 p-6 rounded-lg border border-[#E5D1D1] flex flex-col items-start gap-2">
                                <div class="w-full text-[#1A0F0F] text-base font-medium leading-normal">Utilization Rate</div>
                                <div class="w-full text-[#1A0F0F] text-2xl font-bold leading-tight">80%</div>
                            </div>
                        </div>

                        <div class="w-full px-4 py-3 flex justify-start items-start">
                            <div class="flex-1 bg-white rounded-lg border border-[#E5D1D1] flex justify-start items-start">
                                <div class="flex-1 flex flex-col items-start">
                                    <div class="w-full flex flex-col items-start">
                                        <div class="w-full flex-1 bg-white flex justify-start items-start">
                                            <div class="w-28 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Room ID</div>
                                            </div>
                                            <div class="w-24 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Booking Date</div>
                                            </div>
                                            <div class="w-28 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Time Slot</div>
                                            </div>
                                            <div class="w-28 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Student Name</div>
                                            </div>
                                            <div class="w-28 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Student ID</div>
                                            </div>
                                            <div class="w-32 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Purpose</div>
                                            </div>
                                            <div class="w-36 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Number of People</div>
                                            </div>
                                            <div class="w-36 px-4 py-3 flex flex-col items-start">
                                                <div class="w-full text-[#1A0F0F] text-sm font-medium leading-tight">Status</div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php foreach ($bookings as $booking) {
                                        $status_class = 'bg-[#F2E8E8] text-[#1A0F0F]';
                                    ?>
                                        <div class="w-full h-24 border-t border-[#E5E8EB] flex justify-start items-start">
                                            <div class="w-28 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['room_id']; ?></div>
                                            </div>
                                            <div class="w-24 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['date']; ?></div>
                                            </div>
                                            <div class="w-28 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['time']; ?></div>
                                            </div>
                                            <div class="w-28 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['student_name']; ?></div>
                                            </div>
                                            <div class="w-28 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['student_id']; ?></div>
                                            </div>
                                            <div class="w-32 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['purpose']; ?></div>
                                            </div>
                                            <div class="w-36 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full text-[#915457] text-sm font-normal leading-tight"><?php echo $booking['num_people']; ?></div>
                                            </div>
                                            <div class="w-36 h-full px-4 py-2 flex flex-col justify-center items-center">
                                                <div class="w-full h-8 max-w-xs min-w-20 px-4 <?php echo $status_class; ?> rounded-lg flex justify-center items-center overflow-hidden">
                                                    <div class="text-center text-sm font-medium leading-tight"><?php echo $booking['status']; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once 'footer.php'; 
?>


