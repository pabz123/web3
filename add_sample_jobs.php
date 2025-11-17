<?php
// Quick script to add sample jobs to the database
require_once 'includes/db.php';

echo "<h2>Adding Sample Jobs to Database</h2>";

// First, check if we have any employers
$empCheck = $conn->query("SELECT COUNT(*) as count FROM employers");
$empRow = $empCheck->fetch_assoc();

if ($empRow['count'] == 0) {
    echo "<p>No employers found. Creating sample employer first...</p>";
    
    // Create a sample employer
    $stmt = $conn->prepare("INSERT INTO employers (company_name, email, password, description, website, location, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $company = "Tech Solutions Inc.";
    $email = "hr@techsolutions.com";
    $password = password_hash("password123", PASSWORD_DEFAULT);
    $desc = "Leading technology solutions provider";
    $website = "https://techsolutions.com";
    $location = "Nairobi, Kenya";
    
    $stmt->bind_param("ssssss", $company, $email, $password, $desc, $website, $location);
    $stmt->execute();
    $employerId = $conn->insert_id;
    $stmt->close();
    
    echo "<p>✅ Created employer: $company (ID: $employerId)</p>";
} else {
    // Get first employer
    $emp = $conn->query("SELECT id FROM employers LIMIT 1");
    $employerId = $emp->fetch_assoc()['id'];
    echo "<p>Using existing employer ID: $employerId</p>";
}

// Sample jobs data
$sampleJobs = [
    [
        'title' => 'Software Engineer',
        'description' => 'We are looking for a talented Software Engineer to join our team. You will be working on cutting-edge projects using modern technologies.',
        'location' => 'Nairobi, Kenya',
        'type' => 'Full-time',
        'requirements' => "- Bachelor's degree in Computer Science or related field\n- 2+ years of experience in software development\n- Proficiency in JavaScript, PHP, or Python\n- Strong problem-solving skills",
        'responsibilities' => "- Develop and maintain web applications\n- Collaborate with cross-functional teams\n- Write clean, maintainable code\n- Participate in code reviews"
    ],
    [
        'title' => 'Data Analyst',
        'description' => 'Join our data team to help drive business decisions through data analysis and visualization.',
        'location' => 'Nairobi, Kenya',
        'type' => 'Full-time',
        'requirements' => "- Degree in Statistics, Mathematics, or related field\n- Experience with SQL and Excel\n- Knowledge of data visualization tools (Tableau, Power BI)\n- Strong analytical skills",
        'responsibilities' => "- Analyze business data and create reports\n- Create dashboards and visualizations\n- Identify trends and insights\n- Present findings to stakeholders"
    ],
    [
        'title' => 'Marketing Intern',
        'description' => 'Great opportunity for students to gain hands-on experience in digital marketing.',
        'location' => 'Nairobi, Kenya',
        'type' => 'Internship',
        'requirements' => "- Currently pursuing a degree in Marketing or related field\n- Strong communication skills\n- Familiarity with social media platforms\n- Creative mindset",
        'responsibilities' => "- Assist in social media management\n- Help create marketing content\n- Support campaign planning\n- Learn about digital marketing strategies"
    ],
    [
        'title' => 'UI/UX Designer',
        'description' => 'We need a creative UI/UX Designer to craft beautiful and intuitive user experiences.',
        'location' => 'Remote',
        'type' => 'Contract',
        'requirements' => "- Portfolio demonstrating UI/UX skills\n- Proficiency in Figma or Adobe XD\n- Understanding of design principles\n- Experience with user research",
        'responsibilities' => "- Design user interfaces for web and mobile\n- Create wireframes and prototypes\n- Conduct user research\n- Collaborate with developers"
    ],
    [
        'title' => 'Customer Support Representative',
        'description' => 'Help our customers succeed by providing excellent support and assistance.',
        'location' => 'Mombasa, Kenya',
        'type' => 'Part-time',
        'requirements' => "- Excellent communication skills\n- Problem-solving abilities\n- Patient and friendly demeanor\n- Computer literacy",
        'responsibilities' => "- Respond to customer inquiries\n- Resolve customer issues\n- Maintain customer records\n- Provide product information"
    ]
];

$inserted = 0;
$stmt = $conn->prepare("INSERT INTO jobs (title, description, location, type, requirements, responsibilities, employer_id, status, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, 'Open', NOW(), NOW())");

foreach ($sampleJobs as $job) {
    $stmt->bind_param("ssssssi", 
        $job['title'], 
        $job['description'], 
        $job['location'], 
        $job['type'], 
        $job['requirements'], 
        $job['responsibilities'], 
        $employerId
    );
    
    if ($stmt->execute()) {
        $inserted++;
        echo "<p>✅ Added: {$job['title']} ({$job['type']})</p>";
    } else {
        echo "<p>❌ Failed to add: {$job['title']}</p>";
    }
}

$stmt->close();

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p><strong>$inserted</strong> jobs added successfully!</p>";
echo "<p><a href='pages/jobs.php'>View Jobs Page</a></p>";
echo "<p><a href='test_jobs.php'>Check Database</a></p>";
?>
