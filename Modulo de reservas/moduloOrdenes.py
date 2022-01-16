import pika
import os
import json

url = os.environ.get('CLOUDAMQP_URL', 'amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk')
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)

channel = connection.channel() 

channel.queue_declare(queue='Reserva')

msg = ''
def cargar_msg(ruta):
   with open(ruta) as contenido:
       global msg
       msg = json.load(contenido)

cargar_msg('msgOrdenes.json')

channel.basic_publish(exchange='', routing_key='Reserva', body=str(msg))

connection.close()
