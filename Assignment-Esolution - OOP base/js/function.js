var i, j = 0;
var validateEmail = function()
{
    email = $(this).val();
    var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
    if (!reg.test(email)) {
        $('#email').html("inavlid input");
        i = 1;
    } else {
        $('#email').html("");
        i = 0;
    }
}
var numericCheck = function() {
    val = $(this).val();
    if (!$.isNumeric(val)) {
        $('#AgeError').html('Please enter valid input');
        j = 1;
    } else {
        $('#AgeError').html('');
        j = 0;
    }
}
$(document).ready(function(e) {

    $('#Age').change(numericCheck);
    $('#Email').change(validateEmail);
    list_country();
    $('#cnt').change(function() {
        var id = $('#cnt').val();
        list_city(id)
    });

    /*
     * 
     * grid function to get data from data base 
     * gridLocation function to call location data from data Base
     * gridDepartment function to call department data from data Base
     * locationList call drop down location on employee form 
     * deptList call drop down department on employee form
     * 
     */
    grid_employee();
    grid_location();
    grid_department();
    location_list();
    dept_list();
    $('#btnMultiDel').click(function() {
        Multidel()
    });
    $('#multiDelL').click(function() {
        multiDel_location();

    });
    $('#multiDeptDel').click(function() {
        multiDel_dept();
    });
    $('#multiDelEmp').click(function() {
        multiDel_employee();
    });
    $('#New').click(function() {
        $('#empForm').show(300);
    });
    $('#btnDept').click(function() {
        $('#DeptForm').show(300);
    });
    $('#LocForm').click(function() {
        $('#location').show(300);
    });
    /*
     * 
     * Main employee form submit function
     * Employee form save button function    
     * 
     * 
     */
    $('#save').click(function(e) {
        e.preventDefault();
        var name = $('#Name').val(),
                email = $('#Email').val(),
                age = $('#Age').val(),
                address = $('#Address').val(),
                hiddenID = $('#hiddenid').val(),
                loc = $('#loc').val(),
                dept = $('#dept').val(),
                status = $('#status').val();
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
        if (i == 1) {
            error.push('invalid email');
        }
        if (j == 1) {
            error.push('invalid age');
        }
        if (error.length > 0) {
            $('#ErrorMSg').show();
            $('#ErrorMSg').html(error);
        }
        if (error.length < 1) {
            $('#ErrorMSg').hide();
            //insert data into database
            $.ajax({url: "insert.php", data: {name: name, age: age, email: email, loc: loc, dept: dept, status: status, address: address, hiddenID: hiddenID, tablename: 'person'}, async: false, type: 'POST', success: function(result) {
                    //result = JSON.parse(result);
                    //$('#ErrorMSg').html(result.msg);
                    //alert(result.s);
                    if (result.success === true) {
                        if ($('#hiddenid').val() == '') {
                            var tds = [];
                            $.each(result.data, function(i, value)
                            {
                                /*   if (i == 'status') {
                                 c = (value == 1) ? 'Active' : 'In Active';
                                 value = c;
                                 }*/
                                tds.push('<td  align="center">' + value + '</td>')
                            });
                            tds.push('<td  align="center"><span id=' + result.data.id + ' name="empBtnDel"/> Delete</span>|<a href="#"  onClick="RowEdit(\'' + result.data.id + '\')"> Edit</a><input type="checkbox" name="chekboxDel" class="chkdel" value=\'' + result.data.id + '\'/></td>')
                            var tr = '<tr id="row' + result.data.id + '">' + tds.join('') + '</tr>';
                            $('#tgrid tbody').append(tr);
                        }
                        else
                        {
                            $("#row" + result.data.id + "> td").each(function(i, element) {
                                var row = ['id', 'name', 'age', 'address', 'email', 'status'];
                                if (row.length > i) {
                                    $(element).html(result.data[row[i]]);
                                }
                            });
                        }
                     }
                    $('#hiddenid').val('');
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
                country = $('#cnt').val(),
                city = $('#city').val();

        if (locCode == '' || Detail == '' || country == '')
        {
            $('#locError').html('please fill all field');
        }
        else {
            $('#locError').hide();
            $.ajax({url: "insert.php", data: {locCode: locCode, Detail: Detail, country: country, city: city, tablename: 'location'}, async: false, type: 'POST', success: function(data) {
                    $('#location').hide(300);
                    $("#frmlocation").trigger('reset');
                    //data = JSON.parse(data);
                    //row add into form
                    /*
                     var tds = [];
                     
                     $.each(data.result, function(i, value)
                     {
                     tds.push('<td  align="center">' + value + '</td>')
                     });
                     tds.push('<td  align="center"><a href="#" onclick="RowDelete(\'' + data.result.id + '\')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value=\'' + data.result.id + '\'/></td>');
                     
                     var tr = '<tr id="row' + data.result.id + '">' + tds.join() + '</tr>';
                     $('#tlocation tbody').append(tr);
                     //end row add code */
                }});
        }
    });
    $('#dept').click(function() {
        var deptName = $('#deptName').val(),
                deptCode = $('#deptCode').val(),
                deptH = $('#deptH').val();
        if (deptName == '' || deptCode == '' || deptH == '') {
            $('#error').html('please fill All field');
        } else {
            $.ajax({url: "insert.php", data: {deptName: deptName, deptCode: deptCode, deptH: deptH, tablename: 'department'}, async: false, type: 'POST', success: function(data) {
                    $('#error').hide();
                    $('#DeptForm').hide(300);
                    $("#depForm").trigger('reset');
                    //row add into form

                    //result = JSON.parse(data);
                    /* console.log(data);
                    var tds = [];
                    $.each(result.data, function(i, value)
                    {
                        tds.push('<td align="center">' + value + '</td>')
                    });
                    tds.push('<td align="center"><a href="#" onclick="delDepartment(\'' + data.data.id + '\')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value=\'' + result.data.id + '\'/></td>');

                    var tr = '<tr id="row' + result.data.id + '" >' + tds.join() + '</tr>';
                    $('#tdept tbody').append(tr);
                    //end row add code */
                }});

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
function rowEdit_employee(id)
{
    $.ajax({url: "update.php", data: {id: id, tablename: 'person'}, success: function(data) {
            data = JSON.parse(data);
            $('#Name').val(data.Name);
            $('#Age').val(data.Age);
            $('#Email').val(data.Email);
            $('#Address').val(data.Address);
            $('#hiddenid').val(id);
            $('#status').val(data.Status);
            $("#status").val(data.Status);
            $('#empForm').show(300);
        }});
}
//function to delete data from database
function RowDelete_employee(id){

    $.ajax({url: "delete.php", data: {id: id, tablename: 'person'}, type: 'POST', success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            //console.log($("#row" + id));
            // result = JSON.parse(data);
            //if (result.status === true) {
            $("#row" + id).remove();
        }
    });
}
function RowDelete_location(id){

    $.ajax({url: "delete.php", data: {id: id, tablename: 'location'}, success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            //console.log($("#row" + id));
            $("#row" + id).remove();
        },
    });
}
function RowDelete_department(id){

    $.ajax({url: "delete.php", data: {id: id, tablename: 'department'}, success: function(data) {
            //$('#ErrorMSg').html(data.msg).show(1000);
            //console.log($("#row" + id));
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
function grid_employee() {
    $.ajax({url: "EmployeeDetail.php", async: false, type: 'POST', success: function(data) {
            $('#empGrid').html(data);
        }});
}
function grid_location() {
    $.ajax({url: "locatonDetail.php", async: false, type: 'POST', success: function(data) {
            $('#locDetail').html(data);
        }});
}
function grid_department() {
    $.ajax({url: "DepartmentDetail.php", async: false, type: 'POST', success: function(data) {
            $('#deptDetail').html(data);
        }});
}
function formhide() {
    $('#empForm').hide(1000);
    $('#form').trigger('reset');
}
function multiDel_employee() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
          
        }
    });
    $.post("delete.php", {id: delId.join(), tablename: 'person'});

}
function multiDel_location() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("delete.php", {id: delId.join(), tablename: 'location'}, function(data) {
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
        }
    });

}
function multiDel_dept() {
    var delId = [];
    $('.chkdel:checked').each(function(index, element) {
        var id = $(this).val();
        delId.push(id);
    });
    $.post("delete.php", {id: delId.join(), tablename: 'department'}, function() {
        for (i = 0; i <= delId.length; i++) {
            $("#row" + delId[i]).remove();
        }

    });
}
function list_country() {
    $.ajax({url: "country.php", async: false, type: 'POST', success: function(data) {
            $('#cnt-List').html(data);
        }});
}
function list_city(id) {
    $.ajax({url: "city.php", data: {id: id}, async: false, type: 'POST', success: function(data) {
            $('#city-List').html(data);
        }});
}
function location_list() {
    $.ajax({url: "LocationList.php", async: false, type: 'POST', success: function(data) {
            $('#locList').html(data);
        }});

}
function dept_list() {
    $.ajax({url: "deptList.php", async: false, type: 'POST', success: function(data) {
            $('#depList').html(data);
        }});

}