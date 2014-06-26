<div id="main" style="display:none">
            <div id="ErrorMSg"></div>
            <form method="POST" action="" enctype="multipart/form-data" name="form" id="form" >
                <table border="0" align="center" >
                    <tbody>  <tr>
                            <td>Full Name</td> 
                            <td><input type="text" name="name" id="Name"><span>*</span></td>
                        </tr>
                        <tr>
                            <td>Your Email</td>
                            <td><input type="text" name="email" id="Email"><span id="email">*</span></td>
                        </tr>
                        <tr>
                            <td>Age</td>  
                            <td><input type="text" name="age" id="Age"><span id="AgeError"></span></td>
                        </tr>
                        <tr>
                            <td>Address</td>  
                            <td><textarea cols="15" rows="5" id="Address" name="address"></textarea></td>
                        </tr>
                        <tr>
                            <td>Status</td>  
                            <td><select  id="status">
                                    <option value="0">--Select status--</option>
                                    <option value="1">Active</option>
                                    <option value="0">DeActivate</option>
                                </select></td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center"><input name="Add" type="button" id="save" value="Save"><input type="reset" value="reset"><input type="hidden" name="hiddenName" id="hiddenid" value=""></td></tr>
                    </tbody>
                </table>    
            </form>    
        </div>