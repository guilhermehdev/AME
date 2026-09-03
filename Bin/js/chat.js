if (Notification.permission === 'granted'){
    //notify();
} else if(Notification.permission !== 'denied') {
    Notification.requestPermission().then(permission => {
        console.log(permission);
    });
}

function notify(title, body){
    const notify = new Notification(title, {
        body: body
    });
}

//chat
let incomingMessages = false;
var timer = setInterval(checkNewMessages,2000);
clearInterval(timer);

$(document).ready(function () {   
//    let notications = messagesListener(); 
    
//    notications.forEach(function(i, v){ 
//        console.log(i, v);
//    });
});

$(window).on("beforeunload", function() { 
 
    $.ajax({
    type: 'POST',
    url: GLOBAL_URL+"Chat/updateUserLog",   
    data: {logged:"0"},
    async: true,
    success: function (res) {  
        console.log(res);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown);
        return;
    }})
    .done(function () {})
    .fail(function () {return;}); 
});

function autoScroll(){ 
   $("#chat-container").animate({ scrollTop: $('#chat-container')[0].scrollHeight - $('#chat-container')[0].clientHeight}, 10);
}

function pad(num, size) {
    var s = num + '';
    while (s.length < size) s = '0' + s;
    return s;
}

function getTimeFromDate(timestamp) {
    const dateTime = timestamp;

    let dateTimeParts= dateTime.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
    dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
    const dateObject = new Date(...dateTimeParts); // our Date object
    
    return pad(dateObject.getHours(),2)+":"+pad(dateObject.getMinutes(),2);
}

function getDateFromTimestamp(timestamp) {
    const dateTime = timestamp;
    
    Date.prototype.getMonthText = function() {
        var months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return months[this.getMonth()];
    }
   
    let dateTimeParts= dateTime.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
    dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
    const dateObject = new Date(...dateTimeParts); // our Date object
    day = pad(dateObject.getDate(),2);
    month = dateObject.getMonthText();
    year = dateObject.getFullYear();
    
    return day + " de " + month  + " de " + year ;
}

function openForm() {
  $('#chat').fadeIn();
  $('#slct-chat-user').val('');
  $('#chat-container').html('');
}

function closeForm() { 
  $('#chat').fadeOut();
  clearInterval(timer);
}

function sendMessage(obj, url, data){
    let parsedData = JSON.parse(data);
    const getUrl = obj.attr('get');
        
    $.ajax({
    type: 'POST',
    url: GLOBAL_URL+url,
    data: parsedData,
    success: function (result) {  
        //const addMsg = $("<div class='from-container'><div class='from-box'>"+result+"</div></div>");
        $('#no-messages').remove();        
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown);
        return;
    },
    })
    .done(function () {})
    .fail(function () {return;});     
}

function getMessages(url, from, to){         
    let lastBox = null;
    let prevDate = null;
    let todayClass = null;
    let todayText ='';
    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
    
    $.ajax({
    type: 'GET',
    url: GLOBAL_URL+url+'/'+from+'/'+to,
    async: true,
    cache: false,    

        success: function (result) {
            
            if(result !== null && result !== '' && result.length > 2){
                let data = JSON.parse(result); 
                                                                                                 
                    for(i in data) {  
                        let msg = data[i]['message'];                        
                        let cut = (msg.substring(0, 26));
                        let newmsg;
                        let deleteBtn = "<span style='font-size:8px;color:#ccc;cursor:pointer;' onclick='deleteMessage("+data[i]['id']+")'>X</span>";
                                                
                        if(cut == "<i>[mensagem excluída]</i>"){
                            newmsg = "<i>mensagem excluída</i>";
                            deleteBtn = "<div style='margin-top:5px;'></div>";
                        } else {
                            newmsg = msg;                           
                        }                       
                        
                        let box = data[i]['id_from'];                                
                        if(box === from){ 
                            box = 'from';                            
                        } else {
                            box = 'to';  
                            deleteBtn = "<div style='margin-top:5px;'></div>";
                        }
                        
                        const addMsg = $("<div class='"+box+"-container'><div class='"+box+"-box msg-box' name='msg-box' data-date='"+(data[i]['sent_on'])+"' id='"+data[i]['id']+"'><div style='text-align:right;'>"+deleteBtn+"</div>"+newmsg+"<i><div style='color:#e0e0e0;font-size:0.7em;text-align:right;'>"+getTimeFromDate(data[i]['sent_on'])+"</div></i></div></div>");
                        if(getDateFromTimestamp(data[i]['sent_on']) === getDateFromTimestamp(now)){
                            todayClass = 'hoje';
                            todayText = "Hoje";
                        } else {
                            todayClass = '';
                            todayText = getDateFromTimestamp(data[i]['sent_on']);
                        }
                                                
                        const dataContainer = "<div class='date-container'><div id='message-date' class='message-date "+todayClass+"'>"+todayText+"</div></div>";
                                               
                        if(prevDate === null){
                            $('#chat-container').append(dataContainer);                             
                        } else {
                            if(getDateFromTimestamp(prevDate) !== getDateFromTimestamp(data[i]['sent_on'])){
                                $('#chat-container').append(dataContainer);
                            }
                        }
                                                                        
                        $('#chat-container').append(addMsg.hide().fadeIn(150));
                        
                        lastBox = document.getElementById(getLastMessage());                       
                        prevDate = $(lastBox).data('date');
                          
                        autoScroll(); 
                        updateStatusMessages(from, data[i]['id_from'], data[i]['id']); 
                    }
                                                            
            } else {
                $('#chat-container').append("<div id='no-messages' style='color:#777;text-align:center;'><i>Nenhuma mensagem enviada...</i></div>"); 
            }            
            timer = setInterval(checkNewMessages,1000);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(errorThrown);
            //setTimeout(getMessages(url, from, to), /* Try again after.. */15000); /* milliseconds (15seconds) */ 
        }         
    }); 
    
}

function deleteMessage(id) { 
    $.ajax({
        type: 'POST',
        url: GLOBAL_URL+'Chat/deleteMessage',
        data: {"id":id},
        async: true,
        cache: false,         
        success: function (res) {
           $("#"+res).html("<i>mensagem excluída</i>");
        }
    }).done(checkNewMessages());
}

function getLastMessage(){
    var boxes = $(".msg-box");    
    var arr = [];
    for (var i = 0; i < boxes.length; i++) {
        arr.push(boxes[i]['id']);
    }            
    return Math.max.apply(Math, arr); 
}

function checkNewMessages(){
const obj = $("#btn-send-message");
const user = $("#slct-chat-user").val();
const url = obj.attr('get');
let params = eachParams((obj).data('params'));
let res = params.split('/');
const from = res[0];
const to = res[1];
let today = null;
          
    maxId = getLastMessage(); 
              
    $.ajax({
        type: 'GET',
        url: GLOBAL_URL+url+'/'+from+'/'+to,
        async: true, /* If set to non-async, browser shows page as "Loading.."*/
        cache: false,         
        success: function (result) {
                           
            if(result !== null && result !== '' && result.length > 2){
            let data = JSON.parse(result);
                                                
                for(i in data) {
                                                                                 
                    if(data[i]['id'] > maxId) { 
                        let deleteBtn = "<span style='font-size:8px;color:#ccc;cursor:pointer;' onclick='deleteMessage("+data[i]['id']+")'>X</span>";
                                         
                        //let box = data[i]['id_from'] === from ? 'from' : 'to';
                        
                        let box = data[i]['id_from'];                                
                        if(box === from){ 
                            box = 'from';                            
                        } else {
                            box = 'to';  
                            deleteBtn = "<div style='margin-top:5px;'></div>";
                        }

                        const addMsg = $("<div class='"+box+"-container'><div class='"+box+"-box msg-box' name='msg-box' id='"+data[i]['id']+"'><div style='text-align:right;'>"+deleteBtn+"</div>"+data[i]['message']+"<i><div style='color:#e0e0e0;font-size:0.7em;text-align:right;'>"+getTimeFromDate(data[i]['sent_on'])+"</div></i></div></div>"); 
                                               
                        today = document.getElementsByClassName('hoje');
                                                        
                        if(today[0] === undefined){
                             const dataContainer = "<div class='date-container hoje'><div id='message-date' class='message-date'>Hoje</div></div>";
                            $('#chat-container').append(dataContainer);                             
                        }                     
                        
                        $('#chat-container').append(addMsg.hide().fadeIn(150));
                        autoScroll();
                        updateStatusMessages(from, data[i]['id_from'], data[i]['id']);
                        
                        $("#slct-chat-user option[value='" + user + "']").html().replace(" &nbsp;&nbsp;&nbsp;&nbsp;&#xf075;","");
                        
                    }
                } 
            } else {
               return;
            }
        }
    });  
}  

function updateStatusMessages(from, to, id){
    if (from !== to) {
        parsedId = JSON.parse('{"id":"'+id+'","checked":"1"}');

        $.ajax({
            type: 'POST',
            url: GLOBAL_URL+"Chat/updateMessage",
            data: parsedId,
            async: true, 
            cache: false,
            success: function () {               
                $('#chat').removeClass('yellow-glow');
                $('#slct-chat-user').removeClass('yellow-glow-border');
                $('#open-chat').removeClass('yellow-glow'); 
                clearInterval(messagesListener);
                //messagesListener();
            }
        });
    }    
}

var messagesListener = function(){ 
    let sender = {};
    let messages = [];
    
    $.ajax({
        type: 'GET',
        url: 'http://'+location.hostname+'/Gerenciador/AME/Chat/getMessages/'+$("#user").data('id'),
        async: true,
        cache: false,      
        success: function (result) {
            let data = JSON.parse(result);  
            let selects = [];
            let arrOfMessages = [];
            let filteredUsers = []; 
           
            $("#slct-chat-user option").each(function() {
                selects.push($(this).val());
            }); 
                                    
            data.forEach(function(i, val){ 
                arrOfMessages.push(i['id_from']);              
                messages.push(i['message']);
            });
            
            filteredUsers = arrOfMessages.filter((item,index)=> arrOfMessages.indexOf(item) === index);
                                         
            if(filteredUsers.length > 0) {
                               
                incomingMessages = true;
                filteredUsers.forEach(function(i){
                    var select = $("#slct-chat-user");
                    var username = $("#slct-chat-user option[value='" + i + "']").html(); 
                    
                    sender = JSON.parse('{"sender" : "'+ username +'", "msg" : "'+ messages +'"}');  

                    if(username.indexOf(" &nbsp;&nbsp;&nbsp;&nbsp;") >= 0){
                        
                    } else { 
                        if(select.val() !== i) {
                            $("#slct-chat-user option[value='" + i + "']").html(username+" &nbsp;&nbsp;&nbsp;&nbsp;&#xf075;");                             
                        }
                    }
                    select.addClass('yellow-glow-border');
                    $('#chat').addClass('yellow-glow');
                    $('#open-chat').addClass('yellow-glow');
                    $('#slct-chat-user').addClass('yellow-glow-border');
                });
            } else {
                incomingMessages = false;
                $('#chat').removeClass('yellow-glow');
                $('#slct-chat-user').removeClass('yellow-glow-border');
                $('#open-chat').removeClass('yellow-glow');
            }
        }
    });
    return sender;
};

//window.setInterval('messagesListener()',10000);

function IsUserLoggedIn() {
    var lsValue = true;
    $.ajax({
        type: "Post",
        url: GLOBAL_URL+"Chat/IsUserLoggedIn",
        datatype: "json",
        async: false,
        success: function (foData) {
            if (foData == "true")
                lsValue = true;
            else
                lsValue = false;
        },
        error: function (xhr, ajaxOptions, thrownError) {
        },
        complete: function (e) {
        }
    });
    return lsValue;
}

$(document).on('change', '#slct-chat-user', function () {
    const value = $(this).val();
    
    if(value !== ''){
        const obj = $("#btn-send-message"); 
        const selectedUser = $(this).val();
        const url = obj.attr('get');
        let params = eachParams((obj).data('params'));
        let res = params.split('/');
        const from = res[0];
        const to = res[1];        
        
        $('#chat-container').html('');        
        $("#chat-input").prop("disabled",false).focus();
        $("#btn-send-message").prop("disabled",false);        
        clearInterval(timer);
       // setTimeout(getMessages(url, from, to),300);                        
                     
        const user = $("#slct-chat-user option[value='" + value + "']").html().replace(" &nbsp;&nbsp;&nbsp;&nbsp;", '');
                        
        $("#slct-chat-user option[value='" + value + "']").html(user);
        
    } else {
        clearInterval(timer);
        $("#chat-input").prop("disabled",true).val('');
        $("#btn-send-message").prop("disabled",true);
        $('#chat-container').html("<div style='color:#777;text-align:left;'><i>Selecione um usuário</i></div>");
    }
});

$(document).on('click', '#btn-send-message', function () {
    const input = $("#chat-input");
    
    if(input.val().trim() === ''){
        input.val('').focus();
        return false;
    }
    
    let params = eachParams($(this).data('params'));
    let res = params.split('/');
    const from = res[0];
    const to = res[1];
    const sendMsg = input.val();
    const sendUrl = $(this).attr('send'); 
       
    sendMessage($(this), sendUrl, '{"msg":"'+sendMsg+'","from":"'+from+'","to":"'+to+'"}'); 
    input.val('').focus();
});