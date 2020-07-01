
$( "#resultsTable tbody td.details-control" ).trigger( "click" );


var emailSent="Email Sent";
var countEmailSent=$('.timeline-message').filter(function() {
    return $(this).text().indexOf(emailSent)>=0;
}).length;
console.log('Email Sent: ' +countEmailSent)

var wordOpened="WORD Opened"; 
var countWordOpened = $('.timeline-message').filter(function(){
    return $(this).text().indexOf(wordOpened)>=0;
}).length; 
console.log('Word opened: '+ countWordOpened);

var emailOpened="Email Opened";
var countEmailOpened = $('.timeline-message').filter(function(){
    return $(this).text().indexOf(emailOpened)>=0;
}).length; 
console.log('Email Opened: '+ countEmailOpened);

var clickedLink="Clicked Link";
var countClickedLink = $('.timeline-message').filter(function() {
    return $(this).text().indexOf(clickedLink)>=0;
}).length;
console.log('Clicked Link:' + countClickedLink);

var subData="Submitted Data";
var countSubData= $('.timeline-message').filter(function(){
    return $(this).text().indexOf(subData)>=0;
}).length;
console.log('Submitted Data: '+ countSubData);
$( "#resultsTable tbody td.details-control" ).trigger( "click" );

var resultCount = {
    a:countEmailSent,
    b:countEmailOpened,
    c:countClickedLink,
    d:countSubData,
    e:countWordOpened
}



