$(document).ready(function() {
    var loginOutput = "";
  
  function loginError(){
    $("#emailLabel").text("Email - In");
    $("#passLabel").text("Password - ");
  }
  
  function formReset(){
   $("#emailLabel").text("Email");
   $("#passLabel").text("Password");
  }
  
  formReset();
  
  $("#submitInfo").click(function() {
    const email = $("#logEmail").val();
    const password = $("#logPassword").val();
    //alert(email + " " + password);
    $.ajax({
      url: 'https://www.website.com/php/login.php',
      method: 'POST',
      data: {
        email: email,
        password: password,
        submit: "login",
      },
      success: function(response) {
        alert("data submitted");
        console.log(response);
        console.log(JSON.stringify(response));

        loginOutput = response.trim();

        formReset();
  
        if(loginOutput=="xNF"){
            document.getElementById("loginFailed").style.display = "block";
            $("#loginFailed").show();
        }else{
            document.getElementById("loginFailed").style.display = "none";
            $("#loginFailed").hide();
            //console.log(response);
            //alert(response);
            // Create a new Date object
            var date = new Date();

            // Set the expiration date to one month from now
            date.setMonth(date.getMonth() + 1);

            // Convert the expiration date to a UTC string format
            var expirationDate = date.toUTCString();

            // Set the cookie with the desired name, value, and expiration date
            document.cookie = `sessionId=${response}; expires=${expirationDate} path=/; secure`;
            //console.log(document.cookie);
            
            window.location.href = "https://website.com/index.html";
        }
      }
    });
  });
  
});