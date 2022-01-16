import pymysql

class DataBase:
    def __init__(self):
        self.connection = pymysql.connect(
            host='procesosnegociodatabase.cuxsffuy95k9.us-east-1.rds.amazonaws.com',
            user='admin',
            password='admin12345678',
            db='procesosnegociodatabase'
        )
        self.cursor = self.connection.cursor()
        print('Conexion establecida')

    def seleccionarCantidad(self, codigo):
        sql = 'SELECT cantidad FROM Articulo WHERE cod_articulo={}'.format(codigo)
        cantidad = -1
        try:
            self.cursor.execute(sql)
            item = self.cursor.fetchone()
            cantidad = item[0]
        except Exception as e:
            raise 
        return cantidad
    
    def actualizarCantidad(self, codigo, cantidad):
        sql = 'UPDATE Articulo SET cantidad={} where cod_articulo={}'.format(cantidad,codigo)
        try:
            self.cursor.execute(sql)
            self.connection.commit()
        except Exception as e:
            raise 
    
    def close(self):
        self.connection.close()
