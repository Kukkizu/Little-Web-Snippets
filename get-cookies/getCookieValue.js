var cookieArr;
var cookieValue="";

  if(document.cookie.indexOf(";")>-1){
    cookieArr = document.cookie.split(";");
  }else{
    cookieArr = [document.cookie];
    if((cookieArr[0].slice(0,cookieArr[0].indexOf("="))=="cookieName")){
      cookieValue = cookieArr[0].slice(cookieArr[0].indexOf("=")+1);
    }
  }
  
console.log(cookieArr);
for(var i=0;i<cookieArr.length;i++){
  if((cookieArr[i].slice(0,cookieArr[i].indexOf("="))=="cookieName")){
    cookieValue = cookieArr[i].slice(cookieArr[i].indexOf("=")+1);

    //if cookieName exists then set cookie expiration date to the current date +1 month.
    
    var date = new Date();
    date.setMonth(date.getMonth() + 1);
    var expirationDate = date.toUTCString();
    document.cookie = `cookieName=${cookieValue}; expires=${expirationDate} path=/; secure`;
  }
}