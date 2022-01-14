const amqplib = require('amqplib');
const Broker = require('rascal').BrokerAsPromised;
const config = require('./config.json');

var amqp_url = process.env.CLOUDAMQP_URL || 'amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk';

//this code is for conenct to db
const mysql = require('mysql2');
require('dotenv').config();
module.exports.stablishedConnection = ()=>{
return new Promise((resolve,reject)=>{
  const con = mysql.createConnection( {
    host: process.env.DB_HOST||"procesosnegociodatabase.cuxsffuy95k9.us-east-1.rds.amazonaws.com",
    user: process.env.DB_USER_NAME||"admin",
    password: process.env.DB_PASSWORD||"admin12345678",
    database: process.env.DB_NAME||"procesosnegociodatabase"
  });
  con.connect((err) => {
    if(err){
      reject(err);
    }
    console.log("conectado");
    resolve(con);
  });
  
})
}
module.exports.closeDbConnection =(con)=> {
  con.destroy();
}
async function rascal_produce(){
    console.log("rascal: Publishing");
    var msg = 'Hello rascal World!';
    const broker = await Broker.create(config);
    broker.on('error', console.error);
    const publication = await broker.publish('demo_publication', msg);
    publication.on('error', console.error);
    console.log("rascal: Published")
}

async function produce(){
    console.log("amqplib: Publishing");
    var conn = await amqplib.connect(amqp_url, "heartbeat=60");
    var ch = await conn.createChannel()
    var exch = 'test_exchange';
    var q = 'test_queue';
    var rkey = 'test_route';
    var msg = 'Hello amqplib World!';
    await ch.assertExchange(exch, 'direct', {durable: true}).catch(console.error);
    await ch.assertQueue(q, {durable: true});
    await ch.bindQueue(q, exch, rkey);
    await ch.publish(exch, rkey, Buffer.from(msg));
    setTimeout( function()  {
        ch.close();
        conn.close();},  500 );
}

async function consume() {
    var conn = await amqplib.connect(amqp_url, "heartbeat=60");
    var ch = await conn.createChannel()
    var q = 'test_queue';
    await conn.createChannel();
    await ch.assertQueue(q, {durable: true});
    await ch.consume(q, function (msg) {
        console.log('amqplib: consumed message: ' + msg.content.toString());
        ch.ack(msg);
        ch.cancel('myconsumer');
        }, {consumerTag: 'myconsumer'});
    setTimeout( function()  {
        ch.close();
        conn.close();},  500 );
}

async function rascal_consume(){
    console.log("rascal: Consuming");
    const broker = await Broker.create(config);
    broker.on('error', console.error);
    const subscription = await broker.subscribe('demo_subscription', 'b1');
    subscription.on('message', (message, content, ackOrNack)=>{
        console.log('rascal: consumed message: ' + content);
        ackOrNack();
        subscription.cancel();
    });
    subscription.on('error', console.error);
    subscription.on('invalid_content', (err, message, ackOrNack) =>{
      console.log('Failed to parse message');
    });
}

async function main() {
    await rascal_produce().catch(console.error);
    await rascal_consume().catch(console.error);
    await produce().catch(console.error);
    await consume().catch(console.error);
}

main();
