<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search Console Submit Url</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>
<div class="wrapper indexPage">
    <div class="mainSection">
        <div class="logoContainer">
            <img src="assets/images/logo.png" alt="Search Console Submit Url">
        </div>
        <div class="searchContainer">
            <form method="post" id="formSubmitUrl">
                <input type="url" class="searchBox" name="url" placeholder="Enter url" required>
                <label class="textGradient"></label>
                <input type="submit" class="searchButton" id="buttonSubmit"  value="Submit">
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function resetForm(formid) {
            $('#' + formid + ' :input').each(function(){  
                $(this).val('').attr('checked',false).attr('selected',false);
                $('#buttonSubmit').val('Submit').attr('checked',false).attr('selected',false);
            });
        }        

        $("#formSubmitUrl").submit(function (e) {
            e.preventDefault();
            $( ".textGradient" ).text( "Processing Please wait..." );
            $("#buttonSubmit").prop('disabled', true);
            $.ajax({
                type: 'post',
				// updated ajax request file name here
                url: 'crawl.php',
                data: $('form').serialize(),
                success: function () {
                    $("#buttonSubmit").prop('disabled', false);
                    $( ".textGradient" ).html( "The system has received the url" );
                    setTimeout(function() {
                        resetForm('formSubmitUrl');
                        $(".textGradient").html("");
                        $('#buttonSubmit').attr('value', 'Submit');
                    }, 5000);
                }
            });
        })
    })
</script>
</body>
</html>