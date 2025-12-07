from flask import Flask, request, jsonify
from llm_service import process_question

app = Flask(__name__)

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

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)