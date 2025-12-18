#!/usr/bin/env python3
# xml2html.py
# Genera InfoCircuito.html desde circuitoEsquema.xml cumpliendo restricciones de etiquetas

import xml.etree.ElementTree as ET
from html import escape
from datetime import datetime
import sys
import os

XML_FILE = "circuitoEsquema.xml"
OUT_FILE = "InfoCircuito.html"
CSS_PATH = "estilo/estilo.css"

class Html:
    """Clase para generar HTML semántico sin ids ni clases."""
    def __init__(self, lang="es"):
        self.lang = lang
        self.head_parts = []
        self.body_parts = []

    def add_head(self, title):
        self.head_parts.append('<meta charset="utf-8">')
        self.head_parts.append('<meta name="viewport" content="width=device-width, initial-scale=1">')
        self.head_parts.append(f'<title>{escape(title)}</title>')
        self.head_parts.append(f'<link rel="stylesheet" href="{escape(CSS_PATH)}">')
        self.head_parts.append('<meta name="description" content="Información del circuito (InfoCircuito)">')

    def add_header(self, title, subtitle=None):
        subtitle_html = f'<p>{escape(subtitle)}</p>' if subtitle else ''
        self.body_parts.append(f'<h2>{escape(title)}</h2>\n  {subtitle_html}')

    def add_section(self, title, content_html):
        self.body_parts.append(f'<section>\n  <h3>{escape(title)}</h3>\n  {content_html}\n</section>')

    def add_footer(self, text):
        self.body_parts.append(f'<footer>\n  <p>{escape(text)}</p>\n</footer>')

    def render(self):
        head_html = "\n  ".join(self.head_parts)
        body_html = "\n  ".join(self.body_parts)
        return (
            f'<!doctype html>\n'
            f'<html lang="{escape(self.lang)}">\n'
            f'<head>\n  {head_html}\n</head>\n'
            f'<body>\n  {body_html}\n</body>\n'
            f'</html>'
        )

def detect_namespace(root):
    if root.tag.startswith("{"):
        uri = root.tag.split("}")[0].strip("{")
        return {"ns": uri}
    return {}

def find_text(root, xpath, ns):
    el = root.find(xpath, ns) if ns else root.find(xpath)
    return el.text.strip() if (el is not None and el.text) else ''

def find_all(root, xpath, ns):
    return root.findall(xpath, ns) if ns else root.findall(xpath)

def format_datetime(date_text, time_text):
    try:
        date_str = datetime.fromisoformat(date_text).strftime("%d %b %Y") if date_text else ''
    except Exception:
        date_str = date_text
    try:
        time_str = datetime.fromisoformat(time_text).strftime("%H:%M:%S") if time_text else ''
    except Exception:
        time_str = time_text
    return date_str, time_str

def main():
    if not os.path.exists(XML_FILE):
        print(f"Error: no se encuentra '{XML_FILE}'", file=sys.stderr)
        sys.exit(1)

    tree = ET.parse(XML_FILE)
    root = tree.getroot()
    ns = detect_namespace(root)
    pref = 'ns:' if ns else ''

    # Datos básicos
    nombre = find_text(root, f'.//{pref}nombre', ns)
    longitud_el = root.find(f'.//{pref}longitudCircuito', ns) if ns else root.find('.//longitudCircuito')
    longitud_text = (longitud_el.text or '').strip() if longitud_el is not None else ''
    longitud_unidad = longitud_el.get('unidad', '') if longitud_el is not None else ''

    anchura_el = root.find(f'.//{pref}anchuraMedia', ns) if ns else root.find('.//anchuraMedia')
    anchura_text = (anchura_el.text or '').strip() if anchura_el is not None else ''
    anchura_unidad = anchura_el.get('unidad', '') if anchura_el is not None else ''

    fecha_text = find_text(root, f'.//{pref}fechaCarrera', ns)
    hora_text = find_text(root, f'.//{pref}hora', ns)
    fecha_str, hora_str = format_datetime(fecha_text, hora_text)

    num_vueltas = find_text(root, f'.//{pref}numVueltas', ns)
    loc_prox = find_text(root, f'.//{pref}locProx', ns)
    pais = find_text(root, f'.//{pref}pais', ns)
    patrocinador = find_text(root, f'.//{pref}nombrePatrocinador', ns)

    referencias = [r.text.strip() for r in find_all(root, f'.//{pref}referencias/{pref}referencia', ns) if r.text]

    origen_el = root.find(f'.//{pref}origen/{pref}coordenada', ns) if ns else root.find('.//origen/coordenada')
    if origen_el is not None:
        origen = {
            'latitud': float(origen_el.findtext(f'{pref}latitud', default='0', namespaces=ns).strip()),
            'longitud': float(origen_el.findtext(f'{pref}longitud', default='0', namespaces=ns).strip()),
            'altitud': float(origen_el.findtext(f'{pref}altitud', default='0', namespaces=ns).strip()),
        }
    else:
        origen = None

    tramos = []
    tramos_els = root.findall(f'.//{pref}tramos/{pref}tramo', ns) if ns else root.findall('.//tramos/tramo')

    for tramo_el in tramos_els:
        coord_el = tramo_el.find(f'{pref}coordenada', ns) if ns else tramo_el.find('coordenada')
        distancia_el = tramo_el.find(f'{pref}distancia', ns) if ns else tramo_el.find('distancia')
        sector_el = tramo_el.find(f'{pref}sector', ns) if ns else tramo_el.find('sector')

        # Coordenadas
        tramo = {}
        if coord_el is not None:
            tramo['latitud'] = float(coord_el.findtext(f'{pref}latitud', default='0', namespaces=ns).strip())
            tramo['longitud'] = float(coord_el.findtext(f'{pref}longitud', default='0', namespaces=ns).strip())
            tramo['altitud'] = float(coord_el.findtext(f'{pref}altitud', default='0', namespaces=ns).strip())

        # Distancia
        if distancia_el is not None:
            tramo['distancia'] = float((distancia_el.text or '0').strip())
            tramo['unidad_distancia'] = distancia_el.get('unidad', '')
        else:
            tramo['distancia'] = None
            tramo['unidad_distancia'] = ''

        # Sector
        tramo['sector'] = int((sector_el.text or '0').strip()) if sector_el is not None else None

        tramos.append(tramo)

    fotos = []
    for f in find_all(root, f'.//{pref}galeriaFoto/{pref}foto', ns):
        url = f.find(f'{pref}url', ns).text.strip() if f.find(f'{pref}url', ns) is not None and f.find(f'{pref}url', ns).text else ''
        desc = f.find(f'{pref}description', ns).text.strip() if f.find(f'{pref}description', ns) is not None and f.find(f'{pref}description', ns).text else ''
        fotos.append({'url': url, 'desc': desc})

    videos = []
    for v in find_all(root, f'.//{pref}galeriaVideo/{pref}video', ns):
        url = v.find(f'{pref}url', ns).text.strip() if v.find(f'{pref}url', ns) is not None and v.find(f'{pref}url', ns).text else ''
        desc = v.find(f'{pref}description', ns).text.strip() if v.find(f'{pref}description', ns) is not None and v.find(f'{pref}description', ns).text else ''
        videos.append({'url': url, 'desc': desc})

    vencedor_el = root.find(f'.//{pref}vencedor', ns)
    ganador_nombre = ''
    ganador_tiempo = ''
    if vencedor_el is not None:
        np_el = vencedor_el.find(f'{pref}nombrePiloto', ns) if ns else vencedor_el.find('nombrePiloto')
        t_el = vencedor_el.find(f'{pref}tiempo', ns) if ns else vencedor_el.find('tiempo')
        ganador_nombre = (np_el.text or '').strip() if np_el is not None else ''
        ganador_tiempo = (t_el.text or '').strip() if t_el is not None else ''

    clasificados = [c.text.strip() for c in find_all(root, f'.//{pref}clasificados/{pref}nombrePiloto', ns) if c.text]

    # Generar HTML
    html = Html(lang="es")
    html.add_head(f"Info: {nombre or 'Circuito'}")
    html.add_header(nombre or 'Circuito')

    # Sección Datos básicos
    basic_items = []
    if longitud_text:
        basic_items.append(f'<li>Longitud: {escape(longitud_text)} {escape(longitud_unidad)}</li>')
    if anchura_text:
        basic_items.append(f'<li>Anchura media: {escape(anchura_text)} {escape(anchura_unidad)}</li>')
    if fecha_str or hora_str:
        basic_items.append(f'<li>Fecha/Hora: {escape(fecha_str)} {escape(hora_str)}</li>')
    if num_vueltas:
        basic_items.append(f'<li>Número de vueltas: {escape(num_vueltas)}</li>')
    if loc_prox:
        basic_items.append(f'<li>Localidad próxima: {escape(loc_prox)}</li>')
    if pais:
        basic_items.append(f'<li>País: {escape(pais)}</li>')
    if patrocinador:
        basic_items.append(f'<li>Patrocinador: {escape(patrocinador)}</li>')
    html.add_section('Datos básicos', '<ul>\n' + '\n'.join(basic_items) + '\n</ul>')

    # Sección Referencias
    if referencias:
        refs_html = '<ol>\n' + '\n'.join(f'<li><a href="{escape(r)}">{escape(r)}</a></li>' for r in referencias) + '\n</ol>'
    else:
        refs_html = '<p>No hay referencias.</p>'
    html.add_section('Referencias', refs_html)

    # Sección Galería Fotos
    if fotos:
        pics_html = ''
        for f in fotos:
            alt_text = f['desc'] or 'Foto del circuito'
            pics_html += (
                f'  <img src="/MotoGPDesktop/multimedia/imagenes/{escape(f["url"])}" alt="{escape(alt_text)}" loading="lazy">\n'
            )
    else:
        pics_html = '<p>No hay fotos.</p>'
    html.add_section('Galería fotos', pics_html)

    # Sección Galería Vídeos
    if videos:
        vids_html = ''
        for v in videos:
            vids_html += (
                f'  <video controls preload="auto">\n'
                f'    <source src="/MotoGPDesktop/multimedia/videos/{escape(v["url"])}" type="video/mp4">\n'
                f'    {escape(v["desc"])}\n'
                f'  </video>\n'
            )
    else:
        vids_html = '<p>No hay vídeos.</p>'
    html.add_section('Galería vídeos', vids_html)

    # Sección Resultados
    resultados_html = ''
    if ganador_nombre or ganador_tiempo:
        resultados_html += f'<p>Vencedor: {escape(ganador_nombre)} | Tiempo: {escape(ganador_tiempo)}</p>'
    if clasificados:
        resultados_html += '<ol>\n' + '\n'.join(f'<li>{escape(c)}</li>' for c in clasificados) + '\n</ol>'
    html.add_section('Resultados', resultados_html or '<p>No hay resultados.</p>')

    origen_html = ''
    if origen:
        origen_html += (
            f'<ul>\n'
            f'  <li>Latitud: {escape(str(origen["latitud"]))}</li>\n'
            f'  <li>Longitud: {escape(str(origen["longitud"]))}</li>\n'
            f'  <li>Altitud: {escape(str(origen["altitud"]))} m</li>\n'
            f'</ul>\n'
        )

    tramos_html = ''
    tramo_count = 1
    if tramos:
        for tramo in tramos:
            tramos_html += f'<h4>Tramo {tramo_count}:</h4>\n'
            tramos_html += (
                f'<ul>\n'
                f'  <li>Latitud: {escape(str(tramo["latitud"]))}</li>\n'
                f'  <li>Longitud: {escape(str(tramo["longitud"]))}</li>\n'
                f'  <li>Altitud: {escape(str(tramo["altitud"]))} m</li>\n'
                f'  <li>Distancia: {escape(str(tramo["distancia"]))} m</li>\n'
                f'  <li>Sector: {escape(str(tramo["sector"]))}</li>\n'
                f'</ul>\n'
            )
            tramo_count += 1
        
    html.add_section('Origen', origen_html or '<p>No hay datos de origen.</p>')

    html.add_section(f'Tramos ({tramo_count-1})', tramos_html or '<p>No hay datos de tramos.</p>')
    #html.add_section('Nota', '<p>Los datos de <em>origen</em> y <em>tramos</em> han sido excluidos.</p>')
    #html.add_footer('Archivo generado automáticamente por xml2html.py - Proyecto MotoGP Desktop')

    with open(OUT_FILE, 'w', encoding='utf-8') as f:
        f.write(html.render())

    print(f"Generado: {OUT_FILE}")

if __name__ == "__main__":
    main()
