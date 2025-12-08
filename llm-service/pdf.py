from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.lib import colors
from reportlab.lib.units import inch
import os

def generate_contract_pdf(contract_data, filename="contract.pdf"):
    """Génère un PDF pour un contrat de travail."""
    doc = SimpleDocTemplate(filename, pagesize=letter, rightMargin=72, leftMargin=72, topMargin=72, bottomMargin=72)
    styles = getSampleStyleSheet()

    # Styles personnalisés
    title_style = ParagraphStyle(
        'Title',
        parent=styles['Heading1'],
        fontSize=22,
        alignment=1,  # Centré
        spaceAfter=30,
        spaceBefore=30,
        fontName='Helvetica-Bold'
    )
    normal_style = ParagraphStyle(
        'Normal',
        parent=styles['Normal'],
        fontSize=11,
        spaceAfter=8,
        leading=16
    )
    normal_style_no_space = ParagraphStyle(
        'NormalNoSpace',
        parent=styles['Normal'],
        fontSize=11,
        spaceAfter=0,
        leading=16
    )
    bold_style = ParagraphStyle(
        'Bold',
        parent=styles['Normal'],
        fontSize=11,
        spaceAfter=0,
        leading=16,
        fontName='Helvetica-Bold'
    )
    signature_style = ParagraphStyle(
        'Signature',
        parent=styles['Normal'],
        fontSize=11,
        alignment=1,
        spaceBefore=40
    )

    story = []

    # Titre centré
    story.append(Paragraph("Contrat de Travail (CDI)", title_style))
    story.append(Spacer(1, 24))

    # Bloc "Entre les soussignés"
    story.append(Paragraph("Entre les soussignés :", normal_style))
    story.append(Spacer(1, 8))

    # Bloc Employeur (statique)
    story.append(Paragraph("1. L'Employeur : Mazer Enterprise", normal_style_no_space))
    story.append(Paragraph("Antananarivo, Madagascar", normal_style_no_space))
    story.append(Paragraph("Représenté par Rakoto Lita Mamy, Directeur Général", normal_style))

    # Bloc Salarié (dynamique)
    story.append(Spacer(1, 8))
    story.append(Paragraph(f"2. Le Salarié : {contract_data.get('nom', 'N/A')} {contract_data.get('prenom', '')}", normal_style_no_space))
    # Email et téléphone si présents
    email = contract_data.get('email', None)
    tel = contract_data.get('telephone', None)
    if email:
        story.append(Paragraph(f"Email : {email}", normal_style_no_space))
    if tel:
        story.append(Paragraph(f"Téléphone : {tel}", normal_style_no_space))

    story.append(Spacer(1, 8))

    # Article 1 : Poste occupé
    story.append(Paragraph("<b>Article 1 : Poste occupé</b>", bold_style))
    story.append(Paragraph(f"Le salarié occupe le poste de {contract_data.get('poste', 'N/A')}.", normal_style))

    # Article 2 : Durée du contrat
    story.append(Paragraph("<b>Article 2 : Durée du contrat</b>", bold_style))
    debut = contract_data.get('debut', 'N/A')
    story.append(Paragraph(f"Ce contrat est conclu pour une durée indéterminée et débute le {debut}.", normal_style))

    # Article 3 : Rémunération
    story.append(Paragraph("<b>Article 3 : Rémunération</b>", bold_style))
    salaire = contract_data.get('salaire_base', 'N/A')
    story.append(Paragraph(f"La rémunération mensuelle brute est fixée à {salaire} Ariary.", normal_style))

    # Espace avant signatures
    story.append(Spacer(1, 60))

    # Signatures alignées gauche/droite
    from reportlab.platypus import Table, TableStyle
    sign_table = Table([
        [Paragraph("Signature employeur", normal_style), "", Paragraph("Signature salarié", normal_style)]
    ], colWidths=[200, 100, 200])
    sign_table.setStyle(TableStyle([
        ('ALIGN', (0, 0), (0, 0), 'LEFT'),
        ('ALIGN', (2, 0), (2, 0), 'RIGHT'),
        ('VALIGN', (0, 0), (-1, -1), 'BOTTOM'),
        ('TOPPADDING', (0, 0), (-1, -1), 30),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
    ]))
    story.append(sign_table)

    doc.build(story)
    return filename

def generate_employee_pdf(employee_data, filename="employee.pdf"):
    """Génère un PDF pour les informations d'un employé."""
    doc = SimpleDocTemplate(filename, pagesize=letter)
    styles = getSampleStyleSheet()

    title_style = ParagraphStyle(
        'Title',
        parent=styles['Heading1'],
        fontSize=18,
        spaceAfter=30,
        alignment=1
    )
    normal_style = styles['Normal']
    bold_style = styles['Heading2']

    story = []

    story.append(Paragraph("Fiche Employé", title_style))
    story.append(Spacer(1, 12))

    story.append(Paragraph("Informations Personnelles", bold_style))
    info = [
        ["Nom :", employee_data.get('nom', 'N/A')],
        ["Prénom :", employee_data.get('prenom', 'N/A')],
        ["Genre :", employee_data.get('genre', 'N/A')],
        ["Date de Naissance :", str(employee_data.get('date_naissance', 'N/A'))],
        ["Poste :", employee_data.get('poste', 'N/A')],
        ["Service :", employee_data.get('service', 'N/A')],
        ["Département :", employee_data.get('departement', 'N/A')]
    ]
    table = Table(info, colWidths=[2*inch, 4*inch])
    table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.lightblue),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
        ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
        ('GRID', (0, 0), (-1, -1), 1, colors.black)
    ]))
    story.append(table)

    doc.build(story)
    return filename

# Exemple d'utilisation
if __name__ == "__main__":
    # Exemple de données
    contract_example = {
        'nom': 'Dupont',
        'prenom': 'Alice',
        'poste': 'Développeur Backend',
        'service': 'Informatique',
        'departement': 'Informatique',
        'debut': '2020-01-01',
        'fin': None,
        'salaire_base': 1500000,
        'date_signature': '2020-01-01'
    }
    generate_contract_pdf(contract_example, "example_contract.pdf")
    print("PDF généré : example_contract.pdf")