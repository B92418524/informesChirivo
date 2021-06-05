function contratos_por_proyecto() {

    var e = document.getElementById("project");
    var str = e.options[e.selectedIndex].value;

    //alert (str);

    if (str.length == 0) {
        document.getElementById("contract").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("contract").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "ajax.php?type=1&query=" + str, true);
        xmlhttp.send();
    }
}

function anexos_por_contrato() {

    var e = document.getElementById("contract");
    var str = e.options[e.selectedIndex].value;

    //alert(str);

    if (str.length == 0) {
        document.getElementById("annex").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("annex").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "ajax.php?type=2&query=" + str, true);
        xmlhttp.send();
    }
}


function proveedor_por_cif() {

    var cif = document.getElementById("cif").value;
    var empresa = document.getElementById("empresa").value;
    var str = cif + ':' + empresa;

    //alert(param);

    if (str.length < 8) {
        document.getElementById("info_by_cif").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("info_by_cif").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "ajax.php?type=3&query=" + str, true);
        xmlhttp.send();
    }
}