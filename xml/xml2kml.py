import xml.etree.ElementTree as ET

def parse_circuit_xml(xml_path, kml_output):
    # Namespace del esquema
    ns = {'u': 'http://www.uniovi.es'}
    
    # Parsear XML
    tree = ET.parse(xml_path)
    root = tree.getroot()
    
    # Obtener nombre del circuito (XPath absoluto relativo al root)
    nombre_elem = root.find('.//u:nombre', ns)
    nombre = nombre_elem.text if nombre_elem is not None else "Circuito sin nombre"
    
    # Lista de coordenadas (lon, lat)
    coordenadas = []

    # XPath para la coordenada del origen
    origen = root.find('.//u:origen/u:coordenada', ns)
    if origen is not None:
        lat = origen.find('./u:latitud', ns).text
        lon = origen.find('./u:longitud', ns).text
        coordenadas.append((lon, lat))

    # XPath para todas las coordenadas de tramos
    for coord in root.findall('.//u:tramos/u:tramo/u:coordenada', ns):
        lat = coord.find('./u:latitud', ns).text
        lon = coord.find('./u:longitud', ns).text
        coordenadas.append((lon, lat))
    
    # Crear contenido KML
    kml_header = f"""<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
    <name>{nombre}</name>
    <Placemark>
        <name>Recorrido del circuito</name>
        <Style>
            <LineStyle>
                <color>ff0000ff</color> <!-- rojo -->
                <width>3</width>
            </LineStyle>
        </Style>
        <LineString>
            <tessellate>1</tessellate>
            <altitudeMode>clampToGround</altitudeMode>
            <coordinates>
"""

    kml_footer = """            </coordinates>
        </LineString>
    </Placemark>
</Document>
</kml>"""

    # Añadir coordenadas en formato lon,lat,0
    kml_coords = ""
    for lon, lat in coordenadas:
        kml_coords += f"                {lon},{lat},0\n"

    # Escribir archivo KML
    with open(kml_output, "w", encoding="utf-8") as f:
        f.write(kml_header)
        f.write(kml_coords)
        f.write(kml_footer)
    
    print(f"KML generado correctamente: {kml_output}")


# Ejemplo de uso
if __name__ == "__main__":
    parse_circuit_xml("circuitoEsquema.xml", "circuito.kml")

