import pika
import os
import ast
import db 
import json

url = os.environ.get('CLOUDAMQP_URL', 'amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk')
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel() 

#channel.queue_declare(queue='Reserva')


def callback(ch, method, properties, bodyMsg):
    print('\n [x] Received :%r' % bodyMsg)    
    msg = json.loads(bodyMsg)
    print("Actualizando el stock de articulos en la bd...")

    database = db.DataBase()
    #actualizar las cantidades en la BD de los productos solicitados
    for articulo in msg['detalles']:
        cantActual = database.seleccionarCantidad(articulo['cod_articulo'])
        database.actualizarCantidad(articulo['cod_articulo'], cantActual-articulo['cantidad'])
    database.close()

    #crear la cola Factura y enviar el mensaje a la cola
    #channel.queue_declare(queue='Factura')
    channel.basic_publish(exchange='exchange_procesosnegocio', routing_key='key_cola_facturacion', body=bodyMsg)
    print("Enviado a modulo de FACTURACIÓN...!")

channel.basic_consume(queue='cola_reserva', on_message_callback=callback, auto_ack=True)

print(' [*] Waiting for messages. To exit press ctrl + c')
channel.start_consuming()
