package ram.jms;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

import org.json.JSONArray;
import org.json.JSONObject;

public class JDBCConector {

    static final String DB_URL = "jdbc:mysql://procesosnegociodatabase.cuxsffuy95k9.us-east-1.rds.amazonaws.com";
    static final String USER = "admin";
    static final String PASS = "admin12345678";

    public void insertar (JSONObject recibido, JSONArray detalles) {
        // Open a connection
        try(Connection conn = DriverManager.getConnection(DB_URL, USER, PASS);
            Statement stmt = conn.createStatement();
        ) {
            //Desglosamos el json
            int cod_cliente = recibido.getInt("cod_cliente");
            int cod_factura = recibido.getInt("cod_factura");
            String estado_registro = recibido.getString("estado_registro");
            String fecha_cobro = recibido.getString("fecha_cobro");
            String nombre_cliente = recibido.getString("nombre_cliente");
            String ruc_cliente = recibido.getString("ruc_cliente");
            double total_cobrar = recibido.getDouble("total_factura");
            double total_igv = recibido.getDouble("total_igv");

            // Execute a query para Cuentas por cobrar
            System.out.println("Insertando registros a la tabla...");
            //String sql = "INSERT INTO CuentasPorCobrar VALUES (100, 'Zara', 'Ali', 18)";
            String sql = "INSERT INTO procesosnegociodatabase.CuentasPorCobrar (cod_factura, cod_cliente, nombre_cliente, ruc_cliente, total_igv, total_cobrar, fecha_cobro, estado_registro)" +
                    "VALUES ('"+cod_factura+"','"+cod_cliente+"','"+nombre_cliente+"','"+ruc_cliente+"','"+total_igv+"','"+total_cobrar+"','"+fecha_cobro+"','"+estado_registro+"')";
            stmt.executeUpdate(sql);
            System.out.println("Registros insertados en la tabla Cuentas por cobrar...");

            System.out.println("ahora hay " + detalles.length() + " detalles.");

            // Execute a query para Detalles cuentas por cobrar
            for(int i = 0; i < detalles.length(); i++) {
                // the JSON data
                JSONObject articulo = detalles.getJSONObject(i);
                // Capturamos el detalle de cada articulo.
                int cantidad = articulo.getInt("cantidad");
                int cod_articulo = articulo.getInt("cod_articulo");
                int cod_detalle = articulo.getInt("cod_detalle");
                //int cod_factura =  El codigo factura ya fue definido
                String descripcion = articulo.getString("nombre_articulo");
                double precio_unit = articulo.getDouble("precio_unitario");
                double subtotal = articulo.getDouble("subtotal");

                // Lo guardamos en la bd
                String sql2 = "INSERT INTO procesosnegociodatabase.DetalleCuentasPorCobrar (cod_factura, cod_detalle, cod_articulo, descripcion, cantidad, precio_unitario, subtotal)" +
                        "VALUES ('"+cod_factura+"','"+cod_detalle+"','"+cod_articulo+"','"+descripcion+"','"+cantidad+"','"+precio_unit+"','"+subtotal+"')";
                stmt.executeUpdate(sql2);
                System.out.println("Registros insertados en la tabla Detalle de Cuentas por cobrar...");
                // Listo calixto Tanq gozu
            }

            //Referencia: https://www.tutorialspoint.com/jdbc/jdbc-insert-records.htm
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

}
