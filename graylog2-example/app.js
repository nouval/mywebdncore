var graylog2 = require("graylog2");
var logger = new graylog2.graylog({
    servers: [
        { host: '192.168.99.100', port: 12201 }
    ],
    hostname: 'server.name', // the name of this host 
                             // (optional, default: os.hostname()) 
    facility: 'Node.js',     // the facility for these log messages 
                             // (optional, default: "Node.js") 
    bufferSize: 1350         // max UDP packet size, should never exceed the 
                             // MTU of your system (optional, default: 1400) 
});
 
logger.on('error', function (error) {
    console.error('Error while trying to write to graylog2:', error);
});

logger.log("What we've got here is...failure to communicate");

logger.log("What we've got here is...failure to communicate", "Some men you just can't reach. So you get what we had here last week, which is the way he wants it... well, he gets it. I don't like it any more than you men.");

logger.log("What we've got here is...failure to communicate", { cool: 'beans', messageId: '542de910-c586-449c-bdbd-9ed61e3b4d33' });

logger.error("What we've got here is...failure to communicate", { cool: 'beans', messageId: '542de910-c586-449c-bdbd-9ed61e3b4d33' });

const dgram = require('dgram');
// const server = dgram.createSocket('udp4');

// server.on('error', (err) => {
//   console.log(`server error:\n${err.stack}`);
//   server.close();
// });

// server.on('message', (msg, rinfo) => {
//   console.log(`server got: ${msg} from ${rinfo.address}:${rinfo.port}`);
// });

// server.on('listening', () => {
//   var address = server.address();
//   console.log(`server listening ${address.address}:${address.port}`);
// });

// server.bind(12345);

var message = new Buffer('My KungFu is Good!');
var PORT = 12201;
var HOST = '192.168.99.100';
var client = dgram.createSocket('udp4');
// client.send(message, 0, message.length, PORT, HOST, function(err, bytes) {
//     if (err) throw err;
//     console.log('UDP message sent to ' + HOST +':'+ PORT);
//     client.close();
// });