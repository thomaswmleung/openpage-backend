'use strict';

var isChannelReady = false;
var isInitiator = false;
var isStarted = false;
var localStream;
var pc;
var remoteStream;
var turnReady;

var pcConfig = {
    'iceServers': [{
            'urls': 'stun:stun.l.google.com:19302'
        }]
};

// Set up audio and video regardless of what devices are present.
var sdpConstraints = {
    offerToReceiveAudio: true,
    offerToReceiveVideo: true
};

/////////////////////////////////////////////

//var room = 'foo';
// Could prompt for room name:
// room = prompt('Enter room name:');

var socket = io.connect("http://104.199.139.147:8080");

//if (room !== '') {
//  socket.emit('create or join', room);
//  console.log('Attempted to create or join room from peer 1', room);
//}

socket.on('created', function (room) {
    console.log('Created room 1 for peer 1' + room);
//  console.log('>>>>>>>>Initiator<<<<<<<<<<<<<<');

});

socket.on('full', function (room) {
    console.log('Room for peer 1 ' + room + ' is full');
});

socket.on('join', function (room) {
    console.log('Another peer made a request to join room  peer 1' + room);
    console.log('This peer is the initiator of room  peer 1' + room + '!');
    isChannelReady = true;
});

socket.on('joined', function (room) {
    console.log('joined peer 1: ' + room);
    isChannelReady = true;
});

socket.on('log', function (array) {
    console.log.apply(console, array);
});

////////////////////////////////////////////////

function sendMessage(message) {
    console.log('Client sending message peer 1: ', message);
    socket.emit('message', message);
}

// This client receives a message
socket.on('message', function (message) {
    console.log('Client received message peer 1:', message);
    if (message.type === 'call_invite') {

        $('#recieve_section').show();
        $('#caller_info').html(message.caller_name + " is calling...");

    }
    if (message.type === 'call_accepted') {

        maybeStart();
//        $('#recieve_section').show();
//        $('#caller_info').html(message.caller_name + " is calling...");

    }
//    if (message === 'got user media') {
//        maybeStart();
//    } 
    else if (message.type === 'offer') {
        if (!isInitiator && !isStarted) {
            maybeStart();
        }
        if (pc != null) {
            pc.setRemoteDescription(new RTCSessionDescription(message));
            doAnswer();
        }
    } else if (message.type === 'answer' && isStarted) {
        pc.setRemoteDescription(new RTCSessionDescription(message));
    } else if (message.type === 'candidate' && isStarted) {
        var candidate = new RTCIceCandidate({
            sdpMLineIndex: message.label,
            candidate: message.candidate
        });
        pc.addIceCandidate(candidate);
    } else if (message === 'bye' && isStarted) {
        handleRemoteHangup();
    }
});

////////////////////////////////////////////////////

var localVideo = document.querySelector('#localVideo');
var remoteVideo = document.querySelector('#remoteVideo');

//navigator.mediaDevices.getUserMedia({
//  audio: false,
//  video: true
//})
//.then(gotStream)
//.catch(function(e) {
//  alert('getUserMedia() error: ' + e.name);
//});

function gotStream(stream) {
    console.log('Adding local stream peer 1.' + socket);
    localVideo.src = window.URL.createObjectURL(stream);
    localStream = stream;

    var call_invitee_id = $('#hidden_call_invitee_id').val();
    var candidate_id = $('#hidden_candidate_id').val();
    

    if (isInitiator != true) {
      
        var msg = {
            target_id: call_invitee_id,
            from_id: candidate_id,
            type: "call_accepted"

        };
        socket.emit("global_message", msg);
        isChannelReady = true;
        maybeStart();
    } else {
        var recruiter_id = $('#hidden_recruiter_id').val();
        var candidate_id = $('#hidden_candidate_id').val();
        var msg = {
            target_id: recruiter_id,
            from_id: candidate_id,
            schedule_id: $('#schedule_id').val(),
            info: "Recruiter Calling...",
            type: "call_invite"

        }

        socket.emit("global_message", msg);

    }


//                sendMessage({
//                    type: 'call_accepted',
//                    caller_name: "Sunil"
//                });


//    sendMessage('got user media');

//    if (isInitiator) {
//        maybeStart();
//    }
}

var constraints = {
    video: true
};

console.log('Getting user media with constraints peer 1', constraints);

if (location.hostname !== 'localhost') {
    requestTurn(
            'https://computeengineondemand.appspot.com/turn?username=41784574&key=4080218913'
            );
}

function maybeStart() {
    console.log('>>>>>>> maybeStart() ', isStarted, localStream, isChannelReady);
    if (!isStarted && typeof localStream !== 'undefined' && isChannelReady) {
        console.log('>>>>>> creating peer connection');
        createPeerConnection();
        pc.addStream(localStream);
        isStarted = true;
        console.log('isInitiator peer 1', isInitiator);
        if (isInitiator) {
            doCall();
        }
    }
}

window.onbeforeunload = function () {
    sendMessage('bye');
};

/////////////////////////////////////////////////////////

function createPeerConnection() {
    try {
        pc = new RTCPeerConnection(null);
        pc.onicecandidate = handleIceCandidate;
        pc.onaddstream = handleRemoteStreamAdded;
        pc.onremovestream = handleRemoteStreamRemoved;
        console.log('Created RTCPeerConnnection peer 1');
    } catch (e) {
        console.log('Failed to create PeerConnection, exception peer 1: ' + e.message);
        alert('Cannot create RTCPeerConnection object.');
        return;
    }
}

function handleIceCandidate(event) {
    console.log('icecandidate event peer 1: ', event);
    if (event.candidate) {
        sendMessage({
            type: 'candidate',
            label: event.candidate.sdpMLineIndex,
            id: event.candidate.sdpMid,
            candidate: event.candidate.candidate
        });
    } else {
        console.log('End of candidates peer 1.');
    }
}

function handleRemoteStreamAdded(event) {
    console.log('Remote stream added peer 1.');
    remoteVideo.src = window.URL.createObjectURL(event.stream);
    remoteStream = event.stream;
    alert("added");
    
    
}

function handleCreateOfferError(event) {
    console.log('createOffer() error: ', event);
}

function doCall() {
    console.log('Sending offer to peer peer 1');
    pc.createOffer(setLocalAndSendMessage, handleCreateOfferError);
}

function doAnswer() {
    console.log('Sending answer to peer peer 1.');
    pc.createAnswer().then(
            setLocalAndSendMessage,
            onCreateSessionDescriptionError
            );
}

function setLocalAndSendMessage(sessionDescription) {
    // Set Opus as the preferred codec in SDP if Opus is present.
    //  sessionDescription.sdp = preferOpus(sessionDescription.sdp);
    pc.setLocalDescription(sessionDescription);
    console.log('setLocalAndSendMessage sending message peer 1', sessionDescription);
    sendMessage(sessionDescription);
}

function onCreateSessionDescriptionError(error) {
    trace('Failed to create session description: ' + error.toString());
}

function requestTurn(turnURL) {
    var turnExists = false;
    for (var i in pcConfig.iceServers) {
        if (pcConfig.iceServers[i].url.substr(0, 5) === 'turn:') {
            turnExists = true;
            turnReady = true;
            break;
        }
    }
    if (!turnExists) {
        console.log('Getting TURN server from ', turnURL);
        // No TURN server. Get one from computeengineondemand.appspot.com:
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var turnServer = JSON.parse(xhr.responseText);
                console.log('Got TURN server: ', turnServer);
                pcConfig.iceServers.push({
                    'url': 'turn:' + turnServer.username + '@' + turnServer.turn,
                    'credential': turnServer.password
                });
                turnReady = true;
            }
        };
        xhr.open('GET', turnURL, true);
        xhr.send();
    }
}

function handleRemoteStreamAdded(event) {
    console.log('Remote stream added peer 1.');
    remoteVideo.src = window.URL.createObjectURL(event.stream);
    remoteStream = event.stream;
}

function handleRemoteStreamRemoved(event) {
    console.log('Remote stream removed. Event peer 1: ', event);
}

function hangup() {
    console.log('Hanging up peer 1.');
    stop();
    sendMessage('bye');
}

function handleRemoteHangup() {
    console.log('Session terminated peer 1.');
    stop();
    isInitiator = false;
}

function stop() {
    isStarted = false;
    // isAudioMuted = false;
    // isVideoMuted = false;
    pc.close();
    pc = null;
}

///////////////////////////////////////////

// Set Opus as the default audio codec if it's present.
function preferOpus(sdp) {
    var sdpLines = sdp.split('\r\n');
    var mLineIndex;
    // Search for m line.
    for (var i = 0; i < sdpLines.length; i++) {
        if (sdpLines[i].search('m=audio') !== -1) {
            mLineIndex = i;
            break;
        }
    }
    if (mLineIndex === null) {
        return sdp;
    }

    // If Opus is available, set it as the default in m line.
    for (i = 0; i < sdpLines.length; i++) {
        if (sdpLines[i].search('opus/48000') !== -1) {
            var opusPayload = extractSdp(sdpLines[i], /:(\d+) opus\/48000/i);
            if (opusPayload) {
                sdpLines[mLineIndex] = setDefaultCodec(sdpLines[mLineIndex],
                        opusPayload);
            }
            break;
        }
    }

    // Remove CN in m line and sdp.
    sdpLines = removeCN(sdpLines, mLineIndex);

    sdp = sdpLines.join('\r\n');
    return sdp;
}

function extractSdp(sdpLine, pattern) {
    var result = sdpLine.match(pattern);
    return result && result.length === 2 ? result[1] : null;
}

// Set the selected codec to the first in m line.
function setDefaultCodec(mLine, payload) {
    var elements = mLine.split(' ');
    var newLine = [];
    var index = 0;
    for (var i = 0; i < elements.length; i++) {
        if (index === 3) { // Format of media starts from the fourth.
            newLine[index++] = payload; // Put target payload to the first.
        }
        if (elements[i] !== payload) {
            newLine[index++] = elements[i];
        }
    }
    return newLine.join(' ');
}

// Strip CN from sdp before CN constraints is ready.
function removeCN(sdpLines, mLineIndex) {
    var mLineElements = sdpLines[mLineIndex].split(' ');
    // Scan from end for the convenience of removing an item.
    for (var i = sdpLines.length - 1; i >= 0; i--) {
        var payload = extractSdp(sdpLines[i], /a=rtpmap:(\d+) CN\/\d+/i);
        if (payload) {
            var cnPos = mLineElements.indexOf(payload);
            if (cnPos !== -1) {
                // Remove CN payload from m line.
                mLineElements.splice(cnPos, 1);
            }
            // Remove CN line in sdp
            sdpLines.splice(i, 1);
        }
    }

    sdpLines[mLineIndex] = mLineElements.join(' ');
    return sdpLines;
}
