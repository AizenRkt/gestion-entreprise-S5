<?php
if (isset($_GET['mssg'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Toastify({
                text: '" . addslashes($_GET['mssg']) . "',
                duration: 4000,
                gravity: 'top',
                position: 'right',
                backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                close: true
            }).showToast();
        });
    </script>";
    unset($_GET['mssg']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant LLM RH</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --chat-bg: #f7f7f8;
            --user-bubble: #2563eb;
            --assistant-bubble: #ffffff;
            --border-color: #e5e7eb;
            --text-secondary: #6b7280;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background: var(--chat-bg);
        }

        /* Layout principal */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow);
            z-index: 10;
        }

        .chat-header h1 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #111827;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            background: var(--chat-bg);
        }

        /* Messages area */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: var(--chat-bg);
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Message wrapper */
        .message-wrapper {
            max-width: 800px;
            margin: 0 auto 24px;
            display: flex;
            gap: 16px;
            animation: fadeSlideIn 0.3s ease;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-wrapper.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 600;
            font-size: 14px;
        }

        .message-wrapper.user .message-avatar {
            background: var(--user-bubble);
            color: white;
        }

        .message-wrapper.assistant .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message-content {
            flex: 1;
            max-width: 100%;
        }

        .message-bubble {
            padding: 16px 20px;
            border-radius: 16px;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .message-wrapper.user .message-bubble {
            background: var(--user-bubble);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-wrapper.assistant .message-bubble {
            background: var(--assistant-bubble);
            color: #111827;
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            gap: 6px;
            padding: 16px 20px;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingBounce {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-8px);
                opacity: 1;
            }
        }

        /* Input area */
        .chat-input-area {
            padding: 20px 24px;
            background: white;
            border-top: 1px solid var(--border-color);
            box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
        }

        .input-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }

        .chat-textarea {
            width: 100%;
            min-height: 52px;
            max-height: 200px;
            padding: 14px 56px 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            resize: none;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
            background: var(--chat-bg);
        }

        .chat-textarea:focus {
            border-color: var(--user-bubble);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .chat-textarea::placeholder {
            color: var(--text-secondary);
        }

        .send-button {
            position: absolute;
            right: 8px;
            bottom: 8px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: var(--user-bubble);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .send-button:hover:not(:disabled) {
            background: #1d4ed8;
            transform: scale(1.05);
        }

        .send-button:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
        }

        .empty-state h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #111827;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 32px;
        }

        .suggestion-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            max-width: 700px;
            margin: 0 auto;
        }

        .suggestion-card {
            padding: 16px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            text-align: left;
        }

        .suggestion-card:hover {
            border-color: var(--user-bubble);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .suggestion-card-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-messages {
                padding: 16px;
            }

            .message-wrapper {
                margin-bottom: 16px;
            }

            .chat-header h1 {
                font-size: 16px;
            }

            .suggestion-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <h1>
                <i class="bi bi-robot me-2"></i>
                Assistant RH
            </h1>
            <div class="header-actions">
                <button class="btn-icon" onclick="clearChat()" title="Nouvelle conversation">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn-icon" onclick="toggleMenu()" title="Menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="chatMessages">
            <?php if (empty($chatHistory) || !is_array($chatHistory)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h2>Comment puis-je vous aider ?</h2>
                    <p>Posez-moi vos questions sur les ressources humaines</p>
                    
                    <div class="suggestion-cards">
                        <div class="suggestion-card" onclick="fillQuestion('Comment calculer les congés payés ?')">
                            <p class="suggestion-card-title">Comment calculer les congés payés ?</p>
                        </div>
                        <div class="suggestion-card" onclick="fillQuestion('Quelles sont les étapes de recrutement ?')">
                            <p class="suggestion-card-title">Quelles sont les étapes de recrutement ?</p>
                        </div>
                        <div class="suggestion-card" onclick="fillQuestion('Politique de télétravail')">
                            <p class="suggestion-card-title">Politique de télétravail</p>
                        </div>
                        <div class="suggestion-card" onclick="fillQuestion('Procédure d\'évaluation annuelle')">
                            <p class="suggestion-card-title">Procédure d'évaluation annuelle</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Chat History -->
                <?php foreach ($chatHistory as $item): ?>
                    <?php if (!empty($item['question'])): ?>
                        <div class="message-wrapper user">
                            <div class="message-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-bubble">
                                    <?php echo nl2br(htmlspecialchars($item['question'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['response'])): ?>
                        <div class="message-wrapper assistant">
                            <div class="message-avatar">
                                <i class="bi bi-robot"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-bubble">
                                    <?php
                                    if (is_array($item['response'])) {
                                        echo '<pre>' . htmlspecialchars(print_r($item['response'], true)) . '</pre>';
                                    } else {
                                        echo nl2br(htmlspecialchars($item['response']));
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Typing Indicator -->
            <div class="message-wrapper assistant" id="typingIndicator" style="display: none;">
                <div class="message-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <form id="chatForm" class="input-container">
                <textarea 
                    class="chat-textarea" 
                    id="question" 
                    name="question" 
                    rows="1"
                    placeholder="Posez votre question..."
                    required
                ><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                <button type="submit" class="send-button" id="sendButton">
                    <i class="bi bi-arrow-up"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

    <script>
        const chatForm = document.getElementById('chatForm');
        const chatMessages = document.getElementById('chatMessages');
        const typingIndicator = document.getElementById('typingIndicator');
        const textarea = document.getElementById('question');
        const sendButton = document.getElementById('sendButton');

        // Auto-resize textarea
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
            
            // Enable/disable send button
            sendButton.disabled = !this.value.trim();
        });

        // Submit on Enter (Shift+Enter for new line)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim()) {
                    chatForm.dispatchEvent(new Event('submit'));
                }
            }
        });

        function scrollToBottom() {
            setTimeout(() => {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
        }

        function addUserMessage(message) {
            const wrapper = document.createElement('div');
            wrapper.className = 'message-wrapper user';
            wrapper.innerHTML = `
                <div class="message-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;
            
            // Remove empty state if exists
            const emptyState = chatMessages.querySelector('.empty-state');
            if (emptyState) emptyState.remove();
            
            chatMessages.appendChild(wrapper);
            scrollToBottom();
        }

        function addAssistantMessage(message) {
            const wrapper = document.createElement('div');
            wrapper.className = 'message-wrapper assistant';
            wrapper.innerHTML = `
                <div class="message-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;
            chatMessages.appendChild(wrapper);
            scrollToBottom();
        }

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = textarea.value.trim();
            if (!message) return;

            // Disable input
            textarea.disabled = true;
            sendButton.disabled = true;

            // Add user message
            addUserMessage(message);

            // Clear input
            textarea.value = '';
            textarea.style.height = 'auto';

            // Show typing indicator
            typingIndicator.style.display = 'flex';
            scrollToBottom();

            try {
                // Send request
                const response = await fetch("<?= Flight::base() ?>/llm/ask", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ question: message })
                });

                const data = await response.json();

                // Hide typing indicator
                typingIndicator.style.display = 'none';

                // Add assistant response
                addAssistantMessage(data.response || 'Désolé, je n\'ai pas pu générer une réponse.');

            } catch (error) {
                typingIndicator.style.display = 'none';
                addAssistantMessage('Une erreur est survenue. Veuillez réessayer.');
                console.error('Error:', error);
            } finally {
                // Re-enable input
                textarea.disabled = false;
                textarea.focus();
            }
        });

        function fillQuestion(question) {
            textarea.value = question;
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
        }

        function clearChat() {
            if (confirm('Voulez-vous vraiment commencer une nouvelle conversation ?')) {
                window.location.href = "<?= Flight::base() ?>/llm";
            }
        }

        function toggleMenu() {
            // Add your menu toggle logic here
            alert('Menu functionality to be implemented');
        }

        // Initial scroll
        window.addEventListener('load', scrollToBottom);
        
        // Focus textarea on load
        textarea.focus();
    </script>
</body>
</html>