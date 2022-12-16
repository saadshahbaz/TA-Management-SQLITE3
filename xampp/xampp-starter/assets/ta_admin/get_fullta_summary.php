<?php
$servername = 'localhost'; // Change accordingly
$username = 'root'; // Change accordingly
$password = ''; // Change accordingly
$db = 'xampp_starter'; // Change accordingly

$entered = 'entered';
$NotFound = 'No Entry Found!';
// Create connection
$conn = new mysqli($servername, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$ta_email = $_POST['email'];
$student_id = $_POST['student_id'];

// if (strcmp($ta_email, '') != 0 and strcmp($student_id, '') != 0) {
$sql = $conn->prepare('SELECT * FROM TA_COHORT WHERE email=? AND student_id=?');
$sql->bind_param('ss', $ta_email, $student_id);
$sql->execute();
$result = $sql->get_result();

$ta = $result->fetch_assoc();
// $average = $ta_averges->fetch_assoc();
// $total_courses = $num_crs->fetch_assoc();

if (mysqli_num_rows($result) == 0) {
    echo '<p style="display:flex; 
                    justify-content:center;
                        align-item:center;
                        margin-top: 20px;
                        color: rgb(167, 37, 48);
                        font-weight: bold;
                        font-size: 18px;">' .
        $NotFound .
        '</p>';
}

echo '<h3 style="color:rgb(167, 37, 48);" >' .
    $ta['legal_name'] .
    ' Details  </h3>';

echo '<table>';
echo '<tr>
    <th class="red-label">Term & Year</th>
    <th class="red-label">Degree Level</th>
    <th class="red-label">Location</th>
    <th class="red-label">Phone</th>
    <th class="red-label">Degree</th>
    <th class="red-label">Supervisor Name</th>
    </tr>';

echo '<tr>
    <td>' .
    $ta['term_year'] .
    '</td>
    <td>' .
    $ta['grad_ugrad'] .
    '</td>
    <td>' .
    $ta['location_assigned'] .
    '</td>
    <td>' .
    $ta['phone'] .
    '</td>
    <td>' .
    $ta['degree'] .
    '</td>
    <td>' .
    $ta['supervisor_name'] .
    '</td>
    </tr></table><br /> <br />';

echo '<h3 style="color:rgb(167, 37, 48);" >' .
    $ta['legal_name'] .
    ' Application Details  </h3>';

echo '<table>';
echo '<tr>
    <th class="red-label">Date Applied</th>
    <th class="red-label">Course Applied</th>
    <th class="red-label">Priority</th>
    <th class="red-label">Hours Allocated</th>
    <th class="red-label">Open to other courses</th>
    <th class="red-label">Notes</th>
    </tr>';

echo '<tr>
    <td>' .
    $ta['date_applied'] .
    '</td>
    <td>' .
    $ta['course_applied'] .
    '</td>
    <td>' .
    $ta['priority'] .
    '</td>
    <td>' .
    $ta['hours_allocated'] .
    '</td>
    <td>' .
    $ta['open_to_other_courses'] .
    '</td>
    <td>' .
    $ta['Notes'] .
    '</td>
    </tr></table><br /> <br />';

$sql2 = $conn->prepare('SELECT * FROM TA WHERE email=? AND student_id=?');
$sql2->bind_param('ss', $ta_email, $student_id);
$sql2->execute();
$ta_details = $sql2->get_result();

// $ta2 = $ta_details->fetch_assoc();

echo '<h3 style="color:rgb(167, 37, 48);" >' .
    $ta['legal_name'] .
    ' Previous Course Details  </h3>';
echo '<table>';
echo '<tr>
        <th class="red-label">Term & Year</th>
        <th class="red-label">Course Code</th>
        <th class="red-label">Course Name</th>
        <th class="red-label">Course Instructor</th>
        <th class="red-label">Assigned Hours</th>

        </tr>';

while ($ta2 = $ta_details->fetch_assoc()) {
    // We need course information
    $sql3 = $conn->prepare(
        'SELECT * FROM course WHERE courseNumber=? AND term=? AND `year`=?'
    );
    $sql3->bind_param('sss', $ta2['course'], $ta2['term'], $ta2['years']);
    $sql3->execute();
    $course = $sql3->get_result();
    $course_details = $course->fetch_assoc();
    // Professor information
    $sql4 = $conn->prepare(
        'SELECT * FROM USER where email in (SELECT Distinct courseInstructor FROM course WHERE courseNumber=? AND term=? AND `year`=?)'
    );
    $sql4->bind_param('sss', $ta2['course'], $ta2['term'], $ta2['years']);
    $sql4->execute();
    $prof = $sql4->get_result();
    $profDetails = $prof->fetch_assoc();
    echo '<tr>
        <td>' .
        $ta2['term'] .
        ' ' .
        $ta2['years'] .
        '</td>
        <td>' .
        $ta2['course'] .
        '</td>
        <td>' .
        $course_details['courseName'] .
        '</td>
        <td>' .
        $profDetails['firstName'] .
        ' ' .
        $profDetails['lastName'] .
        '</td>
        <td>' .
        $ta2['assigned_hours'] .
        '</td>
        </tr>';
}

echo '</table><br /> <br />';

$sql6 = $conn->prepare('SELECT * FROM TA_WISHLIST WHERE ta_email=?');
$sql6->bind_param('s', $ta_email);
$sql6->execute();
$ta_wishes = $sql6->get_result();

echo '<h3 style="color:rgb(167, 37, 48);" > All Courses ' .
    $ta['legal_name'] .
    '  was in the Wishlist for Professors </h3>';

echo '<table>';
echo '<tr>
    <th class="red-label">Term & Year</th>
    <th class="red-label">TA Email</th>
    <th class="red-label">Course</th>
    <th class="red-label">Professor</th>
    </tr>';

if (mysqli_num_rows($ta_wishes) == 0) {
    echo '</table>';
    echo '<p style="display:flex; 
                    justify-content:center;
                        align-item:center;
                        margin-top: 20px;
                        color: rgb(167, 37, 48);
                        font-weight: bold;
                        font-size: 18px;">' .
        $NotFound .
        '</p>';
} else {
    while ($ta4 = $ta_wishes->fetch_assoc()) {
        echo '<tr>
        <td>' .
            $ta4['term_year'] .
            '</td>
        <td>' .
            $ta4['ta_email'] .
            '</td>
        <td>' .
            $ta4['course_num'] .
            '</td>
        <td>' .
            $ta4['prof_name'] .
            '</td>
        </tr>';
    }

    echo '</table><br /> <br />';
}

$sql8 = $conn->prepare('SELECT * FROM TA_PERFORMANCE_LOG WHERE ta_email=?');
$sql8->bind_param('s', $ta_email);
$sql8->execute();
$ta_performance = $sql8->get_result();

echo '<h3 style="color:rgb(167, 37, 48);" > Performance Logs for ' .
    $ta['legal_name'] .
    '  </h3>';

echo '<table>';
echo '<tr>
    <th class="red-label">Term & Year</th>
    <th class="red-label">TA Email</th>
    <th class="red-label">Course</th>
    <th class="red-label">Professor</th>
    <th class="red-label">Date & Time</th>
    <th class="red-label">Comments</th>

    </tr>';

if (mysqli_num_rows($ta_performance) == 0) {
    echo '</table>';
    echo '<p style="display:flex; 
                    justify-content:center;
                        align-item:center;
                        margin-top: 20px;
                        color: rgb(167, 37, 48);
                        font-weight: bold;
                        font-size: 18px;">' .
        $NotFound .
        '</p>';
} else {
    while ($ta8 = $ta_performance->fetch_assoc()) {
        $sql11 = $conn->prepare(
            'SELECT firstName, lastName from user where email in ( Select courseInstructor from course where term=? and `year`=? and courseNumber=?)'
        );
        $term_year = explode(' ', $ta8['term_year']);
        $currentTerm = $term_year[0];
        $currentYear = $term_year[1];
        $sql11->bind_param(
            'sss',
            $currentTerm,
            $currentYear,
            $ta8['course_num']
        );
        $sql11->execute();
        $prof_info = $sql11->get_result();
        $profDetails = $prof_info->fetch_assoc();

        $prof_name = $profDetails['firstName'] . ' ' . $profDetails['lastName'];

        echo '<tr>
        <td>' .
            $ta8['term_year'] .
            '</td>
        <td>' .
            $ta8['ta_email'] .
            '</td>
        <td>' .
            $ta8['course_num'] .
            '</td>
            <td>' .
            $prof_name .
            '</td>
        <td>' .
            $ta8['time_stamp'] .
            '</td>
            <td>' .
            $ta8['comment'] .
            '</td>
        </tr>';
    }

    echo '</table><br /> <br />';
}

$sql5 = $conn->prepare('SELECT * FROM TA_Ratings WHERE ta_email=?');
$sql5->bind_param('s', $ta_email);
$sql5->execute();
$ta_feed = $sql5->get_result();

echo '<h3 style="color:rgb(167, 37, 48);" >' .
    $ta['legal_name'] .
    ' Feedback from Students  </h3>';

echo '<table>';
echo '<tr>
    <th class="red-label">Term & Year</th>
    <th class="red-label">Student Email</th>
    <th class="red-label">Course</th>
    <th class="red-label">Rating</th>
    <th class="red-label">Notes</th>
    </tr>';

while ($ta3 = $ta_feed->fetch_assoc()) {
    echo '<tr>
    <td>' .
        $ta3['term'] .
        ' ' .
        $ta3['years'] .
        '</td>
    <td>' .
        $ta3['student_email'] .
        '</td>
    <td>' .
        $ta3['course'] .
        '</td>
    <td>' .
        $ta3['rating'] .
        '</td>
    <td>' .
        $ta3['Notes'] .
        '</td>
    </tr>';
}

echo '</table><br /> <br />';

$conn->close();
?>
