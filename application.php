<?php

include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/customer.php";

$pageTitle = "Job Application - MIPS";
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="application">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Job Application</h1>
                    </div>
                </div>
                <div>
                    <div class="input-container">
                        <h2>Job Title</h2>
                        <div class="input-field">
                            <input type="text" name="job_title" value="<?php echo isset($_POST['job_title']) ? htmlspecialchars($_POST['job_title']) : ''; ?>" required>
                        </div>
                        <p>Please enter the job title.</p>
                    </div>
                    <div class="input-container">
                        <h2>Staff Category</h2>
                        <div class="input-field">
                            <input type="radio" name="staff_category" value="Full-Time">
                            <label>Full-Time</label>
                            <input type="radio" name="staff_category" value="Part-Time">
                            <label>Part-Time</label>
                            <input type="radio" name="staff_category" value="Contract">
                            <label>Contract</label>
                        </div>
                        <p>Please enter the company name.</p>
                    </div>
                    <div class="input-container">
                        <h2>English Name</h2>
                        <input type="text" name="english_name">
                        <p>Please enter the company name.</p>
                    </div>
                    <div class="input-container">
                        <h2>Chinese Name</h2>
                        <input type="text" name="chinese_name">
                        <p>Please enter the company name.</p>
                    </div>
                    <div class="input-container">
                        <h2>IC / Passport No.:</h2>
                        <input type="text" name="ic_passport_no">
                        <p>Please enter the company name.</p>
                    </div>
                    <div class="input-container">
                        <h2>Place of Birth</h2>
                    </div>
                    <div class="input-container">
                        <h2>Date of Birth</h2>
                        <input type="date" name="date_of_birth">
                    </div>
                    <div class="input-container">
                        <h2>Age</h2>
                    </div>
                    <div class="input-container">
                        <h2>Gender</h2>
                        <label><input type="radio" name="gender" value="Male">Male</label>
                        <label><input type="radio" name="gender" value="Female">Female</label>
                    </div>
                    <div class="input-container">
                        <h2>Religion</h2>
                    </div>
                    <div class="input-container">
                        <h2>Height</h2>
                    </div>
                    <div class="input-container">
                        <h2>Weight</h2>
                    </div>
                    <div class="input-container">
                        <h2>Marital Status</h2>
                        <label><input type="radio" name="marital_status" value="Single">Single</label>
                        <label><input type="radio" name="marital_status" value="Married">Married</label>
                    </div>
                    <div class="input-container">
                        <h2>No. of Children</h2>
                        <input type="number" name="no_of_children">
                    </div>
                    <div class="input-container">
                        <h2>Correspondence Address</h2>
                        <input type="text" name="correspondence_address">
                        <h2>Telephone No.</h2>
                    </div>
                    <div class="input-container">
                        <h2>Permanent Address</h2>
                        <input type="text" name="permanent_address">
                        <h2>Telephone No.</h2>
                    </div>
                    <div class="input-container">
                        <h2>Email Address</h2>
                        <input type="text" name="email_address">
                        <h2>Mobile No.</h2>
                    </div>
                    <div class="input-container">
                        <h2>Emergency Contact</h2>
                    </div>
                    <div class="input-container">

                    </div>
                    <div class="input-container">

                    </div>
                    <div class="input-container">

                    </div>
                    <div class="input-container">

                    </div>

                </div>
    </section>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>