window.onload = init;

function init() {
    // When a select is changed, look for the students based on the department id
    // and display on the dropdown students select
    console.log("init");
    $("#menuschool").change(function () {
        console.log("changed");
        alert(this.url);
        window.location = "/local/school/upload_result.php?";
    });

}

