
var validateEmail = function()
{
    email = $(this).val();
    var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
    if (!reg.test(email)) {
        $('#email').html("inavlid input");
    } else {
        $('#email').html("");
    }
}
var numericCheck = function() {
    val = $(this).val();
    if (!$.isNumeric(val)) {
        //error.push('Please enter valid input');
        //error[5] = 'invalid age';
        $('#AgeError').html('Please enter valid input');
    } else {
        $('#AgeError').html('');
        // error[5] = '';
    }
    displayErrors();
}
$(document).ready(function(e) {

    $('#Age').change(numericCheck);
    $('#Email').change(validateEmail);
    //listcountry();
    //listCity();
    //$('#cnt').change(listCity);
    listcountry();
    $('#cnt').change(function() {
        var id = $('#cnt').val();
        listCity(id)



    });
    //call data grid view from data database
    grid();
    gridLocation();
    gridDepartment();
    locationList();
    deptList();

    $('#btnMultiDel').click(
            function() {
                Multidel()
            });
    $('#multiDelL').click(function() {
        multiLocationDel();

    });
    $('#multiDeptDel').click(function() {
        multiDeptDel()
    });
    $('#multiDelEmp').click(function() {
        multiDeptEmp();

    });
    //show form up and down
    $('#New').click(function() {
        $('#empForm').show(300);
    });

    $('#btnDept').click(function() {
        $('#DeptForm').show(300);
    });

    $('#LocForm').click(function() {
        $('#location').show(300);
    });

    $('#save').click(function(e) {
        var name = $('#Name').val(),
                email = $('#Email').val(),
                age = $('#Age').val(),
                address = $('#Address').val(),
                hiddenID = $('#hiddenid').val(),
                loc=$('#loc').val(),
                dept=$('#dept').val(),
                status = $('#status').val();
        e.preventDefault();
        var error = [];
        if (name == '')
        {
            error.push('please Enter Name');
        }
        if (age == '') {
            error.push('please Enter Age');
        }
        if (email == '') {
            error.push('please Enter Email');
        }
        if (address == '') {
            error.push('please Enter Address');
        }
        if (error.length > 0) {
            $('#ErrorMSg').show();
            $('#ErrorMSg').html(error);
        }
        if (error.length < 1) {
            $('#ErrorMSg').hide();

            //insert data into database
            $.ajax({url: "Insert.php", data: {name: name, age: age, email: email,loc:loc,dept:dept, status: status, address: address, hiddenID: hiddenID}, async: false, type: 'POST', success: function(result) {
                    result = JSON.parse(result);
                    $('#ErrorMSg').html(result.msg);
                    //console.log(($('#hiddenid').val() == ''));
                    if ($('#hiddenid').val() == '') {
                        var tds = [];
                        $.each(result.data, function(i, value)
                        {
                            if (i == 'status') {

                                c = (value == 1) ? 'checked' : '';
                                value = '<input type="checkbox" name="chk" value="1" ' + c + '  />';
                            }
                            tds.push('<td>' + value + '</td>')
                        });
                        tds.push('<td><a href="#" onclick="RowDelete(\'' + result.data.id + '\')" >Delete</a>|<a href="#"  onClick="RowEdit(\'' + result.data.id + '\')"> Edit</a></td><td><input type="checkbox" name="chekboxDel" class="chkdel" value=\'' + result.data.id + '\'/></td>')

                        var tr = '<tr id="row' + result.data.id + '">' + tds.join('') + '</tr>';
                        $('#tgrid tbody').append(tr);
                    }
                    else {
                        {
                            $("#row" + result.data.id + "> td").each(function(i, element) {

                                var row = ['id', 'name', 'age', 'address', 'email', 'status'];
                                if (row.length > i)
                                    $(element).html(result.data[row[i]]);
                            });
                        }
                        $('#hiddenid').val('');

                    }
                    formhide();
                }
            });
        }
        else {
            displayErrors();
        }
    });
    $('#btnlocation').click(function() {
        var locCode = $('#locationId').val(),
                Detail = $('#DetId').val(),
                country = $('#country').val(),
                city = $('#city').val();

        if (locCode == '' || Detail == '' || country == '')
        {
            $('#locError').html('please fill all field');
            return false;
        }
        else {
            $('#locError').hide();
            $.ajax({url: "insertLocation.php", data: {locCode: locCode, Detail: Detail, country: country, city: city}, async: false, type: 'POST', success: function(result) {

                    $('#location').hide(300);
                    $("#frmlocation").trigger('reset');
                    //row add into form
                      var tds = [];
                        $.each(result.data, function(i, value)
                        {
                            tds.push('<td>' + value + '</td>')
                        });
                        tds.push('<td><a href="#" onclick="RowDelete(\'' + result.data.id + '\')" >Delete</a></td><td><input type="checkbox" name="chekboxDel" class="chkdel" value=\'' + result.data.id + '\'/></td>')

                        var tr = '<tr id="row' + result.data.id + '">' + tds.join('') + '</tr>';
                        $('#tlocation tbody').append(tr);
                        //end row add code 

                }});
            return true;
        }

    });
    $('#dept').click(function() {
        var deptName = $('#deptName').val(),
                deptCode = $('#deptCode').val(),
                deptH = $('#deptH').val();
        if (deptName == '' || deptCode == '' || deptH == '') {
            $('#error').html('please fill All field');
            return false;
        } else {
            $.ajax({url: "insertDepart.php", data: {deptName: deptName, deptCode: deptCode, deptH: deptH}, async: false, type: 'POST', success: function(data) {
                    $('#error').hide();
                    $('#DeptForm').hide(300);
                    //$('#depForm').each (function(){
                    //          this.reset();
                    //   });
                    $("#depForm").trigger('reset');
                }});
            return true;
        }

    });

}
);
function displayErrors() {
    /*  $.each(function(i,msg){
     $('#ErrorMSg').html(msg[i]);
     }); */
    for (var i in error) {
        $('#ErrorMSg').html(error);
    }
}
//update Data in database 
function RowEdit(id)
{
    $.ajax({url: "edit.php", data: {id: id}, success: function(data) {

            data = JSON.parse(data);

            $('#Name').val(data.Name);
            $('#Age').val(data.Age);
            $('#Email').val(data.Email);
            $('#Address').val(data.Address);
            $('#hiddenid').val(id);
            $('#empForm').show(300);
            //console.log("#"+id+ "> td");

        }});
}
//function to delete data from database
function RowDelete(id)
{

    $.ajax({url: "del.php", data: {id: id}, success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            //console.log($("#row" + id));
            $("#row" + id).remove();



        },
    });
}
function delLocation(id)
{

    $.ajax({url: "delLocation.php", data: {id: id}, success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            console.log($("#row" + id));
            $("#row" + id).remove();
        },
        statusCode: {
            404: function() {
                alert("page not found");
            }
        },
    });
}
function delDepartment(id)
{

    $.ajax({url: "delDepartment.php", data: {id: id}, success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            console.log($("#row" + id));
            $("#row" + id).remove();
        },
        statusCode: {
            404: function() {
                alert("page not found");
            }
        },
    });
}
//grid function retrive all data from dataBase 
function grid() {
    $.ajax({url: "data.php", async: false, type: 'POST', success: function(data) {
            $('#empGrid').html(data);
        }});
}
function gridLocation() {
    $.ajax({url: "locatonData.php", async: false, type: 'POST', success: function(data) {
            $('#locDetail').html(data);
        }});
}
function gridDepartment() {
    $.ajax({url: "DepartmentData.php", async: false, type: 'POST', success: function(data) {
            $('#deptDetail').html(data);
        }});
}
function formhide() {
        $('#empForm').hide(1000);
    $('#form').trigger('reset');
}
function Multidel() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("del.php", {a: delId.join()});

}
function multiLocationDel() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("delLocation.php", {a: delId.join()}, function(data) {
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
        }
    });

}
function multiDeptDel() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("delDepartment.php", {a: delId.join()}, function() {
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
        }

    });
}
function multiDeptEmp() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("del.php", {a: delId.join()}, function(data) {
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
        }
    }


    );

}
function listcountry() {
    $.ajax({url: "country.php", async: false, type: 'POST', success: function(data) {
            $('#cnt-List').html(data);
        }});
}
function listCity(id) {
    $.ajax({url: "city.php", data: {id: id}, async: false, type: 'POST', success: function(data) {
            $('#city-List').html(data);
        }});
}
function locationList() {
    $.ajax({url: "LocationList.php", async: false, type: 'POST', success: function(data) {
            $('#locList').html(data);
        }});

}
function deptList() {
    $.ajax({url: "deptList.php", async: false, type: 'POST', success: function(data) {
            $('#depList').html(data);
        }});

}