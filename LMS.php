<?php

class Course {
    protected $courseName;
    protected $courseCode;
    protected $students = [];
    protected $instructor;

    public function __construct($courseName, $courseCode) {
        $this->courseName = $courseName;
        $this->courseCode = $courseCode;
    }

    public function getCourseName() {
        return $this->courseName;
    }

    public function getCourseCode() {
        return $this->courseCode;
    }

    public function assignInstructor(Instructor $instructor) {
        $this->instructor = $instructor;
        echo "Instructor {$instructor->getInstructorName()} assigned to {$this->courseName}\n";
    }

    public function dropStudent(Student $student) {
        $index = array_search($student, $this->students);
        if ($index !== false) {
            unset($this->students[$index]);
            echo "{$student->getStudentName()} dropped from {$this->courseName}\n";
        }
    }

    public function enrollStudent(Student $student) {
        $this->students[] = $student;
        echo "{$student->getStudentName()} enrolled in {$this->courseName}\n";
    }

    public function displayEnrolledStudents() {
        echo "Enrolled students in {$this->courseName} ({$this->courseCode}):\n";
        foreach ($this->students as $student) {
            echo "- {$student->getStudentName()} ({$student->getStudentID()})\n";
        }
    }

    public function displayInfo() {
        echo "Course: {$this->courseName}, Course Code: {$this->courseCode}\n";
    }
}

class Student {
    protected $studentName;
    protected $studentID;
    protected $enrolledCourses = [];

    public function __construct($studentName, $studentID) {
        $this->studentName = $studentName;
        $this->studentID = $studentID;
    }

    public function getStudentName() {
        return $this->studentName;
    }

    public function getStudentID() {
        return $this->studentID;
    }

    public function enrollCourse(Course $course) {
        $this->enrolledCourses[] = $course;
        $course->enrollStudent($this);
    }

    public function dropCourse(Course $course) {
        $index = array_search($course, $this->enrolledCourses);
        if ($index !== false) {
            unset($this->enrolledCourses[$index]);
            echo "{$this->studentName} dropped from {$course->getCourseName()}\n";
        }
    }

    public function displayEnrolledCourses() {
        echo "{$this->studentName}'s enrolled courses:\n";
        foreach ($this->enrolledCourses as $course) {
            echo "- {$course->getCourseName()} ({$course->getCourseCode()})\n";
        }
    }
}

class Instructor {
    protected $instructorName;
    protected $instructorID;

    public function __construct($instructorName, $instructorID) {
        $this->instructorName = $instructorName;
        $this->instructorID = $instructorID;
    }

    public function getInstructorName() {
        return $this->instructorName;
    }

    public function getInstructorID() {
        return $this->instructorID;
    }

    public function assignInstructorToCourse(Course $course) {
        $course->assignInstructor($this);
    }

    public function dropStudentFromCourse(Student $student, Course $course) {
        $course->dropStudent($student);
    }
}

class Enrollment {
    protected $student;
    protected $courses = [];

    public function __construct($student) {
        $this->student = $student;
    }

    public function enrollCourse(Course $course) {
        $this->courses[] = $course;
        $this->student->enrollCourse($course);
    }

    public function displayEnrollment() {
        echo "Enrollment for {$this->student->getStudentName()} (ID: {$this->student->getStudentID()}):\n";
        foreach ($this->courses as $course) {
            echo "- {$course->getCourseName()} ({$course->getCourseCode()})\n";
        }
    }
}

// Creating instances
$course1 = new Course('Introduction to Programming', 'CS101');
$course2 = new Course('Web Development Basics', 'WEB101');
$course3 = new Course('Database Fundamentals', 'DB101');

$student1 = new Student('Alice Johnson', 'A12345');
$student2 = new Student('Bob Smith', 'B67890');

$instructor1 = new Instructor('John Doe', 'I123');
$instructor2 = new Instructor('Jane Smith', 'I456');

$enrollment1 = new Enrollment($student1);
$enrollment1->enrollCourse($course1);
$enrollment1->enrollCourse($course2);
$enrollment1->enrollCourse($course3);

$enrollment2 = new Enrollment($student2);
$enrollment2->enrollCourse($course1);
$enrollment2->enrollCourse($course3);

$course1->assignInstructor($instructor1);
$course2->assignInstructor($instructor2);

$instructor1->dropStudentFromCourse($student1, $course1);

// Displaying information...
$course1->displayInfo();
$student1->displayEnrolledCourses();
$student2->displayEnrolledCourses();
$course1->displayEnrolledStudents();

$instructor1->getInstructorName();
$course1->displayInfo();
$course2->displayInfo();
$instructor1->getInstructorName();
$course1->displayEnrolledStudents();
$course2->displayEnrolledStudents();

?>
