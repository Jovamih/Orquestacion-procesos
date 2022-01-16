import pika
import os
import ast
import db 

url = os.environ.get('CLOUDAMQP_URL', 'amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk')
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel() 

channel.queue_declare(queue='Reserva')

def callback(ch, method, properties, bodyMsg):
    print('\n [x] Received :%r' % bodyMsg)
    msg = ast.literal_eval(str(bodyMsg)[2:-1])

    database = db.DataBase()
    #actualizar las cantidades en la BD de los productos solicitados
    for articulo in msg['lista_articulos']:
        cantActual = database.seleccionarCantidad(articulo['cod_articulo'])
        database.actualizarCantidad(articulo['cod_articulo'], cantActual-articulo['cantidad_solicitada'])
    database.close()

    #crear la cola Factura y enviar el mensaje a la cola
    channel.queue_declare(queue='Factura')
    channel.basic_publish(exchange='', routing_key='Factura', body=bodyMsg)

channel.basic_consume(queue='Reserva', on_message_callback=callback, auto_ack=True)

#print(' [*] Waiting for messages. To exit press ctrl + c')
channel.start_consuming()
