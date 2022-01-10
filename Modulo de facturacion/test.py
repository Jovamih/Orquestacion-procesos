import pika,json,sys

# Conectar al servidor RabbitQM
#AMQP_URL = "amqps://mhvisocb:EQqCdVh3E4ZyUbeH6pRH2chTz4nGYKDe@tiger.rmq.cloudamqp.com/mhvisocb"
AMQP_URL="amqps://apfwqrdk:QfWRMKJpECkqHzz43MdFveLcQG3_YWFX@tiger.rmq.cloudamqp.com/apfwqrdk"
params=pika.URLParameters(AMQP_URL)
#Establecemos la conexion
connection=pika.BlockingConnection(params)
#creamos el canal
channel=connection.channel()

def send():
    with open("data.json","r") as f:
        content=json.load(f)
    #print(content)
    channel.basic_publish(exchange="exchange_procesosnegocio",routing_key="key_cola_procesamientoordenes",body=json.dumps(content))
    print(f"[+] Mensaje enviado a la cola de facturacion")
    connection.close()
def callback(ch,method,properties,body):
    print("[+] Recibido mensaje:")
    print(body)

def receive():
    channel.basic_consume(queue="cola_cuentasporcobrar",on_message_callback=callback,auto_ack=True)
    channel.start_consuming()
    print("[*] Esperando mensajes de la cola de Reserva. CTRL+C para salir")
    connection.close()

if __name__=="__main__":

    if len(sys.argv)>1:
        if sys.argv[1]=="send":
            send()
        elif sys.argv[1]=="receive":
            try:
                receive()
            except KeyboardInterrupt:
                print("[*] Saliendo del modulo")
                connection.close()
    else:
        print("[*] Modulo de Facturacion")
        print("[*] Ejecute: python3 test.py send")
        print("[*] Ejecute: python3 test.py receive")
