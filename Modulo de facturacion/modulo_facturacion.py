import pika,os, sys,json
from utilities import *
import mysql.connector

def get_database_connection():
    """
    Obtenemos la conexion con el servidor mysql
    """
    AWS_URL_DATABASE="procesosnegociodatabase.cuxsffuy95k9.us-east-1.rds.amazonaws.com"
    mydb = mysql.connector.connect(
        host=AWS_URL_DATABASE,
        user="admin",
        password="admin12345678",
        database="procesosnegociodatabase"
    )
    return mydb


def generar_factura(data):
    print("[+] Generando JSON Factura")
    data_factura=data.copy()
    mydb=get_database_connection()
    mycursor=mydb.cursor()
    #obtenemos el id de la factura
    sql="SELECT MAX(cod_factura) FROM Factura"
    mycursor.execute(sql)
    myresult=mycursor.fetchone()
    #mycursor.close()

    data_factura['cod_factura']=myresult[0]+1 if myresult[0] else 1
    #obtenemos los codigo de detalle, subtotal de cada producto.
    suma_subtotal,total_igv=0,0
    for cod_detalle,producto in enumerate(data['detalles']):
        data_factura['detalles'][cod_detalle]['cod_detalle']=cod_detalle+1
        data_factura['detalles'][cod_detalle]['subtotal']=int(producto['cantidad'])*float(producto['precio_unitario'])
        #obtenemos el  subtotal
        suma_subtotal+=int(producto['cantidad'])*float(producto['precio_unitario'])
        total_igv+=int(producto['cantidad'])*float(producto['precio_unitario'])*0.18
    data_factura['total_igv']=total_igv
    data_factura['total_factura']=suma_subtotal+total_igv

    print("[+] Generando Documento Factura [pdf]")
    data_factura=get_documento_factura(data_factura)
    return data_factura

def registrar_factura(data):
    print("[+] Registrando Factura en la base de datos")
    mydb=get_database_connection()
    mycursor=mydb.cursor()

    #registramos los datos de la factura
    sql="INSERT INTO Factura(cod_cliente,nombre_cliente,ruc_cliente,total_igv,total_factura) VALUES(%s,%s,%s,%s,%s)"
    val=(data['cod_cliente'],data['nombre_cliente'],data['ruc_cliente'],data['total_igv'],data['total_factura'])
    mycursor.execute(sql,val)
    mydb.commit()
    #print(mycursor.rowcount,"Factura registrada .")
    #INSERTAMOS LOS DETALLES
    lista_detalles=[]
    for detalle in data['detalles']:
        lista_detalles.append((data['cod_factura'],detalle['cod_detalle'],detalle['cod_articulo'],detalle['nombre_articulo'],detalle['cantidad'],detalle['precio_unitario'],detalle['subtotal']))
    #Una vez obtenido la lista procedemos a insertarlas en la base de datos
    sql="INSERT INTO DetalleFactura(cod_factura,cod_detalle,cod_articulo,descripcion,cantidad,precio_unitario,subtotal) VALUES(%s,%s,%s,%s,%s,%s,%s)"
    mycursor.executemany(sql,lista_detalles)
    mydb.commit()
    #print(mycursor.rowcount,"Detalles Factura registrados .")
    mydb.close()

def enviar_mensaje(chanel,data):
    """
    Se le envia un mensaje al modulo de cuentas por cobrar.
    El modulo de cuentas por cobrar
    """
    chanel.basic_publish(exchange='exchange_procesosnegocio',routing_key='key_cola_cuentasporcobrar',body=json.dumps(data))
    print("[+] Mensaje enviado al modulo de CUENTAS POR COBRAR")


def main():
    # Conectar al servidor RabbitQM
    AMQP_URL="amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk"
    params=pika.URLParameters(AMQP_URL)
    #Establecemos la conexion
    connection=pika.BlockingConnection(params)
    #creamos el canal
    channel=connection.channel()
    #Declaramos la cola de laque se recibe el mensaje
    channel.queue_declare(queue='cola_reserva',durable=True)
    #podemos declarar el exchange pero esta vez usaremos uno predeterminado ''
    #channel.exchange_declare(exchange='', exchange_type='direct')
    
    #estamos listos para recibir un mensaje del exchange 'exchange_procesonegocio' y cola cola_facturacion
    def callback(ch,method,properties,body):
        
        #desempaqueta el mensaje para extraer los datos
        data=json.loads(body)
        #alteracion
        #data=adaptar_json_porque_jeffrey_no_quiere(data)
        #genera la factura
        data_factura=generar_factura(data)
        #registramos la factura en la base de datos
        registrar_factura(data_factura)
        #confirmamos el mensaje  al modulo de cuentas por cobrar
        enviar_mensaje(ch,data_factura)

        print("[*] Modulo de facturacion Esperando mensajes de Modulo de Reserva. CTRL+C para salir")


    channel.basic_consume(queue='cola_facturacion',on_message_callback=callback,auto_ack=True)
    print("[*] Modulo de facturacion Esperando mensajes de Modulo de Reserva. CTRL+C para salir")
    channel.start_consuming()
    connection.close()


if __name__=="__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("Saliendo del programa")
        sys.exit(0)