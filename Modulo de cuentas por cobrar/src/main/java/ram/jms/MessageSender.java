package ram.jms;

import com.rabbitmq.client.Channel;
import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;
import java.util.Scanner;

/*
 * This class is used to send a text message to the queue.
 */
public class MessageSender
{

	private final static String QUEUE_NAME = "cola_cuentasporcobrar";

	public static void main(String[] argv) throws Exception
	{
		Scanner in = new Scanner(System.in);
		ConnectionFactory factory = new ConnectionFactory();

		/*
		 * Here we connect to a broker on the local machine - hence
		 * the localhost. If we wanted to connect to a broker on a
		 * different machine we'd simply specify its name or IP
		 * address here.
		 */
		factory.setHost("tiger.rmq.cloudamqp.com");
		factory.setUsername("apfwqrdk");
		factory.setPassword("QfWRMKJpECkqHzz43MdFveLcQG3_YWFX");
		factory.setVirtualHost("apfwqrdk");
		try (
				Connection connection = factory.newConnection();
				Channel channel = connection.createChannel())

		{
			channel.queueDeclare(QUEUE_NAME, true, false, false,
					null);
			//String message = "La fecha de hoy es: ";
			String message = "{\"cod_factura\": 1, \"cod_cliente\": 1, \"nombre_cliente\": \"Juan Perezz\", \"ruc_cliente\": \"123456789\", \"detalles\": [{\"cod_detalle\": 1, \"cod_articulo\": 1, \"nombre_articulo\": \"Leche\", \"cantidad\": 2, \"precio_unitario\": 10, \"subtotal\": 20}, {\"cod_detalle\": 2, \"cod_articulo\": 3, \"nombre_articulo\": \"Pan\", \"cantidad\": 1, \"precio_unitario\": 5, \"subtotal\": 5}], \"total_igv\": 15.6, \"total_factura\": 55.6, \"documento_factura\": \"KNUjnekjvew=\"}";
			//String message = in.nextLine();
			channel.basicPublish("", QUEUE_NAME, null,
					message.getBytes("UTF-8"));
			System.out.println(" [x] Sent '" + message + "'");
		}
		catch (Exception exe)
		{
			exe.printStackTrace();
		}
	}
}
