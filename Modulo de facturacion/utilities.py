import datetime
import random
from decimal import Decimal
import os,base64

import typing
from borb.pdf.canvas.color.color import HexColor, X11Color
from borb.pdf.canvas.layout.image.image import Image
from borb.pdf.canvas.layout.layout_element import Alignment
from borb.pdf.canvas.layout.page_layout.multi_column_layout import SingleColumnLayout
from borb.pdf.canvas.layout.page_layout.page_layout import PageLayout
from borb.pdf.canvas.layout.table.fixed_column_width_table import FixedColumnWidthTable
from borb.pdf.canvas.layout.table.table import Table, TableCell
from borb.pdf.canvas.layout.text.paragraph import Paragraph
from borb.pdf.document import Document
from borb.pdf.page.page import Page
from borb.pdf.pdf import PDF


def _build_invoice_information(data) -> Table:
    """
    This function builds a Table containing invoice information
    :return:    a Table containing invoice information
    """
    table_001 = FixedColumnWidthTable(number_of_rows=3, number_of_columns=3)

    table_001.add(Paragraph(f"RUC: {data['ruc_cliente']}"))
    table_001.add(
        Paragraph("Date: ", font="Helvetica-Bold", horizontal_alignment=Alignment.RIGHT)
    )
    now = datetime.datetime.now()
    table_001.add(Paragraph("%d/%d/%d" % (now.day, now.month, now.year)))

    table_001.add(Paragraph(data['nombre_cliente']))
    table_001.add(
        Paragraph(
            "N° Factura: ", font="Helvetica-Bold", horizontal_alignment=Alignment.RIGHT
        )
    )
    table_001.add(Paragraph(f"00{data['cod_factura']}"))

    table_001.add(Paragraph("sistemas@sac.com"))
    table_001.add(
        Paragraph(
            "Due Date", font="Helvetica-Bold", horizontal_alignment=Alignment.RIGHT
        )
    )
    table_001.add(Paragraph("%d/%d/%d" % (now.day, now.month, now.year)))



    #table_001.add(Paragraph("sistemas.distribuidos.com SAC"))
  

    table_001.set_padding_on_all_cells(Decimal(2), Decimal(2), Decimal(2), Decimal(2))
    table_001.no_borders()
    return table_001






def _build_itemized_description_table(data):
    """
    This function builds a Table containing itemized billing information
    :param:     products
    :return:    a Table containing itemized billing information
    """
    products=data['detalles']

    table_001 = FixedColumnWidthTable(number_of_rows=len(products)+4, number_of_columns=4)
    for h in ["Descripcion", "Cantidad", "Precio  Unitario", "Subtotal"]:
        table_001.add(
            TableCell(
                Paragraph(h, font_color=X11Color("White")),
                background_color=HexColor("0b3954"),
            )
        )

    odd_color = HexColor("BBBBBB")
    even_color = HexColor("FFFFFF")
    for row_number, item in enumerate(products):
        
        c = even_color if row_number % 2 == 0 else odd_color
        table_001.add(TableCell(Paragraph(item['nombre_articulo']), background_color=c))
        table_001.add(TableCell(Paragraph(str(item['cantidad'])), background_color=c))
        table_001.add(
            TableCell(Paragraph("$ " + str(item['precio_unitario'])), background_color=c)
        )
        table_001.add(
            TableCell(
                Paragraph("$ " + str(item['subtotal'])),
                background_color=c,
            )
        )

    # Optionally add some empty rows to have a fixed number of rows for styling purposes
    """for row_number in range(len(products), 10):
        c = even_color if row_number % 2 == 0 else odd_color
        for _ in range(0, 4):
            table_001.add(TableCell(Paragraph(" "), background_color=c))"""

    # subtotal
    subtotal: float = sum([x['subtotal'] for x in products])
    table_001.add(
        TableCell(
            Paragraph(
                "Subtotal",
                font="Helvetica-Bold",
                horizontal_alignment=Alignment.RIGHT,
            ),
            col_span=3,
        )
    )
    table_001.add(
        TableCell(Paragraph(f"$ {subtotal:0.2f}", horizontal_alignment=Alignment.RIGHT))
    )

    # discounts
    total_igv=data['total_igv']
    table_001.add(
        TableCell(
            Paragraph(
                "Total IGV",
                font="Helvetica-Bold",
                horizontal_alignment=Alignment.RIGHT,
            ),
            col_span=3,
        )
    )
    table_001.add(TableCell(Paragraph(f"$ {total_igv:0.2f}", horizontal_alignment=Alignment.RIGHT)))


    # total
    total_factura=data['total_factura']
    table_001.add(
        TableCell(
            Paragraph(
                "Total", font="Helvetica-Bold", horizontal_alignment=Alignment.RIGHT
            ),
            col_span=3,
        )
    )
    table_001.add(
        TableCell(Paragraph("$ " + str(total_factura), horizontal_alignment=Alignment.RIGHT))
    )
    table_001.set_padding_on_all_cells(Decimal(2), Decimal(2), Decimal(2), Decimal(2))
    table_001.no_borders()
    return table_001


def get_documento_factura(data):

    doc: Document = Document()
    page: Page = Page(width=350)
    doc.append_page(page)

    # set PageLayout
    page_layout: PageLayout = SingleColumnLayout(
        page, vertical_margin=page.get_page_info().get_height() * Decimal(0.02)
    )
    # add corporate logo
    page_layout.add(
        Image(
            "https://image.flaticon.com/icons/png/512/107/107831.png",
            width=Decimal(64),
            height=Decimal(64),
        )
    )

    # Invoice information table
    page_layout.add(_build_invoice_information(data))

    # Itemized description
    page_layout.add(
        _build_itemized_description_table(
            data
        )
    )

    # store
    with open("output.pdf", "wb") as out_file_handle:
        PDF.dumps(out_file_handle, doc)
    #os.system("output.pdf")
    file_bytes=open("output.pdf", "rb").read()
    data['documento_factura']=base64.b64encode(file_bytes).decode("utf-8")
    return data


def adaptar_json_porque_jeffrey_no_quiere(data):
    #print(data.keys())
    data_mod={"detalles":[]}
    data_mod['cod_cliente']=data['cod_cliente']
    data_mod['nombre_cliente']=data['nombre_cliente']
    data_mod['ruc_cliente']=data['ruc_cliente']
    for productos in data['detalles']:
        data_mod['detalles'].append({
            'cod_articulo':productos['ID'],
            'nombre_articulo':productos['NOMBRE'],
            'cantidad':productos['CANTIDAD'],
            'precio_unitario':productos['PRECIO']
        })
    return data_mod