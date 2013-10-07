<?php if(isset($_POST['submit'])) 
         {
          $name=$_POST['name'];
		  $company=$_POST['company'];
		  $from_user=$name;
		  $from_email=$_POST['email'];
		  
		  //$to="amit@webpathfinders.com";
		  $to=" info@associated-strategies.com";
		  
		  $subject="contact us";
		  
		  $message.="Name : ".$name."<br>";
		  $message.="Company : ".$company."<br>";
		  $message.="Mail ID :".$from_email."<br>";
          $message.="Message :".$_POST['comments'];
          
		  $headers = "From: $from_user <$from_email>\r\n". 
               "MIME-Version: 1.0" . "\r\n" . 
               "Content-type: text/html; charset=UTF-8" . "\r\n"; 
		  
	   $mail=  mail($to, $subject, $message, $headers);
          
		 if($mail)
          {
		 header('Location:http://dev.tradebooster.com/associated-strategies/about-us.html?sent=sucess');
		 }
		  
		  }
?>