window.onload = init;

function init() {

    document.getElementById("id_province").onchange = function () {
        provinceid = $("#id_province").val();
        $("#id_district").load("getDistricts.php?provinceid=" + $("#id_province").val(), function () {
            load_school();
        });
    };
    document.getElementById("id_district").onchange = function () {
        load_school();
    };

}
function load_school() {
    $('#id_school').load('getDistricts.php?districtid=' + $('#id_district').val(), function () {
        $("#id_school").trigger("change");
    });

}

function getAcronym(str) {
    str = str.split(' ').map(function(item){return item[0]}).join('').toLowerCase();
    return non_unicode(str);
}

function getProvinceAcronym(str) {
    str = str.replace("Tỉnh ", "");
    str = str.replace("Thành phố ", "");
    
    if(str=="Bình Định") return "bdi";
    if(str=="Hà Nam") return "hna";
    if(str=="Quảng Nam") return "qna";
    if(str=="Quảng Ninh") return "qni";
    ret =  getAcronym(str);
    if (ret == "br-vt")
        return "bvt";
    else
        return ret;
}

function getDistrictAcronym(str) {
    str = str.replace("Quận ", "");
    str = str.replace("Thị xã ", "");
    str = str.replace("Huyện ", "");
    str = str.replace("Thành phố ", "");
    return getAcronym(str);
}

function non_unicode(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a"); 
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e"); 
    str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i"); 
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o"); 
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u"); 
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y"); 
    str = str.replace(/đ/g,"d");
    str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
    str = str.replace(/ + /g," ");
    str = str.trim(); 
    return str;
}
