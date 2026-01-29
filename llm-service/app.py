from flask import Flask, request, jsonify, send_file
from llm_service import process_question
import mysql.connector
import os
from pdf import generate_contract_pdf, generate_employee_pdf
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)

def get_db_connection():
    return mysql.connector.connect(
        host=os.getenv('DB_HOST', 'localhost'),
        user=os.getenv('DB_USER', 'root'),
        password=os.getenv('DB_PASSWORD', ''),
        database=os.getenv('DB_NAME', 'gestion_entreprise')
    )

@app.route('/api/llm', methods=['POST'])
def llm_endpoint():
    """Point d'entrée pour les requêtes LLM."""
    data = request.get_json()
    if not data or 'question' not in data:
        return jsonify({"status": "error", "error": "Question manquante"}), 400

    question = data['question']
    employee_id = data.get('employee_id')
    user_role = data.get('user_role')
    user_service_id = data.get('user_service_id')

    print(f"Reçu : question={question}, employee_id={employee_id}, user_role={user_role}, user_service_id={user_service_id}")

    try:
        response = process_question(question, employee_id, user_role, user_service_id)
        return jsonify({"status": "success", "data": {"response": response}})
    except Exception as e:
        print(f"Erreur dans process_question: {e}")
        return jsonify({"status": "error", "error": str(e)}), 500

@app.route('/api/generate_pdf/<int:employee_id>', methods=['GET'])
def generate_pdf_endpoint(employee_id):
    """Génère un PDF pour le contrat d'un employé."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True, buffered=True)

        # Récupérer les données du contrat
        query = (
            "SELECT e.nom, e.prenom, e.email, e.telephone, p.titre as poste, s.nom as service, d.nom as departement, "
            "ct.debut as debut, ct.fin as fin, ct.salaire_base, ct.date_signature "
            "FROM employe e "
            "JOIN employe_statut es ON es.id_employe = e.id_employe AND es.activite = 1 "
            "JOIN poste p ON es.id_poste = p.id_poste "
            "JOIN service s ON p.id_service = s.id_service "
            "JOIN departement d ON s.id_dept = d.id_dept "
            "LEFT JOIN contrat_travail ct ON e.id_employe = ct.id_employe "
            "WHERE e.id_employe = %s "
            "ORDER BY ct.date_signature DESC "
            "LIMIT 1"
        )
        cursor.execute(query, (employee_id,))
        contract_data = cursor.fetchone()

        if not contract_data:
            return jsonify({"status": "error", "error": "Employé ou contrat non trouvé"}), 404

        # Générer le PDF
        filename = f"contract_{employee_id}.pdf"
        filepath = os.path.join(os.getcwd(), filename)
        generate_contract_pdf(contract_data, filepath)

        # Retourner le PDF
        return send_file(filepath, as_attachment=True, download_name=filename)

    except Exception as e:
        print(f"Erreur lors de la génération du PDF: {e}")
        return jsonify({"status": "error", "error": str(e)}), 500
    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals():
            conn.close()

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)