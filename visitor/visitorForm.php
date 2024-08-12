

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form - Mahans School</title>
    <link rel="stylesheet" href="../visitor/visitor.css" >
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "../visitor/header.php"; ?>
    <div class="box1">
        <h1>Please fill up the form </h1>
        <form action="submit.php" method="post">
            <div class="name">
                <h2>Name :</h2>
                <input type="text" id="name" name="Name" >
            </div>
            <div class="Phone">
                <h2>Phone Number :</h2>
                <input type="text" id="phone" name="Phone" >
            </div>
            <div class="Email">
                <h2>Email Address :</h2>
                <input type="text" id="email" name="Email" > 
            </div>
            <div class="Company">
                <h2>Company/Organization</h2>
                <input type="text" id="company" name="Company">
            </div>
            <div class="Plate">
                <h2>Plate Number</h2>
                <input type="text" id="plate" name="Plate">
            </div>
            <div class="Date">
                <h2>Visit Date</h2>
                <input type="date" id="date" name="Date">
            </div>
            <div class="Time">
                <h2>Visit Time</h2>
                <input type="text" id="time" name="Time" placeholder="XX.XXAM/PM">
            </div>
            <div class="People">
                <h2>People</h2>
                <input type="text" id="people" name="People">
            </div>
            <div class="Purpose">
                <h2>Purpose</h2>
                <textarea id="purpose" name="Purpose" rows="4" cols="5"></textarea>
            </div>
            <div>
                <input type="submit" value="Submit" id="btn" >
            </div>
        </form>
    </div>
    <!-- <dialog  class="modal" id="modal">
        <h1>Submitted Sucessfully !</h1>
        <input type="submit" value="Ok" id="btn1">
    </dialog>
    <script>
        const modal = document.querySelector('#modal');
        const openModal = document.querySelector('#btn');
        const closeModal = document.querySelector('#btn1');

        openModal.addEventListener('click', () => {
            modal.showModal();
        })
        closeModal.addEventListener('click', () => {
            modal.close();
        })
    </script> -->

</body>
</html>