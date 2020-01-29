<style>
.chat{
	margin-top: auto;
	margin-bottom: auto;
}


.card_chat{
	height: 500px !important;
	border-radius: 15px !important;
	position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
	border: 1px solid #e5e9f2;
	-webkit-box-orient: vertical;
    -webkit-box-direction: normal;
}
.contacts_body{
	background-color: white !important;
	padding:  0.75rem 0 !important;
	overflow-y: auto;
	white-space: nowrap;
}
.msg_card_body{
	background: #f5f6fa;
	overflow-y: auto;
}
.card-header{
	border-radius: 15px 15px 0 0 !important;
	border-bottom: 0 !important;
}
.card-footer{
border-radius: 0 0 15px 15px !important;
	border-top: 0 !important;
	background: #f1f1f1 !important;
}
.container{
	align-content: center;
}

.type_msg{
	background-color: white !important;
	border:0 !important;
	color:black !important;
	height: 60px !important;
	overflow-y: auto;
}
	.type_msg:focus{
	color : black !important;	
		box-shadow:none !important;
	outline:0px !important;
}
.attach_btn{
border-radius: 15px 0 0 15px !important;
background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.send_btn{
border-radius: 0 15px 15px 0 !important;
background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.search_btn{
	border-radius: 0 15px 15px 0 !important;
	background-color: rgba(0,0,0,0.3) !important;
	border:0 !important;
	color: white !important;
	cursor: pointer;
}
.contacts{
	list-style: none;
	padding: 0;
}
.contacts li{
	width: 100% !important;
	padding: 5px 10px;
	margin-bottom: 15px !important;
}
.active_chat{
	background-color: rgba(0,0,0,0.3);
}
.user_img{
	height: 40px;
	width: 40px;
	border:1.5px solid #f5f6fa;

}
.user_img_msg{
	height: 40px;
	width: 40px;
	border:1.5px solid #f5f6fa;

}
.img_cont{
	position: relative;
	height: 40px;
	width: 40px;
}
.img_cont_msg{
	height: 40px;
	width: 40px;
}
.online_icon{
position: absolute;
height: 15px;
width:15px;
background-color: #4cd137;
border-radius: 50%;
bottom: 0.2em;
right: 0.4em;
border:1.5px solid white;
}
.offline{
background-color: #c23616 !important;
}
.user_info{
margin-top: auto;
margin-bottom: auto;
margin-left: 15px;
}
.user_info span{
font-size: 14px;
color: rgba(0, 0, 0, 0.5);
}
.user_info p{
font-size: 10px;
color: currentColor;
}
.video_cam{
margin-left: 50px;
margin-top: 5px;
}
.video_cam span{
color: white;
font-size: 20px;
cursor: pointer;
margin-right: 20px;
}
.msg_cotainer{
margin-top: auto;
margin-bottom: auto;
margin-left: 10px;
border-radius: 25px;
background-color: #f1f1f1;
padding: 10px;
position: relative;
}
.msg_cotainer_send{
margin-top: auto;
margin-bottom: auto;
margin-right: 10px;
border-radius: 25px;
background-color: #f1f1f1;
padding: 10px;
position: relative;
}
.msg_time{
position: absolute;
left: 0;
bottom: -15px;
color: rgba(0, 0, 0, 0.9);
font-size: 10px;
}
.msg_time_send{
position: absolute;
right:0;
bottom: -15px;
color: rgba(255,255,255,0.5);
font-size: 10px;
}
.msg_head{
position: relative;
}
#action_menu_btn{
position: absolute;
right: 10px;
top: 10px;
color: white;
cursor: pointer;
font-size: 20px;
}
.action_menu{
z-index: 1;
position: absolute;
padding: 15px 0;
background-color: rgba(0,0,0,0.5);
color: white;
border-radius: 15px;
top: 30px;
right: 15px;
display: none;
}
.action_menu ul{
list-style: none;
padding: 0;
margin: 0;
}
.action_menu ul li{
width: 100%;
padding: 10px 15px;
margin-bottom: 5px;
}
.action_menu ul li i{
padding-right: 10px;

}
.action_menu ul li:hover{
cursor: pointer;
background-color: rgba(0,0,0,0.2);
}
.new_message_icon{
height: 15px;
width: 15px;
background-color: skyblue;
border-radius: 50%;
bottom: 0.2em;
right: 0.4em;
border: 1.5px solid white;
}
@media(max-width: 576px){
.contacts_card{
margin-bottom: 15px !important;
}
}

.chat-button-float{
	position: fixed;
    top: 1em;
    right: 2em;
    z-index: 19999;
}
.customer-info{
	overflow-y: scroll;
	max-height: 500px;
}
.chat-righbox{
	background-color: rgb(255, 255, 255);
	margin-top: 0;
	margin-bottom: 20px;
	font-size: 14px;
	line-height: 16px;
	border-width: 1px;
	border-style: solid;
	border-color: rgb(221, 226, 230);
	border-image: initial;
	border-radius: 8px;
	padding: 18px 18px 10px;
	overflow: hidden;
}
.chat-righbox .title{
	font-size: 18px;
	line-height: 24px;
	color: rgb(66, 77, 87);
	font-weight: 600;
	white-space: nowrap;
	text-overflow: ellipsis;
	width: 100%;
	overflow: hidden;
	padding-bottom: 10px;
}
.chat-righbox .name{
	display: flex;
	-webkit-box-pack: start;
	justify-content: flex-start;
	line-height: 18px;
}
.chat-righbox .name .inital-wrapper{
	min-width: 40px;
    min-height: 40px;
    margin: 4px 0px;
}
.chat-righbox .name .inital{
	display: block;
    color: rgb(255, 255, 255);
    overflow: hidden;
    border-radius: 50%;
    background: rgb(57, 76, 130);
    height: 40px;
    text-align: center;
    line-height: 40px;
}
.chat-righbox .name .fname-wrapper{
	display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    flex-direction: column;
    margin-left: 10px;
    color: rgb(66, 77, 87);
    flex: 1 1 0%;
    padding: 4px 0px;
}
.chat-righbox .name .fullname{
	font-weight: bold;
    white-space: pre-wrap;
    word-break: break-word;
}
.chat-righbox .name .email{
	white-space: pre-wrap;
    word-break: break-word;
}
.chat-righbox .time{
	padding: 5px 0;
}
.chat-righbox .location{
	padding: 5px 0;
}
.chat-righbox .mapouter{
	position: relative;
	text-align: right;
	height: 250px;
	width: 100%;
}
.chat-righbox .gmap_canvas{
	overflow:hidden;
	background: none!important;
	height: 250px;
	width: 100%;
}
.chat-righbox .line-spacing{
	line-height: 150%;
}
.typing-indicator{
	background-color: #f5f6fa;
	padding: 5px 5px 5px 15px;
	font-style: italic;
	font-size: 12px;
	height:25px;
}
.user_inital{
	display: block;
    background-color: #ddd;
    text-align: center;
    line-height: 40px;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    height: 40px;
    width: 40px;
}
.online_icon{
	right: -0.3em !important;
}
</style>
<script src="https://cdn.livechatinc.com/accounts/accounts-sdk.min.js"></script>
<script>

var accessToken = '';
var websocket = false;
var wsUri = "wss://api.livechatinc.com/v3.1/agent/rtm/ws";
const instance = AccountsSDK.init({
	client_id: "babb4c2a30a83d68daf0ffc271ef1748",
	// response_type: "code",
	onIdentityFetched: (error, data) => {
		if (error){
			//console.log(error)
		} 
		if (data) {
			//console.log("User authorized!");
			// console.log(data);
			accessToken = data.access_token;
			setTimeout(instance, data.expires_in);
			//console.log("License number: " + data.license);
		}
	}
});

function runWebSocket(chatId) {
	websocket = new WebSocket(wsUri);

	websocket.onopen = function(evt) {
		pingSock();
		websocket.send('{ "action": "login", "payload": { "token": "Bearer ' + accessToken + '" }}');
	};

	websocket.onmessage = function(evt) {
		var evtDat = JSON.parse(evt.data);
 
		if(evtDat.action == 'login' && evtDat.type == 'response' && evtDat.success == true){
			websocket.send('{ "action": "send_typing_indicator", "payload": { "chat_id": "' +chatId + '", "is_typing": true }}');
		}
		if(evtDat.action == 'send_typing_indicator' && evtDat.type == 'response' && evtDat.success == true){
			//user is typing.. show status in chat box
			//$('#typing-indicator').html('typing...').delay(1000).html('');
		}
		if(evtDat.action == 'incoming_sneak_peek' && evtDat.type == 'push'){
			//user is typing.. show status in chat box
			$('#typing-indicator').html('typing...');
			setTimeout( function() { $('#typing-indicator').html('') }, 1000);
		}
	};
}

var pingTimerObj = false;
function pingSock() {
	if (!websocket) return;
	if (websocket.readyState !== 1) return;
	websocket.send('{ "action": "ping" }');
	pingTimerObj = setTimeout(pingSock, 15000); //ping every 15 seconds
}

var currentChatId = 0;
var chatTimerObj = false;
function openChatBox(show){
	if(show){
		//open socket
		if(currentChatId != 0){
			runWebSocket(currentChatId);
		}

		getChatsWithoutRefresh();
		getUserList();
		chatTimerObj = setInterval(function(){
			getChatsWithoutRefresh();
			getUserList();
		}, 5000);
	}
	else{
		clearInterval(chatTimerObj);
		clearInterval(pingTimerObj);

		// Close the connection, if open.
		if (websocket.readyState === WebSocket.OPEN) {
			websocket.close();
		}
	}
}

$(window).on("blur focus", function(e) {
    var prevType = $(this).data("prevType");
    if (prevType != e.type) {
        switch (e.type) {
            case "blur":
            	if(chatBoxOpen){
            		openChatBox(false);
            	}
                break;
            case "focus":
            	if(chatBoxOpen){
            		openChatBox(true);
            	}
                break;
        }
    }

    $(this).data("prevType", e.type);
})

function formatAMPM(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var ampm = hours >= 12 ? 'pm' : 'am';
	hours = hours % 12;
	hours = hours ? hours : 12; // the hour '0' should be '12'
	minutes = minutes < 10 ? '0'+minutes : minutes;
	var strTime = hours + ':' + minutes + ' ' + ampm;
	return strTime;
}

function customerInfoSetter(customerInfo){
	var customerLocation = [];
	var lastVisited = customerInfo.last_visit;
	if(lastVisited.geolocation.city != ''){
		customerLocation.push(lastVisited.geolocation.city);
	}
	if(lastVisited.geolocation.region != ''){
		customerLocation.push(lastVisited.geolocation.region);
	}
	if(lastVisited.geolocation.country != ''){
		customerLocation.push(lastVisited.geolocation.country);
	}
	var customerInfoHTML = '<div class="name"><div class="inital-wrapper"><span class="inital">L</span></div><div class="fname-wrapper"><div class="fullname">' + customerInfo.name + '</div><div class="email">' + customerInfo.email + '</div></div></div>';

	var atSeenAtUTC = new Date(customerInfo.customer_last_event_created_at);

	customerInfoHTML += '<div class="time"><svg fill="#9c999d" width="16px" height="16px" viewBox="0 0 14 14"><g><path fill="inherit" d="M6.993.333A6.663 6.663 0 0 0 .333 7c0 3.68 2.98 6.667 6.66 6.667A6.67 6.67 0 0 0 13.667 7 6.67 6.67 0 0 0 6.993.333zm.007 12A5.332 5.332 0 0 1 1.667 7 5.332 5.332 0 0 1 7 1.667 5.332 5.332 0 0 1 12.333 7 5.332 5.332 0 0 1 7 12.333z"></path><path fill="inherit" d="M7.333 3.667h-1v4l3.5 2.1.5-.82-3-1.78z"></path></g></svg> ' + formatAMPM(atSeenAtUTC) + ' local time</div>';
	customerInfoHTML += '<div class="location"><svg fill="#9c999d" width="16px" height="16px" viewBox="0 0 10 14"><path fill="inherit" d="M5 .333A4.663 4.663 0 0 0 .333 5C.333 8.5 5 13.667 5 13.667S9.667 8.5 9.667 5A4.663 4.663 0 0 0 5 .333zm0 6.334a1.667 1.667 0 1 1 .001-3.335A1.667 1.667 0 0 1 5 6.667z"></path></svg> ' + customerLocation.join(', ') + '</div>';
	customerInfoHTML += '<div class="mapouter"><div class="gmap_canvas" id="gmap"><iframe width="100%" height="250" id="gmap_canvas" src="https://maps.google.com/maps?q=' + customerLocation.join(', ') + '&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div></div>';

	$('#chatCustomerInfo').html(customerInfoHTML);

	var lastVisitedPages = lastVisited.last_pages;
	var lastVisitedPageHTML = '';
	if(lastVisitedPages.length > 0){
		$.each(lastVisitedPages, function(k,v){
			lastVisitedPageHTML += '<li>' + v.title + '<br><a href="' + v.url + '" target="_blank" style="color: #337ab7">' + v.url + '</a></li>';
		});
		lastVisitedPageHTML = '<ul>' + lastVisitedPageHTML + '</ul>';
	}
	$('#chatVisitedPages').html(lastVisitedPageHTML);
	$('#chatAdditionalInfo').html('Visits: ' + customerInfo.statistics.visits_count + '<br>Chats: ' + customerInfo.statistics.threads_count);
	$('#chatTechnology').html('IP address: ' + lastVisited.ip + '<br>User agent: ' + lastVisited.user_agent);
}

function getCustomerInfoOnLoad(){
	$('#chatCustomerInfo').html('Fetcing Details...');
	$('#chatVisitedPages').html('Fetcing Details...');
	$('#chatAdditionalInfo').html('Fetcing Details...');
    $('#chatTechnology').html('Fetcing Details...');
	$.ajax({
		url: "{{ route('livechat.customer.info') }}",
		type: 'GET',
		dataType: 'json',
		data: { _token: "{{ csrf_token() }}" },
	})
	.done(function(data) {
		var customerInfo = data.customerInfo;
		if(customerInfo!=''){
			customerInfoSetter(customerInfo);
		}
		else{
			$('#chatCustomerInfo').html('');
	    	$('#chatVisitedPages').html('');
	    	$('#chatAdditionalInfo').html('');
	    	$('#chatTechnology').html('');
		}

		if(data.threadId != ''){
			currentChatId = data.threadId;
		}
	})
	.fail(function() {
		$('#chatCustomerInfo').html('');
		$('#chatVisitedPages').html('');
		$('#chatAdditionalInfo').html('');
		$('#chatTechnology').html('');
	});
}

getCustomerInfoOnLoad();

function getChats(id){

	// Close the connection, if open.
	if (websocket.readyState === WebSocket.OPEN) {
		clearInterval(pingTimerObj);
		websocket.close();
	}

	$('#chatCustomerInfo').html('Fetcing Details...');
	$('#chatVisitedPages').html('Fetcing Details...');
	$('#chatAdditionalInfo').html('Fetcing Details...');
    $('#chatTechnology').html('Fetcing Details...');
	$.ajax({
    	url: "{{ route('livechat.get.message') }}",
    	type: 'POST',
    	dataType: 'json',
    	data: { id : id ,   _token: "{{ csrf_token() }}" },
    })
    .done(function(data) {
    	//if(typeof data.data.message != "undefined" && data.length > 0 && data.data.length > 0) {
	        $('#message-recieve').empty().html(data.data.message);
	        $('#message-id').val(data.data.id);
			$('#new_message_count').text(data.data.count);
			$('#user_name').text(data.data.name);
			$("li.active").removeClass("active");
			$("#user"+data.data.id).addClass("active");
			$('#user_inital').text(data.data.customerInital);

			var customerInfo = data.data.customerInfo;
			if(customerInfo!=''){
				customerInfoSetter(customerInfo);
			}
			else{
				$('#chatCustomerInfo').html('');
				$('#chatVisitedPages').html('');
				$('#chatAdditionalInfo').html('');
				$('#chatTechnology').html('');
			}

			currentChatId = data.data.threadId;

			//open socket
			runWebSocket(data.data.threadId);
			
    	//}
        console.log("success");
    })
    .fail(function() {
		console.log("error");
		$('#chatCustomerInfo').html('');
    	$('#chatVisitedPages').html('');
    	$('#chatAdditionalInfo').html('');
    	$('#chatTechnology').html('');
    });
}

function getChatsWithoutRefresh(){
	var scrolled=0;
	$.ajax({
		url: "{{ route('livechat.message.withoutrefresh') }}",
		type: 'POST',
		dataType: 'json',
		data: { _token: "{{ csrf_token() }}" },
	})
	.done(function(data) {
		 $('#message-recieve').empty().html(data.data.message);
		 $('#message-id').val(data.data.id);
		 getLanguage(data.data.id);
		 $('#new_message_count').text(data.data.count);
		 $('#user_name').text(data.data.name);
		 $("li .active").removeClass("active");
		 $("#user"+data.data.id).addClass("active");
		 $('#user_inital').text(data.data.customerInital);
		 scrolled=scrolled+300;
         $(".cover").animate({
			scrollTop:  scrolled
		 });
		console.log(data);
	})
	.fail(function() {
		console.log("error");
	});
}

function getLanguage(customerId) {
	$.ajax({
		url: "{{ route('livechat.customer.language') }}",
		type: 'GET',
		dataType: 'json',
		data: { id:customerId, _token: "{{ csrf_token() }}" },
	})
	.done(function(res) {
		if(res.data.language) {
			$('.selectedValue option[value="' + res.data.language + '"]').prop('selected', true);
		} else {
			$('.selectedValue option[value=""]').prop('selected', true);
		}
	})
}

function getUserList(){
	$.ajax({
		url: "{{ route('livechat.get.userlist') }}",
		type: 'POST',
		dataType: 'json',
		data: { _token: "{{ csrf_token() }}" },
	})
	.done(function(data) {
		 $('#customer-list-chat').empty().html(data.data.message);
		 $('#new_message_count').text(data.data.count);
		 console.log(data);
	})
	.fail(function() {
		console.log("error");
	});

}

function sendMessage(){
    id = $('#message-id').val();
	message = $('#message').val();
	var scrolled=0;
    $.ajax({
    	url: "{{ route('livechat.send.message') }}",
    	type: 'POST',
    	dataType: 'json',
    	data: { id : id ,
			message : message,
		   _token: "{{ csrf_token() }}" 
		   },
    })
    .done(function(data) {
       console.log(data);
		chat_message = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer">'+message+'<span class="msg_time"></span></div></div>'; //<div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
		$('#message-recieve').append(chat_message);
		$('#message').val('');
		scrolled=scrolled+300;
        $(".cover").animate({
			scrollTop:  scrolled
		});
	})
    .fail(function() {
    	alert('Chat Not Active');
    });
}
//Send File
// function sendFile(){
//     id = $('#message-id').val();
// 	file = $('#imgupload').prop('files')[0];
// 	var fd = new FormData();
// 		fd.append("id", id);
// 		fd.append("file", file);
// 		fd.append("_token", "{{ csrf_token() }}" );
// 	var scrolled=0;
//     $.ajax({
//     	url: "{{ route('livechat.send.file') }}",
//     	type: 'POST',
//     	dataType: 'json',
//     	data: fd,
// 		cache: false,
//         contentType: false,
//         processData: false   
//     })
//     .done(function(data) {
//        console.log(data);
// 		chat_message = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'+message+'<span class="msg_time"></span></div></div>';
// 		$('#message-recieve').append(chat_message);
// 		$('#message').val('');
// 		scrolled=scrolled+300;
//         $(".cover").animate({
// 			scrollTop:  scrolled
// 		});
// 	})
//     .fail(function() {
//     	alert('Chat Not Active');
//     });
// }


// function sendImage() {
// 	$('#imgupload').trigger('click');
// }




function sendImage() {
	$('#imgupload').trigger('click');
}

$(document).ready(function() {
	$('#imgupload').on('change', function(){
	    id = $('#message-id').val();
		file = $('#imgupload').prop('files')[0];
		var fd = new FormData();
			fd.append("id", id);
			fd.append("file", file);
			fd.append("_token", "{{ csrf_token() }}" );
		var scrolled=0;
	    $.ajax({
	    	url: "{{ route('livechat.upload.file') }}",
	    	type: 'POST',
	    	dataType: 'json',
	    	data: fd,
			cache: false,
	        contentType: false,
	        processData: false,
	        success: function(data){
	        	//alert(data);
				// chat_message = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'+data.filename+' uploaded<br><a href="'+data.fileCDNPath+'" target="_blank">'+data.fileCDNPath+'</a><span class="msg_time"></span></div></div>';
				// $('#message-recieve').append(chat_message);
				// //$('#message').val('');
				// scrolled=scrolled+300;
				// $(".cover").animate({
				// 	scrollTop:  scrolled
				// });	        	
	        }
	    });
	});
});

</script>
