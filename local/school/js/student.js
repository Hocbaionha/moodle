window.onload = init;

function init() {
    // When a select is changed, look for the students based on the department id
    // and display on the dropdown students select

    $('#id_school').change(function () {
        $('#id_class').load('getClasses.php?schoolid=' + $('#id_school').val());
    });

}