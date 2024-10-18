<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/customer.php";

$pageTitle = "Job Application - MIPS";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <style>
        .application .wrapper {
            background-color: white;
        }

        form {
            margin: 3%;
        }

        .form .input-container {
            width: 100%;
            margin-top: 20px;
        }

        .input-container label {
            color: #333;
        }

        .input-container h2 {
            color: #333;
        }


        /* .form :where(.input-container input, .select-container) {
            position: relative;
            height: 50px;
            width: 100%;
            outline: none;
            font-size: 1rem;
            color: #707070;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0 15px;
        } */

        .form .input-container input[type="text"],
        .form .input-container input[type="number"],
        .form .input-container input[type="email"],
        .form .input-container input[type="date"] {
            position: relative;
            height: 50px;
            width: 100%;
            outline: none;
            font-size: 1rem;
            color: #707070;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0 15px;
        }

        .select-container select {
            height: 100%;
            width: 100%;
            outline: none;
            border: none;
            color: #707070;
            font-size: 1rem;
        }

        .input-container input:focus,
        .input-container textarea:focus {
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        }

        .form .column {
            display: flex;
            column-gap: 15px;
        }

        .form .gender-box {
            margin-top: 20px;
        }

        .gender-box h2 {
            color: #333;
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .form :where(.gender-option, .gender) {
            display: flex;
            align-items: center;
            column-gap: 50px;
        }

        .form .gender {
            column-gap: 5px;
        }

        .gender input {
            accent-color: rgb(130, 106, 251);
        }

        .form :where(.gender input, .gender h2) {
            cursor: pointer;
        }

        .address :where(input, .select-container) {
            margin-top: 15px;
        }

        /* Responsive */
        @media screen and (max-width: 500px) {
            .form .column {
                flex-wrap: wrap;
            }

            .form :where(.gender-option, .gender) {
                row-gap: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="application container">
        <div class="wrapper">
            <div class="title">
                <div class="left">
                    <h1>工作申請表 Job Application</h1>
                </div>
            </div>
            <form action="post" class="form" id="form-ajax">
                <!-- 表单开始 -->
                <div class="input-container">
                    <h2>工作職稱 Job Title</h2>
                    <div class="input-field">
                        <input type="text" name="job_title" value="<?php echo isset($_POST['job_title']) ? htmlspecialchars($_POST['job_title']) : ''; ?>" placeholder="Please enter the job title" required>
                    </div>
                </div>
                <div class="input-container">
                    <h2>職工類別 Staff Category</h2>
                    <div class="input-field">
                        <label><input type="radio" name="staff_category" value="Full-Time"> Full-Time</label>
                        <label><input type="radio" name="staff_category" value="Part-Time"> Part-Time</label>
                        <label><input type="radio" name="staff_category" value="Contract"> Contract</label>
                    </div>
                </div>
                <div class="column">
                    <div class="input-container">
                        <h2>英文姓名 English Name</h2>
                        <input type="text" name="english_name" placeholder="Please enter your English name">
                    </div>
                    <div class="input-container">
                        <h2>中文姓名 Chinese Name</h2>
                        <input type="text" name="chinese_name" placeholder="Please enter your Chinese name">
                    </div>
                </div>
                <div class="input-container">
                    <h2>身份證/護照號碼 IC / Passport No.</h2>
                    <input type="text" name="ic_passport_no" placeholder="Please enter your IC or Passport number">
                </div>
                <div class="column">
                    <div class="input-container">
                        <h2>出生地 Place of Birth</h2>
                        <input type="text" name="place_of_birth" placeholder="Please enter your place of birth">
                    </div>
                    <div class="input-container">
                        <h2>出生日期 Date of Birth</h2>
                        <input type="date" name="date_of_birth">
                    </div>
                    <div class="input-container">
                        <h2>年齡 Age</h2>
                        <input type="number" name="age" placeholder="Please enter your age">
                    </div>
                </div>
                <div class="input-container">
                    <h2>性別 Gender</h2>
                    <label><input type="radio" name="gender" value="Male"> Male</label>
                    <label><input type="radio" name="gender" value="Female"> Female</label>
                </div>
                <div class="input-container">
                    <h2>宗教 Religion</h2>
                    <input type="text" name="religion" placeholder="Please enter your religion">
                </div>
                <div class="column">
                    <div class="input-container">
                        <h2>身高 Height</h2>
                        <input type="text" name="height" placeholder="Please enter your height">
                    </div>
                    <div class="input-container">
                        <h2>體重 Weight</h2>
                        <input type="text" name="weight" placeholder="Please enter your weight">
                    </div>
                </div>
                <div class="input-container">
                    <h2>婚姻狀況 Marital Status</h2>
                    <label><input type="radio" name="marital_status" value="Single"> Single</label>
                    <label><input type="radio" name="marital_status" value="Married"> Married</label>
                </div>
                <div class="input-container">
                    <h2>子女數量 No. of Children</h2>
                    <input type="number" name="no_of_children" placeholder="Please enter number of children">
                </div>
                <div class="input-container">
                    <h2>通訊地址 Correspondence Address</h2>
                    <input type="text" name="correspondence_address" placeholder="Please enter your correspondence address">
                    <h2>電話號碼 Telephone No.</h2>
                    <input type="text" name="correspondence_phone" placeholder="Please enter your telephone number">
                </div>
                <div class="input-container">
                    <h2>永久地址 Permanent Address</h2>
                    <input type="text" name="permanent_address" placeholder="Please enter your permanent address">
                    <h2>電話號碼 Telephone No.</h2>
                    <input type="text" name="permanent_phone" placeholder="Please enter your telephone number">
                </div>
                <div class="input-container">
                    <h2>電子郵件地址 Email Address</h2>
                    <input type="email" name="email_address" placeholder="Please enter your email address">
                    <h2>手機號碼 Mobile No.</h2>
                    <input type="text" name="mobile_no" placeholder="Please enter your mobile number">
                </div>
                <div class="column">
                    <div class="input-container">
                        <h2>緊急聯絡人 Emergency Contact</h2>
                        <input type="text" name="emergency_contact" placeholder="Please enter emergency contact name">
                    </div>
                    <div class="input-container">
                        <h2>關係 Relationship</h2>
                        <input type="text" name="relationship" placeholder="Please enter relationship">
                    </div>
                    <div class="input-container">
                        <h2>聯絡電話 Contact No.</h2>
                        <input type="text" name="emergency_contact_no" placeholder="Please enter emergency contact number">
                    </div>
                </div>
                <div class="input-container">
                    <h2>是否有犯罪記錄 Any Criminal Records?</h2>
                    <label><input type="radio" name="criminal_record" value="Yes"> Yes</label>
                    <label><input type="radio" name="criminal_record" value="No"> No</label>
                    <h2>請說明原因 Please state reason</h2>
                    <input type="text" name="criminal_reason" placeholder="Please state reason if any">
                </div>
                <div class="input-container">
                    <h2>教育背景 Education Record</h2>
                    <textarea name="education_record" placeholder="Please enter your education record"></textarea>
                </div>
                <div class="input-container">
                    <h2>工作經驗 Work Experience</h2>
                    <textarea name="work_experience" placeholder="Please enter your work experience"></textarea>
                </div>
                <div class="input-container">
                    <h2>家庭成員 Family Members</h2>
                    <textarea name="family_members" placeholder="Please list your family members"></textarea>
                </div>
                <div class="input-container">
                    <h2>自我介紹 Self-Introduction</h2>
                    <textarea name="self_introduction" placeholder="Please write a self-introduction"></textarea>
                </div>
                <div class="input-container">
                    <h2>健康狀況 Health Condition</h2>
                    <textarea name="health_condition" placeholder="Please describe your health condition"></textarea>
                </div>
                <div class="input-container">
                    <h2>求職詳情 Job Application Details</h2>
                    <textarea name="job_application_details" placeholder="Please provide details about your job application"></textarea>
                </div>
                <div class="input-container">
                    <h2>技能 Skills</h2>
                    <textarea name="skills" placeholder="Please list your skills"></textarea>
                </div>
                <div class="input-container">
                    <h2>個人學習和實踐經驗 Personal Learning and Practicing Experience</h2>
                    <textarea name="learning_experience" placeholder="Please describe your learning and practicing experience"></textarea>
                </div>
                <div class="input-container">
                    <h2>備註 Remarks</h2>
                    <textarea name="remarks" placeholder="Any additional remarks"></textarea>
                </div>
                <div class="input-container controls">
                    <button type="button" class="cancel">Cancel</button>
                    <button type="reset" class="delete">Clear</button>
                    <button type="submit" class="confirm">Submit</button>
                </div>
                <!-- 表單結束 -->
            </form>
        </div>
    </section>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>