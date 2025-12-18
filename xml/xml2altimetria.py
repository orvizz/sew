import xml.etree.ElementTree as ET


def detect_namespace(root):
    """Detecta namespace si el tag tiene formato {uri}localname"""
    if root.tag.startswith("{"):
        uri = root.tag.split("}")[0].strip("{")
        return {"u": uri}, "u:"
    else:
        return {}, ""


def get_altitudes_distances(xml_path):
    """
    Devuelve dos listas:
    - altitudes: alturas en metros
    - distances: distancias acumuladas en metros
    """
    tree = ET.parse(xml_path)
    root = tree.getroot()
    ns, pfx = detect_namespace(root)

    altitudes = []
    distances = []

    # Altitud y distancia del origen
    origen_coord = root.find(f".//{pfx}origen/{pfx}coordenada", ns)
    if origen_coord is not None:
        alt_elem = origen_coord.find(f"./{pfx}altitud", ns)
        altitudes.append(float(alt_elem.text.strip()) if alt_elem is not None else 0.0)
        distances.append(0.0)  # origen siempre empieza en 0

    # Tramos
    acum_dist = 0.0
    tramo_nodes = root.findall(f".//{pfx}tramos/{pfx}tramo", ns)
    for tramo in tramo_nodes:
        # Altitud
        coord = tramo.find(f"./{pfx}coordenada", ns)
        alt_elem = coord.find(f"./{pfx}altitud", ns) if coord is not None else None
        alt = float(alt_elem.text.strip()) if alt_elem is not None else 0.0
        altitudes.append(alt)

        # Distancia del tramo
        dist_elem = tramo.find(f"./{pfx}distancia", ns)
        if dist_elem is not None and dist_elem.text.strip():
            dist_val = float(dist_elem.text.strip())
            # Convertir a metros si unidad diferente
            unidad = dist_elem.attrib.get("unidad", "m").lower()
            if unidad in ["km", "kilometro", "kilómetro"]:
                dist_val *= 1000.0
            elif unidad in ["m", "metro", "metros"]:
                pass
            else:
                pass  # si unidad desconocida, asumir metros
        else:
            dist_val = 0.0
        acum_dist += dist_val
        distances.append(acum_dist)

    return altitudes, distances


def generate_svg_altimetry_simple(
    altitudes,
    distances,
    svg_path,
    width=1200,
    height=400,
    margin_left=20,
    margin_right=20,
    margin_top=20,
    margin_bottom=20,
):
    """
    Genera un SVG simple:
    - línea roja de altimetría
    - polígono cerrado por abajo
    - sin ejes ni título
    """
    if not altitudes or not distances or len(altitudes) != len(distances):
        raise ValueError("Listas de altitudes y distancias inválidas.")

    # Escala horizontal y vertical
    min_x, max_x = min(distances), max(distances)
    min_y, max_y = min(altitudes), max(altitudes)

    inner_w = width - margin_left - margin_right
    inner_h = height - margin_top - margin_bottom

    # Mapear distancias a X (0..inner_w)
    xs = [
        (
            margin_left + (d - min_x) / (max_x - min_x) * inner_w
            if max_x != min_x
            else width / 2
        )
        for d in distances
    ]
    # Mapear altitudes a Y (SVG: 0 arriba, height abajo)
    ys = [
        (
            margin_top + (max_y - a) / (max_y - min_y) * inner_h
            if max_y != min_y
            else height / 2
        )
        for a in altitudes
    ]

    # Polyline de la curva
    polyline_points = " ".join(f"{x:.2f},{y:.2f}" for x, y in zip(xs, ys))

    # Polígono cerrado por abajo (baseline)
    baseline_y = margin_top + inner_h
    poly_closed_points = [f"{xs[0]:.2f},{baseline_y:.2f}"]
    poly_closed_points += [f"{x:.2f},{y:.2f}" for x, y in zip(xs, ys)]
    poly_closed_points.append(f"{xs[-1]:.2f},{baseline_y:.2f}")
    poly_closed_attr = " ".join(poly_closed_points)

    # Crear SVG
    svg = []
    svg.append(f'<?xml version="1.0" encoding="utf-8"?>')
    svg.append(
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{width}" height="{height}" viewBox="0 0 {width} {height}">'
    )
    svg.append(f'<rect x="0" y="0" width="{width}" height="{height}" fill="white"/>')
    # Polígono bajo la curva (opacidad baja)
    svg.append(
        f'<polygon points="{poly_closed_attr}" fill="rgba(255,0,0,0.1)" stroke="none"/>'
    )
    # Línea roja
    svg.append(
        f'<polyline points="{polyline_points}" fill="none" stroke="red" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>'
    )
    svg.append("</svg>")

    with open(svg_path, "w", encoding="utf-8") as f:
        f.write("\n".join(svg))

    print(f"SVG de altimetría generado: {svg_path}")


# ---------------------------------------------------------
# Flujo principal
# ---------------------------------------------------------
if __name__ == "__main__":
    xml_input = "circuitoEsquema.xml"
    svg_output = "altimetria.svg"

    altitudes, distances = get_altitudes_distances(xml_input)
    generate_svg_altimetry_simple(altitudes, distances, svg_output)
