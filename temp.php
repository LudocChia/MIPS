<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Registration Form in HTML CSS</title>
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgb(130, 106, 251);
    }

    .container {
        position: relative;
        max-width: 700px;
        width: 100%;
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .container header {
        font-size: 1.5rem;
        color: #333;
        font-weight: 500;
        text-align: center;
    }

    .container .form {
        margin-top: 30px;
    }

    .form .input-box {
        width: 100%;
        margin-top: 20px;
    }

    .input-box label {
        color: #333;
    }

    .form :where(.input-box input, .select-box) {
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

    .select-box select {
        height: 100%;
        width: 100%;
        outline: none;
        border: none;
        color: #707070;
        font-size: 1rem;
    }

    .input-box input:focus {
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
    }

    .form .column {
        display: flex;
        column-gap: 15px;
    }

    .form .gender-box {
        margin-top: 20px;
    }

    .gender-box h3 {
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

    .form :where(.gender input, .gender label) {
        cursor: pointer;
    }

    .address :where(input, .select-box) {
        margin-top: 15px;
    }

    .form button {
        height: 55px;
        width: 100%;
        color: #fff;
        font-size: 1rem;
        font-weight: 400;
        margin-top: 30px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: rgb(130, 106, 251);
    }

    .form button:hover {
        background-color: rgb(130, 106, 251);
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

<body>
    <section class="container">
        <header>Registration Form</header>
        <form action="" class="form">
            <div class="input-box">
                <label>Full Name</label>
                <input type="text" placeholder="Enter your name">
            </div>
            <div class="input-box">
                <label>Email Address</label>
                <input type="text" placeholder="Enter your email">
            </div>
            <div class="column">
                <div class="input-box">
                    <label>Phone Number</label>
                    <input type="text" placeholder="Enter phone number" required>
                </div>
                <div class="input-box">
                    <label>Birth Date</label>
                    <input type="text" placeholder="Enter birth date">
                </div>
            </div>
            <div class="gender-box">
                <h3>Gender</h3>
                <div class="gender-option">
                    <div class="gender">
                        <label for="check-male"><input type="radio" id="check-male" name="gender" checked>Male</label>
                    </div>
                    <div class="gender">
                        <label for="check-female"><input type="radio" id="check-female" name="gender">Female</label>
                    </div>
                    <div class="gender">
                        <label for="check-other"><input type="radio" id="check-other" name="gender">Prefer not to say</label>
                    </div>
                </div>
                <div class="input-box">
                    <label>Address</label>
                    <input type="text" placeholder="Enter street address" required>
                    <input type="text" placeholder="Enter street address line 2" required>
                    <div class="column">
                        <div class="select-box">
                            <select>
                                <option hidden>Select Country</option>
                                <option>America</option>
                                <option>Japan</option>
                                <option>India</option>
                                <option>Nepal</option>
                            </select>
                        </div>
                        <input type="text" placeholder="Enter your city" required>
                    </div>
                    <input type="text" placeholder="Enter you region" required>
                    <input type="number" placeholder="Enter postal code" required>
                </div>
                <button>Submit</button>
                <div class="text">
                    <h3>Already have an account? <a href="#">Login Now</a></h3>
                </div>
            </div>
        </form>
    </section>
</body>

</html>