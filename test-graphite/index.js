var graphite = require('graphite');
var client = graphite.createClient('plaintext:ec2-54-82-247-73.compute-1.amazonaws.com:2003/');

var metrics = {foo: { bar : 10} };
client.write(metrics, function(err) {
  // if err is null, your data was sent to graphite!
  console.log(err);
});