<?php
if ($_POST) {

	$email = $_POST['custemail'];
	$newcsv = "download/csv" . $_POST['ticket'] . ".csv";
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	for ($i = 1; $_POST['productname' . $i] != null; $i++) {
		$name = str_replace('"', '""' , $_POST['productname' . $i]);
		$price = (float)$_POST['price' . $i];
		$cat = str_replace('"', '' , $_POST['category' . $i]);
		$qty = (int)$_POST['quantity' . $i];
		$desc = str_replace('"', '""' , str_replace(array("\r\n", "\r", "\n"), "<br>", $_POST['description' . $i]));
		$imgurl = str_replace("index.php", "", $url) . "uploads/" . $_POST['ticket'] . "-" . $i . ".jpg";		
		$line = '"' . $name . '",' . $price . ',"' . $cat . '",' . $qty . ',"' . $desc . '","' . $imgurl . '"' . "\n";
		$success = file_put_contents($newcsv, $line, FILE_APPEND | LOCK_EX);
		
	}
	if ($success > 1) {
		$msgcsvurl = str_replace("index.php", "", $url) . $newcsv;
		$to      = 'contactus@yell.com';
		$subject = 'Re: [ Ticket: ' . $_POST['ticket'] . ' ] CSV submission';
		$message = "Hi, please see the address below for the customers CSV\r\n\r\n $msgcsvurl \r\n\r\nKind regards\r\nCSV Tool\r\n";
		$headers = 'From: ' . $email . "\r\n" .
		'Reply-To: ' . $email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $message, $headers);
		$submission = "<br><br><h2>Your submission has been sent. Thank you</h2><br><br>";
	}	
}
// delete localstorage, make new local data saying form complete
?>
<!DOCTYPE html>
<html lang="en-gb">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Product form</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <style type="text/css">
     body {
		font-family: 'Source Sans Pro', sans-serif;
		font-size: 15px; 
		background-color: #fefff1;
		margin: auto;
		max-width: 960px;
		padding: 5px;
	}
    table tr:nth-child(odd) td{
		background-color: #feffd9;
    }
	table tr:nth-child(even) td{
		background-color: #fefff1;
	}
	td {
		text-align: center;
	}
      /* this does not work */
	tr + td {
   		text-align: center;
        background-color: #000000;
	}
    button, input, select, textarea {
        font-family : inherit;
        font-size   : 90%;    
        margin: auto;
        position: relative; 	
    }
    .exampletxt {font-size: 12px; 
	}
	

	.loader {
	    display: none;	
	    font-size: 10px;
	    margin-left: 20px auto;
	    text-indent: -9999em;
	    width: 3em;
	    height: 3em;
		border-radius: 50%;
		background: #0c1deb;
   	    background: -moz-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
		background: -webkit-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
		background: -o-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
		background: -ms-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
		background: linear-gradient(to right, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
		position: relative;
		-webkit-animation: load3 1.4s infinite linear;
		animation: load3 1.4s infinite linear;
		-webkit-transform: translateZ(0);
		-ms-transform: translateZ(0);
		transform: translateZ(0);

	}
    .loader:before {
		width: 50%;
		height: 50%;
		background: #0c1deb;
		border-radius: 100% 0 0 0;
		position: absolute;
		top: 0;
			left: 0;
		content: '';
	}	
	.loader:after {
		background: #ffffff;
		width: 75%;
		height: 75%;
		border-radius: 50%;
		content: '';
		margin: auto;
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
	}
	@-webkit-keyframes load3 {
		0% {
			-webkit-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}
	@keyframes load3 {
		0% {
			-webkit-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		 }
	}	
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function (e) {

		$(window).unload(function(){

			$('input[type="text"], textarea, input[type="email"]').each(function(){    
				var id = $(this).attr('id');
				var value = $(this).val();
				localStorage.setItem(id, value);
			});   
		});

		$(function(){
			$('input[type="text"], textarea, input[type="email"]').each(function(){    
				var id = $(this).attr('id');
				var value = localStorage.getItem(id);
				$(this).val(value);

			}); 
		});


	   $('input[id^=file]').on('change', function () {

			var rowposition = $(this).attr('id').substring(4,6);
			//$(this).html('Uploading'); // change button message
			$(this).prop('disabled', true); // disable upload button 
			$('#loader' + rowposition).css("display", "inline-block"); // show animation
			var file_data = $('#file' + rowposition).prop('files')[0];
			var form_data = new FormData();
			
			//TEMP HARDFIX TO RENAME FILE
			// EDIT LATER TO INHERIT TICKETID
			var ticketid = window.location.search;
			ticketid = ticketid.replace("?ticket=", ''); 

			// ASSUMES JPG - ADD PNG FIX
			form_data.append('file', file_data, ticketid + '-' + rowposition + '.jpg');
			$.ajax({
				url: 'uploadimage.php', 
				dataType: 'text', 
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,
				type: 'post',
				success: function (response) {
					$('#msg' + rowposition).html(response); // display response
					$('#loader' + rowposition).hide(); // hide animation
					if (response.indexOf("ERROR") == -1) // check for error
					{
						$('#file' + rowposition).hide(); // hide upload field
					} else if (response.indexOf("Image") == 0) {
						$('#file' + rowposition).hide(); // hide upload field					   
					} else {
						$('#file' + rowposition).prop('disabled', false); // enable upload button	
						$('#file' + rowposition).html('Choose file'); // change button message back
					}

				},
				error: function (response) {
					$('#msg1').html(response); // display error response from the PHP script
					$('#file' + rowposition).prop('disabled', false); // enable upload button
					$('#file' + rowposition).html('Choose file'); // change button message back
					$('#loader' + rowposition).hide(); // hide animation
				}
			});
		});		

	});
	</script>
  </head>
  <body>
  <?=$submission ?>
    <h1>Online Product Form</h1>
    <form name="product_form" method="post" action="index.php" enctype="multipart/form-data">
      <table style="width: 525px; height: 77px;" border="0">
        <tbody>
          <tr>
            <td style="width: 350.217px;">Enter your email address:</td>
            <td style="width: 332.983px;"><input name="custemail" required="required" size="30" maxlength="100" type="email"><br>
            </td>
          </tr>
          <tr>
            <td>
              <p> Ticket number:</p>
            </td>
            <td style="margin-left: 11px;">
              <p id="ticketnum"><?=$_GET['ticket']?> </p>
			  <input type="hidden" name="ticket" value="<?=$_GET['ticket']?>">
              <br>
            </td>
          </tr>
        </tbody>
      </table>
<!--      <script>
        var ticketid = window.location.search;
        ticketid = ticketid.replace("?ticket=", ''); 
        document.getElementById('ticketnum').innerHTML = ticketid;
    </script> -->
      <p>
	  <b>Instructions</b><br>
	  Every product you add needs a name and a price. Choosing a category, quantity, description and photo is also recommended. 
      </p><br>
      <table style="width: 1027px; height: 1236px;" name="bigtable" border="0">
        <tbody>
          <tr>
            <td style="width: 22px;"><br>
            </td>
            <td style="width: 190px;">Product Name*<br>
              <div class="exampletxt"><br>
              </div>
            </td>
            <td style="width: 83.1503px;">Price*<br>
              <div class="exampletxt"><br>
              </div>
            </td>
            <td style="width: 159.25px;">Category<br>
              <div class="exampletxt"><br>
              </div>
            </td>
            <td style="width: 80px;">Quantity<br>
              <div class="exampletxt"><br>
              </div>
            </td>
            <td style="text-align: center;">Description<br>
              <div class="exampletxt"><br>
              </div>
            </td>
            <td style="text-align: center;">Upload Photo<br>
              &nbsp;</td>
          </tr>
          <tr>
            <td>
              1</td>
            <td><input id="productname1" name="productname1" maxlength="70" placeholder="Example: Ladies Striped Red Jumper" type="text" required> </td>
            <td><input id="price1" name="price1" size="3" maxlength="7" placeholder="18.99" style="margin-left: 2px" type="text" required>
            </td>
            <td><input id="category1" name="category1" size="16" maxlength="30" placeholder="Clothes/Jumpers" type="text">
            </td>
            <td><input id="quantity1" name="quantity1" size="3" maxlength="4" placeholder="21" type="text"><br>
            </td>
            <td style="width: 289.883px;"><textarea id="description1" name="description1" cols="39" rows="3" maxlength="2000" placeholder="This striped red jumper is perfect for cooler evenings and is made from 100% wool. Machine washable."></textarea><br>
            </td>
            <td style="width: 319.483px;">
			<span id="msg1"></span>
            <input type="file" id="file1" name="file" />
		    <div id="loader1" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td><input id="productname2" name="productname2" maxlength="70" type="text"> </td>
            <td><input id="price2" name="price2" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category2" name="category2" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity2" name="quantity2" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description2" name="description2" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
	  		  <span id="msg2"></span>
			  <input type="file" id="file2" name="file" /><br>
		     <div id="loader2" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>3</td>
            <td><input id="productname3" name="productname3" maxlength="70" type="text"> </td>
            <td><input id="price3" name="price3" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category3" name="category3" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity3" name="quantity3" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description3" name="description3" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			   <span id="msg3"></span>
			  <input type="file" id="file3" name="file" /><br>
		     <div id="loader3" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>4</td>
            <td><input id="productname4" name="productname4" maxlength="70" type="text"> </td>
            <td><input id="price4" name="price4" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category4" name="category4" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity4" name="quantity4" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description4" name="description4" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
		      <span id="msg4"></span>
			  <input type="file" id="file4" name="file" /><br>
		     <div id="loader4" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>5</td>
            <td><input id="productname5" name="productname5" maxlength="70" type="text"> </td>
            <td><input id="price5" name="price5" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category5" name="category5" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity5" name="quantity5" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description5" name="description5" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg5"></span>
			  <input type="file" id="file5" name="file" /><br>
		     <div id="loader5" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>6</td>
            <td><input id="productname6" name="productname6" maxlength="70" type="text"></td>
            <td><input id="price6" name="price6" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category6" name="category6" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity6" name="quantity6" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description6" name="description6" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg6"></span>
			  <input type="file" id="file6" name="file" /><br>
		     <div id="loader6" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>7</td>
            <td><input id="productname7" name="productname7" maxlength="70" type="text"> </td>
            <td><input id="price7" name="price7" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category7" name="category7" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity7" name="quantity7" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description7" name="description7" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg7"></span>
			  <input type="file" id="file7" name="file" /><br>
		     <div id="loader7" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>8</td>
            <td><input id="productname8" name="productname8" maxlength="70" type="text"> </td>
            <td><input id="price8" name="price8" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category8" name="category8" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity8" name="quantity8" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description8" name="description8" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg8"></span>
			  <input type="file" id="file8" name="file" /><br>
		     <div id="loader8" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>9</td>
            <td><input id="productname9" name="productname9" maxlength="70" type="text"> </td>
            <td><input id="price9" name="price9" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category9" name="category9" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity9" name="quantity9" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description9" name="description9" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg9"></span>
			  <input type="file" id="file9" name="file" /><br>
		     <div id="loader9" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>10</td>
            <td><input id="productname10" name="productname10" maxlength="70" type="text"> </td>
            <td><input id="price10" name="price10" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category10" name="category10" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity10" name="quantity10" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description10" name="description10" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg10"></span>
			  <input type="file" id="file10" name="file" /><br>
		     <div id="loader10" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>11</td>
            <td><input id="productname11" name="productname11" maxlength="70" type="text"> </td>
            <td><input id="price11" name="price11" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category11" name="category11" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity11" name="quantity11" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description11" name="description11" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg11"></span>
			  <input type="file" id="file11" name="file" /><br>
		     <div id="loader11" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>12</td>
            <td><input id="productname12" name="productname12" maxlength="70" type="text"> </td>
            <td><input id="price12" name="price12" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category12" name="category12" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity12" name="quantity12" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description12" name="description12" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg12"></span>
			  <input type="file" id="file12" name="file" /><br>
		     <div id="loader12" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>13</td>
            <td><input id="productname13" name="productname13" maxlength="70" type="text"> </td>
            <td><input id="price13" name="price13" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category13" name="category13" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity13" name="quantity13" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description13" name="description13" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg13"></span>
			  <input type="file" id="file13" name="file" /><br>
		     <div id="loader13" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>14</td>
            <td><input id="productname14" name="productname14" maxlength="70" type="text"> </td>
            <td><input id="price14" name="price14" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category14" name="category14" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity14" name="quantity14" size="3" maxlength="4" type="text"><br>
            </td>
            <td><textarea id="description14" name="description14" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg14"></span>
			  <input type="file" id="file14" name="file" /><br>
		     <div id="loader14" class="loader">Loading...</div>
            </td>
          </tr>
          <tr>
            <td>15</td>
            <td><input id="productname15" name="productname15" maxlength="70" type="text"> </td>
            <td><input id="price15" name="price15" size="3" maxlength="7" style="margin-left: 2px" type="text">
            </td>
            <td><input id="category15" name="category15" size="16" maxlength="30" type="text">
            </td>
            <td><input id="quantity15" name="quantity15" size="3" maxlength="4" type="text"><br>
            </td>
            <td style="text-align: center;"><textarea id="description15" name="description15" cols="39" rows="3" maxlength="2000"></textarea> </td>
            <td>
			  <span id="msg15"></span>
			  <input type="file" id="file15" name="file" /><br>
		     <div id="loader15" class="loader">Loading...</div>
            </td>
          </tr>
        </tbody>
      </table>
	  <input type="submit" name="buildcsv" value="Send Products">
    </form>
    <p><br>
    </p>
    
  </body>
</html>
